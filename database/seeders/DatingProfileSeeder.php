<?php

namespace Database\Seeders;

use App\Models\Api\DatingProfile;
use App\Models\Api\Language;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatingProfileSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('dating_profile_language')->truncate();
        DatingProfile::truncate();

        Schema::enableForeignKeyConstraints();

        DB::transaction(function () {
            $languageIds = Language::pluck('id')->toArray();

            // minden userhez csak 1 profil
            $users = User::all();
            foreach ($users as $user) {
                $profile = DatingProfile::factory()->create([
                    'user_id' => $user->id,
                ]);

                // 1–3 nyelv véletlenszerűen
                if (!empty($languageIds)) {
                    $attach = collect($languageIds)->shuffle()->take(rand(1, 3))->all();
                    $profile->languages()->sync($attach);
                }
            }
        });
    }
}
