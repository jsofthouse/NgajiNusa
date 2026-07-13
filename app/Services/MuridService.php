<?php

namespace App\Services;

use App\Models\Murid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MuridService
{
    private const PER_PAGE = 10;

    /**
     * List murid utk admin: search (nama/email/whatsapp) + pagination.
     * Referral Agent di-eager load supaya tersedia (mis. utk modal detail) tanpa N+1.
     */
    public function paginate(?string $search): LengthAwarePaginator
    {
        return Murid::with('referralAgent')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('whatsapp', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * Tambah murid dari admin. Status otomatis Daftar, referral agent selalu kosong
     * (beda dari pendaftaran publik yang resolve referral dari cookie).
     */
    public function createMurid(array $data): Murid
    {
        $data['whatsapp'] = $this->normalizeWhatsapp($data['whatsapp']);
        $data['status'] = Murid::STATUS_DAFTAR;
        $data['referral_agent_id'] = null;

        return Murid::create($data);
    }

    /**
     * Update data murid dari admin. Status & referral agent tidak disentuh form ini.
     */
    public function updateMurid(Murid $murid, array $data): Murid
    {
        $data['whatsapp'] = $this->normalizeWhatsapp($data['whatsapp']);

        $murid->update($data);

        return $murid;
    }

    /**
     * Normalisasi nomor WA ke format 62xxxx — mekanisme identik dengan
     * MuridController::normalizeWhatsapp() di flow pendaftaran publik.
     */
    public function normalizeWhatsapp(string $number): string
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

    /**
     * Seluruh data murid utk export CSV — tanpa filter/search, sesuai requirement.
     */
    public function allForExport(): Collection
    {
        return Murid::query()->orderBy('nama')->get();
    }
}
