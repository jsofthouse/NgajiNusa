<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
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
        'Group',
        'Basic',
        'Pro',
        'Premium',
        'Platinum',
        'Diamond',
    ];

    public const STATUS_DAFTAR = 'Daftar';

    // Ditambahkan bersamaan dengan modul Transaksi (2026-07-13, dikonfirmasi owner) —
    // auto-set saat TransaksiService::verifyPayment() berhasil. Status Murid lain
    // (Pending/Nonaktif/dst) masih belum didefinisikan, tunggu keputusan terpisah.
    public const STATUS_AKTIF = 'Aktif';
}
