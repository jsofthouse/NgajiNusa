<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    protected $table = 'murid';

    protected $fillable = [
        'nama',
        'email',
        'whatsapp',
        'level_belajar',
        'paket',
        'status',
    ];

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
