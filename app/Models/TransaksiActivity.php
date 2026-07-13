<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiActivity extends Model
{
    protected $table = 'transaksi_activities';

    public $timestamps = false; // readonly, hanya created_at (di-set manual/default DB)

    protected $fillable = [
        'transaksi_id',
        'type',
        'description',
        'causer_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    // Daftar type yang dipakai saat ini — string bebas di DB, konstanta ini cuma
    // memudahkan konsistensi penulisan & gampang ditambah kalau ada aktivitas baru.
    public const TYPE_CREATED = 'created';

    public const TYPE_OPENED = 'opened';

    public const TYPE_NOTE_UPDATED = 'note_updated';

    public const TYPE_PROOF_UPLOADED = 'proof_uploaded';

    public const TYPE_VERIFIED = 'verified';

    public const TYPE_REJECTED = 'rejected';
}
