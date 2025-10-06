<?php

namespace Database\Factories\Api;

use App\Models\Api\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        // Generáljunk ISO-szerű kódot és nevet
        $name = $this->faker->unique()->languageCode(); // pl. en, de, fr
        return [
            'code' => strtolower($name),
            'name' => strtoupper($name),
        ];
    }
}
