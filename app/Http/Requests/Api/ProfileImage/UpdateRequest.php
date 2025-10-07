<?php

namespace App\Http\Requests\Api\ProfileImage;

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
        'dating_profile_id' => ['sometimes', 'integer', 'exists:dating_profiles,id'],
        'image'             => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
        'caption'           => ['sometimes', 'nullable', 'string', 'max:255'],
        'visibility'        => ['sometimes', 'in:public,private'], // <-- ÚJ
        'is_primary'        => ['sometimes', 'boolean'],
        'sort_order'        => ['sometimes', 'nullable', 'integer', 'min:0'],
    ];
}
}
