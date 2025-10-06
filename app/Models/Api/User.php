<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    /**
     * Tábla neve
     */
    protected $table = 'users';

    /**
     * Tömegesen tölthető mezők
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * Rejtett mezők JSON-ban
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Típus konverziók
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
