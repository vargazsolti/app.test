<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profile_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dating_profile_id')
                  ->constrained('dating_profiles')
                  ->cascadeOnDelete();
            $table->string('path'); // storage path (public disk)
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->nullable();
            $table->timestamps();

            $table->index(['dating_profile_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_images');
    }
};
