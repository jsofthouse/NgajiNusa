<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMuridRequest;
use App\Models\AdminSetting;
use App\Models\Murid;

class MuridController extends Controller
{
    public function store(StoreMuridRequest $request)
    {
        $validated = $request->validated();

        // Normalisasi nomor WA ke format 62xxxx (biar konsisten di DB & buat link wa.me nanti)
        $validated['whatsapp'] = $this->normalizeWhatsapp($validated['whatsapp']);
        $validated['status'] = Murid::STATUS_DAFTAR;

        $murid = Murid::create($validated);

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
