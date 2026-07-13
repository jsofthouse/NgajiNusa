<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'invoice_number',
        'murid_id',
        'jenis',
        'paket',
        'nominal',
        'metode_pembayaran',
        'status',
        'bukti_original_filename',
        'bukti_stored_filename',
        'bukti_mime_type',
        'bukti_file_size',
        'bukti_path',
        'catatan_internal',
        'opened_at',
        'verified_at',
        'verified_by',
        'gateway_provider',
        'gateway_transaction_id',
        'gateway_payload',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'bukti_file_size' => 'integer',
            'opened_at' => 'datetime',
            'verified_at' => 'datetime',
            'gateway_payload' => 'array',
        ];
    }

    public function murid(): BelongsTo
    {
        return $this->belongsTo(Murid::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TransaksiActivity::class)->latest('created_at');
    }

    // ===== Invoice number =====
    public const INVOICE_PREFIX = 'INV-';

    public const INVOICE_PADDING = 8;

    /**
     * Generate invoice_number otomatis setelah insert (butuh id), format INV-00000001.
     * Pakai saveQuietly() supaya tidak memicu event `created` lagi (hindari infinite loop).
     */
    protected static function booted(): void
    {
        static::created(function (Transaksi $transaksi) {
            if (blank($transaksi->invoice_number)) {
                $transaksi->invoice_number = self::INVOICE_PREFIX . str_pad((string) $transaksi->id, self::INVOICE_PADDING, '0', STR_PAD_LEFT);
                $transaksi->saveQuietly();
            }
        });
    }

    // ===== Jenis transaksi =====
    // Satu-satunya yang dipakai fase ini. Nilai lain (perpanjangan, upgrade_paket)
    // disiapkan sbg dokumentasi tapi BELUM diimplementasikan — jangan dipakai dulu.
    public const JENIS_PENDAFTARAN_BARU = 'pendaftaran_baru';

    public const JENIS_OPTIONS = [
        self::JENIS_PENDAFTARAN_BARU,
    ];

    // ===== Metode pembayaran =====
    // Satu-satunya yang dipakai fase ini. Nilai lain (midtrans, xendit, qris) disiapkan
    // sbg dokumentasi tapi BELUM diimplementasikan — jangan dipakai dulu.
    public const METODE_TRANSFER_MANUAL = 'transfer_manual';

    public const METODE_OPTIONS = [
        self::METODE_TRANSFER_MANUAL,
    ];

    public const METODE_LABELS = [
        self::METODE_TRANSFER_MANUAL => 'Transfer Bank (Manual)',
    ];

    // ===== Status transaksi =====
    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';

    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    public const STATUS_BERHASIL = 'berhasil';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_OPTIONS = [
        self::STATUS_MENUNGGU_PEMBAYARAN,
        self::STATUS_MENUNGGU_VERIFIKASI,
        self::STATUS_BERHASIL,
        self::STATUS_DITOLAK,
    ];

    public const STATUS_LABELS = [
        self::STATUS_MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
        self::STATUS_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
        self::STATUS_BERHASIL => 'Berhasil',
        self::STATUS_DITOLAK => 'Ditolak',
    ];
}
