<?php

namespace Database\Seeders;

use App\Models\Api\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Language::truncate();
        Schema::enableForeignKeyConstraints();

        DB::transaction(function () {
            $defaults = [
                ['code' => 'hu', 'name' => 'Magyar'],
                ['code' => 'en', 'name' => 'English'],
                ['code' => 'de', 'name' => 'Deutsch'],
                ['code' => 'fr', 'name' => 'Français'],
                ['code' => 'es', 'name' => 'Español'],
            ];

            foreach ($defaults as $row) {
                Language::firstOrCreate(['code' => $row['code']], $row);
            }

            // Opcionálisan generál még pár random nyelvet
            Language::factory(3)->create();
        });
    }
}
