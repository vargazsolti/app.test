<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profile_images', function (Blueprint $table) {
            $table->string('visibility')->default('public')->after('caption'); // public|private
            $table->index(['visibility']);
        });
    }

    public function down(): void
    {
        Schema::table('profile_images', function (Blueprint $table) {
            $table->dropIndex(['visibility']);
            $table->dropColumn('visibility');
        });
    }
};
