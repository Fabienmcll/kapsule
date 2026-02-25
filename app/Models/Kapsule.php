<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kapsule extends Model
{
    protected $fillable = [
        'user_id', 
        'name', 
        'share_code',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($kapsule) {
           if (! $kapsule->share_code) {
                $part1 = strtoupper(Str::random(3)); 
                $part2 = strtoupper(Str::random(3)); 
                $part3 = Str::password(2, letters: false, symbols: false, numbers: true);
                $kapsule->share_code = "{$part1}-{$part2}-{$part3}";
            }
        });
    }

    public function members() {
        return $this->belongsToMany(User::class, 'kapsule_user');
    }
}