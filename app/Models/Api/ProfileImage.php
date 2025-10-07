<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProfileImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'dating_profile_id',
        'path',
        'caption',
        'visibility',
        'is_primary',
        'sort_order',
    ];

    // tetszőleges: ha szeretnéd, hogy a JSON-ban mindig benne legyen
    protected $appends = ['url', 'is_redacted'];

    // hogy JSON-ban bool legyen
    protected $casts = [
        'is_primary'  => 'boolean',
        'is_redacted' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(DatingProfile::class, 'dating_profile_id');
    }

    public function shares()
    {
        return $this->hasMany(ProfileImageShare::class);
    }

    // <<< ÚJ: accessor az is_redacted-hez
    public function getIsRedactedAttribute(): bool
    {
        return (bool)($this->attributes['is_redacted'] ?? false);
    }

    // módosított URL accessor (redaktált esetben placeholder)
    public function getUrlAttribute(): ?string
    {
        if ($this->getIsRedactedAttribute()) {
            return asset('img/locked-placeholder.png');
        }
        if (empty($this->path)) {
            return null;
        }
        return url(Storage::url($this->path));
    }
}
