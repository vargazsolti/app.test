<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ha már létezik ez az email, csak adminná tesszük
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'), // csak dev
            ]
        );

        if (!$user->is_admin) {
            $user->is_admin = true;
            $user->save();
        }
    }
}
