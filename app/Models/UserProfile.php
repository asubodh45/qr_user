<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'qr_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (UserProfile $profile) {
            $profile->qr_token = (string) Str::uuid();
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(UserImage::class);
    }

    public function profileImage(): HasOne
    {
        return $this->hasOne(UserImage::class)->where('is_profile', true);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(QrScan::class);
    }
}
