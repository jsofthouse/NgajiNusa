<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralAgent extends Model
{
    protected $table = 'referral_agents';

    protected $fillable = [
        'nama',
        'whatsapp',
        'kode',
        'status',
    ];

    public const STATUS_ACTIVE = 'Aktif';
    public const STATUS_INACTIVE = 'Nonaktif';

    public function murid(): HasMany
    {
        return $this->hasMany(Murid::class);
    }
}
