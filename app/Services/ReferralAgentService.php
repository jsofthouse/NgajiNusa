<?php

namespace App\Services;

use App\Models\ReferralAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ReferralAgentService
{
    private const COOKIE_NAME = 'referral_code';

    private const COOKIE_MINUTES = 30 * 24 * 60; // 30 hari

    /**
     * Validasi ?ref=KODE di request, simpan ke cookie kalau valid & aktif.
     * Kode invalid diabaikan begitu saja — tidak pernah disimpan.
     */
    public function captureFromRequest(Request $request): void
    {
        $kode = $request->query('ref');

        if (! $kode) {
            return;
        }

        $isValid = ReferralAgent::where('kode', $kode)
            ->where('status', ReferralAgent::STATUS_ACTIVE)
            ->exists();

        if ($isValid) {
            Cookie::queue(self::COOKIE_NAME, $kode, self::COOKIE_MINUTES);
        }
    }

    /**
     * Baca cookie referral_code lalu resolve ke ID referral agent (aktif).
     * Dipakai saat submit pendaftaran murid, supaya user tidak input manual.
     */
    public function resolveAgentIdFromCookie(Request $request): ?int
    {
        $kode = $request->cookie(self::COOKIE_NAME);

        if (! $kode) {
            return null;
        }

        return ReferralAgent::where('kode', $kode)
            ->where('status', ReferralAgent::STATUS_ACTIVE)
            ->value('id');
    }

    /**
     * Generate kode referral acak (huruf+angka) yang unik.
     * Sengaja acak, bukan dari nama, biar tidak gampang ketebak.
     */
    public function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (ReferralAgent::where('kode', $code)->exists());

        return $code;
    }
}
