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
        Schema::create('pc_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('pc_name');
            $table->string('hardware_id')->unique();
            $table->integer('max_profile_limit')->default(1);
            $table->integer('max_link_limit')->default(10);
            $table->enum('status', ['active', 'inactive', 'blocked', 'deleted'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pc_profiles');
    }
}; 