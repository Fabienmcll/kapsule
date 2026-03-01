<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Kapsule extends Model implements HasMedia
{
    use InteractsWithMedia;

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

    //Méthode pour récupérer les membres d'une kapsule AVEC les champs de la table pivot
    public function members()
{
    return $this->belongsToMany(User::class, 'kapsule_user')
        ->withPivot('accepted', 'is_pending')
        ;
}

    // Permet de générer automatiquement des miniatures
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('preview')
            ->width(368)
            ->height(232)
            ->sharpen(10);
    }
}
