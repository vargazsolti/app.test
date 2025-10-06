<?php

namespace App\Http\Requests\Api\Me;

use Illuminate\Foundation\Http\FormRequest;

class ShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth:sanctum védi
    }

    public function rules(): array
    {
        return [
            // nincs kötelező input; csak visszaadjuk a bejelentkezett usert
        ];
    }
}
