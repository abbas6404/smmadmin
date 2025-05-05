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
        Schema::create('disks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pc_profile_id')->constrained('pc_profiles')->onDelete('cascade');
            $table->string('drive_letter', 1); // C, D, E, etc.
            $table->string('file_system')->nullable(); // NTFS, FAT32, etc.
            $table->bigInteger('total_size')->nullable(); // Total size in bytes
            $table->bigInteger('free_space')->nullable(); // Free space in bytes
            $table->bigInteger('used_space')->nullable(); // Used space in bytes
            $table->decimal('health_percentage', 5, 2)->nullable(); // Drive health percentage
            $table->integer('read_speed')->nullable(); // Average read speed in MB/s
            $table->integer('write_speed')->nullable(); // Average write speed in MB/s
            $table->timestamp('last_checked_at')->nullable(); // When the disk info was last updated
            $table->timestamps();
            $table->softDeletes();
            // Add index for faster queries
            $table->index(['pc_profile_id', 'drive_letter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disks');
    }
};
