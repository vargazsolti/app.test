<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nickname',
        'height_cm',
        'weight_kg',
        'body_type',
        'hair_color',
        'sexual_orientation',
        'marital_status',
        'education_level',
        'occupation',
        'country',
        'state',
        'city',
        'registration_purpose',
    ];

    // Opcionális: "enum" értékek validáláshoz (requestben használjuk)
    public const BODY_TYPES = ['slim', 'average', 'athletic', 'curvy', 'plus'];
    public const HAIR_COLORS = ['black', 'brown', 'blonde', 'red', 'grey', 'other'];
    public const ORIENTATIONS = ['hetero', 'homo', 'bi', 'asexual', 'other'];
    public const MARITAL_STATUSES = ['single', 'relationship', 'married', 'divorced', 'widowed'];
    public const EDUCATION_LEVELS = ['primary', 'secondary', 'vocational', 'college', 'bachelor', 'master', 'phd'];
    public const PURPOSES = ['dating', 'friendship', 'serious', 'casual', 'networking'];

    // Kapcsolatok
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'dating_profile_language')
            ->withTimestamps();
    }
}
