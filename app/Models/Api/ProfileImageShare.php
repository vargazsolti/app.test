<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileImageShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_image_id',
        'shared_with_user_id',
    ];

    public function image()
    {
        return $this->belongsTo(ProfileImage::class, 'profile_image_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'shared_with_user_id');
    }
}
