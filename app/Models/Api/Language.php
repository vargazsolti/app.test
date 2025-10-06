<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    public function profiles()
    {
        return $this->belongsToMany(DatingProfile::class, 'dating_profile_language')
            ->withTimestamps();
    }
}
