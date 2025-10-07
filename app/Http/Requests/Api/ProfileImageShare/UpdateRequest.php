<?php

namespace App\Http\Requests\Api\ProfileImageShare;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'shared_with_user_id'  => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }
}
