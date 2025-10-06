<?php

namespace App\Http\Requests\Api\DatingProfile;

use App\Models\Api\DatingProfile;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => ['sometimes', 'string', 'max:100'],
            'height_cm' => ['sometimes', 'integer', 'min:100', 'max:250'],
            'weight_kg' => ['sometimes', 'integer', 'min:30', 'max:250'],

            'body_type' => ['sometimes', 'string', 'in:' . implode(',', DatingProfile::BODY_TYPES)],
            'hair_color' => ['sometimes', 'string', 'in:' . implode(',', DatingProfile::HAIR_COLORS)],
            'sexual_orientation' => ['sometimes', 'string', 'in:' . implode(',', DatingProfile::ORIENTATIONS)],
            'marital_status' => ['sometimes', 'string', 'in:' . implode(',', DatingProfile::MARITAL_STATUSES)],
            'education_level' => ['sometimes', 'string', 'in:' . implode(',', DatingProfile::EDUCATION_LEVELS)],
            'occupation' => ['sometimes', 'string', 'max:150'],

            'country' => ['sometimes', 'string', 'max:100'],
            'state' => ['sometimes', 'string', 'max:100'],
            'city' => ['sometimes', 'string', 'max:100'],

            'registration_purpose' => ['sometimes', 'string', 'in:' . implode(',', DatingProfile::PURPOSES)],

            'language_ids' => ['sometimes', 'array'],
            'language_ids.*' => ['integer', 'exists:languages,id'],
        ];
    }
}
