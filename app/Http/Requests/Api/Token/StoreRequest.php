<?php

namespace App\Http\Requests\Api\Token;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // nyilvános endpoint
    }

    public function rules(): array
    {
        return [
            'email'       => ['required','email','max:255'],
            'password'    => ['required','string','min:4','max:255'],
            'device_name' => ['nullable','string','max:255'], // lehet NULL
        ];
    }
}
