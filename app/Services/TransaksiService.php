<?php

namespace App\Services;

use App\Models\Murid;
use App\Models\Transaksi;
use App\Models\TransaksiActivity;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransaksiService
{
    private const PER_PAGE = 10;

    /**
     * Mapping harga paket — sesuai angka yang sudah tertulis di landing page
     * (`pages/home.blade.php`). Belum ada tabel harga di backend, jadi nominal
     * transaksi otomatis diambil dari sini (dikonfirmasi owner, 2026-07-13).
     * Kalau nanti ada tabel harga sungguhan, cukup ganti sumber array ini.
     */
    private const PAKET_PRICES = [
        'Group' => 150_000,
        'Basic' => 300_000,
        'Pro' => 550_000,
        'Premium' => 800_000,
        'Platinum' => 1_200_000,
    ];

    /**
     * Dipanggil otomatis saat murid mendaftar (publik). Status awal selalu
     * Menunggu Pembayaran, metode default transfer manual (satu-satunya fase ini).
     */
    public function createFromMurid(Murid $murid): Transaksi
    {
        $transaksi = Transaksi::create([
            'murid_id' => $murid->id,
            'jenis' => Transaksi::JENIS_PENDAFTARAN_BARU,
            'paket' => $murid->paket,
            'nominal' => self::PAKET_PRICES[$murid->paket] ?? 0,
            'metode_pembayaran' => Transaksi::METODE_TRANSFER_MANUAL,
            'status' => Transaksi::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $this->logActivity($transaksi, TransaksiActivity::TYPE_CREATED, 'Transaksi dibuat otomatis saat murid mendaftar.');

        return $transaksi;
    }

    /**
     * List transaksi utk admin: filter status/paket/metode/tanggal + search
     * (invoice / nama murid / nomor WA) + pagination. Eager load murid (cegah N+1).
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Transaksi::with('murid')->latest();

        if (! empty($filters['status']) && $filters['status'] !== 'semua') {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['paket'])) {
            $query->where('paket', $filters['paket']);
        }

        if (! empty($filters['metode_pembayaran'])) {
            $query->where('metode_pembayaran', $filters['metode_pembayaran']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('murid', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%")
                            ->orWhere('whatsapp', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate(self::PER_PAGE)->withQueryString();
    }

    /**
     * Angka kartu ringkasan dashboard. Total Pendapatan Bulan Ini dihitung dari
     * `verified_at` (bulan uang benar-benar dikonfirmasi masuk), bukan tanggal daftar.
     */
    public function dashboardSummary(): array
    {
        return [
            'menunggu_pembayaran' => Transaksi::where('status', Transaksi::STATUS_MENUNGGU_PEMBAYARAN)->count(),
            'menunggu_verifikasi' => Transaksi::where('status', Transaksi::STATUS_MENUNGGU_VERIFIKASI)->count(),
            'berhasil' => Transaksi::where('status', Transaksi::STATUS_BERHASIL)->count(),
            'pendapatan_bulan_ini' => (int) Transaksi::where('status', Transaksi::STATUS_BERHASIL)
                ->whereNotNull('verified_at')
                ->whereMonth('verified_at', now()->month)
                ->whereYear('verified_at', now()->year)
                ->sum('nominal'),
        ];
    }

    /**
     * Jumlah transaksi per status — dipakai badge angka di tab filter. Selalu
     * hitung total keseluruhan (tidak terpengaruh filter lain yang sedang aktif).
     */
    public function statusCounts(): array
    {
        $counts = Transaksi::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'semua' => $counts->sum(),
            Transaksi::STATUS_MENUNGGU_PEMBAYARAN => $counts[Transaksi::STATUS_MENUNGGU_PEMBAYARAN] ?? 0,
            Transaksi::STATUS_MENUNGGU_VERIFIKASI => $counts[Transaksi::STATUS_MENUNGGU_VERIFIKASI] ?? 0,
            Transaksi::STATUS_BERHASIL => $counts[Transaksi::STATUS_BERHASIL] ?? 0,
            Transaksi::STATUS_DITOLAK => $counts[Transaksi::STATUS_DITOLAK] ?? 0,
        ];
    }

    /**
     * Tandai transaksi sudah dibuka admin (indikator "transaksi baru" hilang).
     * Cuma dicatat + di-log sekali (saat opened_at masih kosong).
     */
    public function markOpened(Transaksi $transaksi, ?User $admin): Transaksi
    {
        if ($transaksi->opened_at === null) {
            $transaksi->opened_at = now();
            $transaksi->save();

            $this->logActivity(
                $transaksi,
                TransaksiActivity::TYPE_OPENED,
                'Detail transaksi dibuka pertama kali' . ($admin ? " oleh {$admin->name}." : '.'),
                $admin?->id
            );
        }

        return $transaksi;
    }

    /**
     * Verifikasi pembayaran: simpan bukti transfer sbg dokumentasi, ubah status
     * jadi Berhasil, catat waktu & admin verifikator, dan aktifkan murid.
     */
    public function verifyPayment(Transaksi $transaksi, UploadedFile $buktiTransfer, ?string $catatan, User $admin): Transaksi
    {
        $meta = $this->storeBuktiTransfer($buktiTransfer);
        $statusSebelum = $transaksi->status;

        $transaksi->fill([
            ...$meta,
            'status' => Transaksi::STATUS_BERHASIL,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);

        $catatanBerubah = $catatan !== null && $catatan !== '' && $catatan !== $transaksi->getOriginal('catatan_internal');
        if ($catatan !== null && $catatan !== '') {
            $transaksi->catatan_internal = $catatan;
        }

        $transaksi->save();

        $this->logActivity(
            $transaksi,
            TransaksiActivity::TYPE_PROOF_UPLOADED,
            "Bukti transfer diunggah oleh {$admin->name}.",
            $admin->id
        );

        if ($catatanBerubah) {
            $this->logActivity($transaksi, TransaksiActivity::TYPE_NOTE_UPDATED, "Catatan internal diubah oleh {$admin->name}.", $admin->id);
        }

        $this->logActivity(
            $transaksi,
            TransaksiActivity::TYPE_VERIFIED,
            "Pembayaran diverifikasi oleh {$admin->name}.",
            $admin->id,
            ['status_from' => $statusSebelum, 'status_to' => Transaksi::STATUS_BERHASIL]
        );

        // Sesuai konfirmasi owner: murid otomatis Aktif setelah pembayaran diverifikasi.
        $transaksi->murid->update(['status' => Murid::STATUS_AKTIF]);

        return $transaksi->refresh();
    }

    /**
     * Tolak transaksi (mis. bukti transfer tidak valid). Tidak menghapus data apa pun,
     * cuma ubah status — bisa dibuka lagi & diverifikasi ulang kalau ternyata valid.
     */
    public function reject(Transaksi $transaksi, User $admin): Transaksi
    {
        $statusSebelum = $transaksi->status;

        $transaksi->update(['status' => Transaksi::STATUS_DITOLAK]);

        $this->logActivity(
            $transaksi,
            TransaksiActivity::TYPE_REJECTED,
            "Transaksi ditolak oleh {$admin->name}.",
            $admin->id,
            ['status_from' => $statusSebelum, 'status_to' => Transaksi::STATUS_DITOLAK]
        );

        return $transaksi;
    }

    /**
     * Update catatan internal admin — bisa diedit kapan saja dari Detail Transaksi,
     * terpisah dari alur verifikasi.
     */
    public function updateCatatan(Transaksi $transaksi, ?string $catatan, User $admin): Transaksi
    {
        if ($catatan === $transaksi->catatan_internal) {
            return $transaksi;
        }

        $transaksi->update(['catatan_internal' => $catatan]);

        $this->logActivity($transaksi, TransaksiActivity::TYPE_NOTE_UPDATED, "Catatan internal diubah oleh {$admin->name}.", $admin->id);

        return $transaksi;
    }

    /**
     * Simpan file bukti transfer ke disk 'local' (private, storage/app/private/...).
     * Nama file acak berbasis UUID — sengaja bukan nama murid/invoice.
     */
    private function storeBuktiTransfer(UploadedFile $file): array
    {
        $directory = 'payment-proofs/' . now()->format('Y') . '/' . now()->format('m');
        $extension = $file->getClientOriginalExtension() ?: $file->extension();
        $storedFilename = Str::uuid()->toString() . '.' . $extension;

        Storage::disk('local')->putFileAs($directory, $file, $storedFilename);

        return [
            'bukti_original_filename' => $file->getClientOriginalName(),
            'bukti_stored_filename' => $storedFilename,
            'bukti_mime_type' => $file->getMimeType(),
            'bukti_file_size' => $file->getSize(),
            'bukti_path' => $directory . '/' . $storedFilename,
        ];
    }

    /**
     * Helper pusat pencatatan histori aktivitas (audit trail) transaksi.
     * causerId null = aktivitas otomatis oleh sistem.
     */
    private function logActivity(Transaksi $transaksi, string $type, string $description, ?int $causerId = null, array $metadata = []): void
    {
        TransaksiActivity::create([
            'transaksi_id' => $transaksi->id,
            'type' => $type,
            'description' => $description,
            'causer_id' => $causerId,
            'metadata' => $metadata,
        ]);
    }
}
