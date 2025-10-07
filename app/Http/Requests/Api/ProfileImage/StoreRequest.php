<?php

namespace App\Http\Requests\Api\ProfileImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Sanctum middleware védi route szinten
    }

    public function rules(): array
{
    return [
        'dating_profile_id' => ['required', 'integer', 'exists:dating_profiles,id'],
        'image'             => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        'caption'           => ['nullable', 'string', 'max:255'],
        'visibility'        => ['nullable', 'in:public,private'], // <-- ÚJ
        'is_primary'        => ['nullable', 'boolean'],
        'sort_order'        => ['nullable', 'integer', 'min:0'],
    ];
}
}
