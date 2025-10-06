<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dating_profile_language', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dating_profile_id')
                ->constrained('dating_profiles')
                ->cascadeOnDelete();
            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['dating_profile_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dating_profile_language');
    }
};
