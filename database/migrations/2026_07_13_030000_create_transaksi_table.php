<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel transaksi terpisah dari `murid` (sengaja, sesuai requirement: jangan simpan
     * info pembayaran di tabel murid). Struktur dibuat future-proof supaya nanti gampang
     * diintegrasikan ke payment gateway (Midtrans/Xendit/dll) tanpa perlu perubahan skema besar:
     * - `jenis` & `metode_pembayaran` disiapkan sebagai string enum (bukan DB enum) supaya
     *   nilai baru (perpanjangan, upgrade_paket, midtrans, xendit, qris, dst) bisa ditambah
     *   tanpa migration baru.
     * - `gateway_provider`/`gateway_transaction_id`/`gateway_payload` disiapkan tapi belum
     *   dipakai sama sekali di fase ini (murni placeholder future-proof).
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            // Diisi otomatis setelah insert (format INV-00000001, lihat Transaksi::booted()).
            $table->string('invoice_number', 20)->nullable()->unique();

            $table->foreignId('murid_id')->constrained('murid')->cascadeOnDelete();

            // Jenis transaksi: pendaftaran_baru (satu-satunya yang dipakai fase ini).
            // Nilai lain menyusul: perpanjangan, upgrade_paket — belum diimplementasikan.
            $table->string('jenis', 30)->default('pendaftaran_baru');

            // Snapshot nama paket saat transaksi dibuat — sengaja disalin (bukan relasi),
            // supaya histori transaksi tidak berubah kalau paket murid berubah di kemudian hari.
            $table->string('paket', 50);

            // Rupiah, tanpa desimal.
            $table->unsignedBigInteger('nominal');

            // Metode pembayaran: transfer_manual (satu-satunya yang dipakai fase ini).
            // Nilai lain menyusul: midtrans, xendit, qris, dst — belum diimplementasikan.
            $table->string('metode_pembayaran', 30)->default('transfer_manual');

            // Status: menunggu_pembayaran (default) / menunggu_verifikasi / berhasil / ditolak.
            $table->string('status', 30)->default('menunggu_pembayaran');

            // ===== Bukti transfer (diisi admin saat verifikasi) =====
            $table->string('bukti_original_filename')->nullable();
            $table->string('bukti_stored_filename')->nullable();
            $table->string('bukti_mime_type', 100)->nullable();
            $table->unsignedBigInteger('bukti_file_size')->nullable();
            $table->string('bukti_path')->nullable();

            // Catatan internal admin — tidak pernah terlihat murid, beda dari histori aktivitas.
            $table->text('catatan_internal')->nullable();

            // Kapan admin pertama kali membuka Detail Transaksi (dipakai utk indikator "transaksi baru").
            $table->timestamp('opened_at')->nullable();

            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // ===== Future-proof: payment gateway, belum dipakai fase ini =====
            $table->string('gateway_provider', 30)->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_payload')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('murid_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
