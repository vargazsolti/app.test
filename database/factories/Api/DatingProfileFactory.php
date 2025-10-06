<?php

namespace Database\Factories\Api;

use App\Models\Api\DatingProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatingProfileFactory extends Factory
{
    protected $model = DatingProfile::class;

    public function definition(): array
    {
        $faker = $this->faker;

        return [
            // ⚠️ Itt már NEM adunk meg user_id-t → a seeder adja meg
            'nickname' => $faker->unique()->userName(),
            'height_cm' => $faker->numberBetween(150, 200),
            'weight_kg' => $faker->numberBetween(45, 110),
            'body_type' => $faker->randomElement(DatingProfile::BODY_TYPES),
            'hair_color' => $faker->randomElement(DatingProfile::HAIR_COLORS),
            'sexual_orientation' => $faker->randomElement(DatingProfile::ORIENTATIONS),
            'marital_status' => $faker->randomElement(DatingProfile::MARITAL_STATUSES),
            'education_level' => $faker->randomElement(DatingProfile::EDUCATION_LEVELS),
            'occupation' => $faker->jobTitle(),
            'country' => $faker->country(),
            'state' => $faker->state(),
            'city' => $faker->city(),
            'registration_purpose' => $faker->randomElement(DatingProfile::PURPOSES),
        ];
    }
}
