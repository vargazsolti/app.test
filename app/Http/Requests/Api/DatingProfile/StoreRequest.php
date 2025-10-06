<?php

namespace App\Http\Requests\Api\DatingProfile;

use App\Models\Api\DatingProfile;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sanctum védi az endpointot; itt engedjük
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'max:100'],
            'height_cm' => ['required', 'integer', 'min:100', 'max:250'],
            'weight_kg' => ['required', 'integer', 'min:30', 'max:250'],

            'body_type' => ['required', 'string', 'in:' . implode(',', DatingProfile::BODY_TYPES)],
            'hair_color' => ['required', 'string', 'in:' . implode(',', DatingProfile::HAIR_COLORS)],
            'sexual_orientation' => ['required', 'string', 'in:' . implode(',', DatingProfile::ORIENTATIONS)],
            'marital_status' => ['required', 'string', 'in:' . implode(',', DatingProfile::MARITAL_STATUSES)],
            'education_level' => ['required', 'string', 'in:' . implode(',', DatingProfile::EDUCATION_LEVELS)],
            'occupation' => ['required', 'string', 'max:150'],

            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],

            'registration_purpose' => ['required', 'string', 'in:' . implode(',', DatingProfile::PURPOSES)],

            // nyelvek: id-k tömbje
            'language_ids' => ['sometimes', 'array'],
            'language_ids.*' => ['integer', 'exists:languages,id'],
        ];
    }
}
