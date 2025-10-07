<?php

namespace Database\Seeders;

use App\Models\Api\ProfileImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileImageSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ProfileImage::truncate();
        Schema::enableForeignKeyConstraints();

        DB::transaction(function () {
            // minden profilhoz 1-3 image (dummy path-okkal)
            \App\Models\Api\DatingProfile::all()->each(function ($profile) {
                $count = rand(1, 3);
                $images = ProfileImage::factory($count)->create([
                    'dating_profile_id' => $profile->id,
                ]);

                // első legyen primary
                $first = $images->first();
                if ($first) {
                    $first->update(['is_primary' => true]);
                }
            });
        });
    }
}
