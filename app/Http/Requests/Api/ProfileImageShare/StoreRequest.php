<?php

namespace App\Http\Requests\Api\ProfileImageShare;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'profile_image_id'     => ['required', 'integer', 'exists:profile_images,id'],
            'shared_with_user_id'  => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
