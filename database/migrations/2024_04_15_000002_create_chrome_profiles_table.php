<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chrome_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pc_profile_id')->constrained()->onDelete('cascade');
            $table->string('profile_directory')->unique()->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'remove'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chrome_profiles');
    }
}; 