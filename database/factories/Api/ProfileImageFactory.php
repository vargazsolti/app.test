<?php

namespace Database\Factories\Api;

use App\Models\Api\DatingProfile;
use App\Models\Api\ProfileImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileImageFactory extends Factory
{
    protected $model = ProfileImage::class;

    public function definition(): array
    {
        return [
            'dating_profile_id' => DatingProfile::query()->inRandomOrder()->value('id') ?? DatingProfile::factory(),
            'path'              => 'profile-images/sample-' . $this->faker->uuid . '.jpg', // dummy path
            'caption'           => $this->faker->optional()->sentence(3),
            'is_primary'        => false,
            'sort_order'        => $this->faker->optional()->numberBetween(0, 10),
            'visibility' => $this->faker->randomElement(['public','private']),
        ];
    }
}
