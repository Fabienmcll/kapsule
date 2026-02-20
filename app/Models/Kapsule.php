<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kapsule extends Model
{
    protected $fillable = [
        'user_id', 
        'name', 
        'share_code' 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($kapsule) {
            if (! $kapsule->share_code) {
                $kapsule->share_code = strtoupper(Str::random(8));
            }
        });
    }
}