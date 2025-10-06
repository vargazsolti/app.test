<?php

namespace App\Http\Requests\Api\Token;

use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth:sanctum védi
    }

    public function rules(): array
    {
        return [
            // nincs kötelező mező; aktuális token törlődik
        ];
    }
}
