<?php
// app/Models/VisitorLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = ['visit_date', 'path', 'ip_address', 'hit_count'];

    public static function scopeToday($query)
    {
        return $query->where('visit_date', now()->toDateString());
    }
}
