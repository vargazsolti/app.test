<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dating_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('nickname');                  // Becenév *
            $table->unsignedSmallInteger('height_cm');   // Magasság * (cm)
            $table->unsignedSmallInteger('weight_kg');   // Testsúly * (kg)

            $table->string('body_type');                 // Testalkat *
            $table->string('hair_color');                // Hajszín *
            $table->string('sexual_orientation');        // Szexuális beállítottság *
            $table->string('marital_status');            // Családi állapot *
            $table->string('education_level');           // Végzettség *
            $table->string('occupation');                // Foglalkozás *

            $table->string('country');                   // Lakhely (ország) *
            $table->string('state');                     // (megye) *
            $table->string('city');                      // (város) *

            $table->string('registration_purpose');      // Regisztráció célja *
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dating_profiles');
    }
};
