<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMuridRequest;
use App\Models\AdminSetting;
use App\Models\Murid;
use App\Services\ReferralAgentService;
use App\Services\TransaksiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MuridController extends Controller
{
    /**
     * Tampilkan halaman pendaftaran (GET /daftar). Kalau ada ?ref=KODE,
     * validasi & simpan ke cookie lewat ReferralAgentService.
     */
    public function create(Request $request, ReferralAgentService $referralAgentService): View
    {
        $referralAgentService->captureFromRequest($request);

        return view('pages.home');
    }

    public function store(StoreMuridRequest $request, ReferralAgentService $referralAgentService, TransaksiService $transaksiService)
    {
        $validated = $request->validated();

        // Normalisasi nomor WA ke format 62xxxx (biar konsisten di DB & buat link wa.me nanti)
        $validated['whatsapp'] = $this->normalizeWhatsapp($validated['whatsapp']);
        $validated['status'] = Murid::STATUS_DAFTAR;
        // Ambil referral agent dari cookie (kalau ada) — user tidak input manual
        $validated['referral_agent_id'] = $referralAgentService->resolveAgentIdFromCookie($request);

        $murid = Murid::create($validated);

        // Auto-generate transaksi (status awal Menunggu Pembayaran) — hanya utk pendaftaran
        // publik fase ini. Tambah murid manual dari admin belum dapat transaksi otomatis
        // (lihat docs/todo.md).
        $transaksiService->createFromMurid($murid);

        $waAdmin = AdminSetting::get('wa_admin_number');

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil disimpan.',
            'data' => [
                'id' => $murid->id,
                'nama' => $murid->nama,
                'paket' => $murid->paket,
                'level_belajar' => $murid->level_belajar,
            ],
            'wa_admin_number' => $waAdmin,
        ]);
    }

    /**
     * Ubah 08xx / +62xx jadi 62xx (format yang dipakai wa.me link)
     */
    private function normalizeWhatsapp(string $number): string
    {
        $number = preg_replace('/[^0-9+]/', '', $number);

        if (str_starts_with($number, '+62')) {
            return substr($number, 1);
        }

        if (str_starts_with($number, '0')) {
            return '62' . substr($number, 1);
        }

        return $number; // sudah 62xx
    }
}
