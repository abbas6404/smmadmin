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
        Schema::create('submission_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('submission_type', ['facebook', 'gmail', 'twitter', 'instagram', 'facebook_and_gmail']);
            $table->integer('total_submissions')->default(0);
            $table->integer('accurate_submissions')->default(0);
            $table->integer('incorrect_submissions')->default(0);
            $table->boolean('approved')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_batch');
    }
};
