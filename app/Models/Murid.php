<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Murid extends Model
{
    use SoftDeletes;

    protected $table = 'murid';

    protected $fillable = [
        'nama',
        'email',
        'whatsapp',
        'level_belajar',
        'paket',
        'status',
        'referral_agent_id',
    ];

    public function referralAgent(): BelongsTo
    {
        return $this->belongsTo(ReferralAgent::class);
    }

    // Daftar valid, dipakai juga di Form Request validasi
    public const LEVEL_OPTIONS = [
        'Hijaiyah',
        'Iqra',
        'Tahsin',
        'Tajwid',
        'Hafalan',
    ];

    public const PAKET_OPTIONS = [
        'Basic',
        'Pro',
        'Premium',
        'Platinum',
    ];

    public const STATUS_DAFTAR = 'Daftar';
}
