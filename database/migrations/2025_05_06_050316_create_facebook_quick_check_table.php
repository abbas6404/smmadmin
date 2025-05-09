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
        Schema::create('facebook_quick_check', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('two_factor_secret')->nullable();
            $table->enum('status', ['pending', 'processing', 'active', 'in_use', 'blocked'])->default('pending');
            $table->string('check_result')->nullable();
            $table->dateTime('last_checked_at')->nullable();
            $table->string('checked_by')->nullable();
            $table->integer('check_count')->default(0);
            $table->text('notes')->nullable();
            $table->json('account_cookies')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_quick_check');
    }
};
