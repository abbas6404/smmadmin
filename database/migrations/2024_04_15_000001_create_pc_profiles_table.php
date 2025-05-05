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
            $table->string('pc_name')->nullable();
            $table->string('email');
            $table->string('password');
            $table->string('hostname')->nullable();
            $table->string('os_version')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('profile_root_directory')->nullable();
            $table->string('hardware_id')->unique()->nullable();
            $table->string('access_token', 64)->unique()->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked', 'deleted'])->default('inactive');
            $table->integer('max_profile_limit')->default(6);
            $table->integer('max_order_limit')->default(5);
            $table->integer('min_profile_limit')->default(1);
            $table->integer('min_order_limit')->default(1);
            $table->integer('cpu_cores')->nullable();
            $table->decimal('total_memory', 8, 2)->nullable()->comment('Total memory in GB');
            $table->timestamp('last_verified_at')->nullable();
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