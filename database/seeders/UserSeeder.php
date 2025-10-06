<?php

namespace Database\Seeders;

use App\Models\Api\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1) FK tiltás
        Schema::disableForeignKeyConstraints();

        // 2) truncate
        User::truncate();

        // 3) FK engedélyezés
        Schema::enableForeignKeyConstraints();

        // 4) Insert tranzakcióban
        DB::transaction(function () {
            // Admin/teszt felhasználó
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                // jelszó: password (factory-ban definiálva)
            ]);

            // További dummy felhasználók
            User::factory(5)->create();
        });
    }
}
