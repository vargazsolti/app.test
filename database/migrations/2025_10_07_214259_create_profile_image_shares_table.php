<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profile_image_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_image_id')->constrained('profile_images')->cascadeOnDelete();
            $table->foreignId('shared_with_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['profile_image_id', 'shared_with_user_id'], 'ux_image_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_image_shares');
    }
};
