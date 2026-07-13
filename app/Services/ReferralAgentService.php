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

    // Nama query param di URL referral (mis. domain.com/?share_via=KODE).
    // Ditaruh sebagai konstanta biar kalau nanti mau ganti (?via=, dll) cukup ubah di satu tempat.
    // Business logic-nya tetap "Referral" — ini cuma nama parameter yang tampil di URL.
    public const QUERY_PARAM = 'share_via';

    /**
     * Validasi ?share_via=KODE di request, simpan ke cookie kalau valid & aktif.
     * Kode invalid diabaikan begitu saja — tidak pernah disimpan.
     */
    public function captureFromRequest(Request $request): void
    {
        $kode = $request->query(self::QUERY_PARAM);

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
     * Generate kode referral acak (hu