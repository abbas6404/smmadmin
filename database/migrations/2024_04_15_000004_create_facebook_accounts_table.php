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
        Schema::create('facebook_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pc_profile_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('chrome_profile_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('submission_batch_id')->nullable()->constrained('submission_batch')->onDelete('set null');
            $table->foreignId('gmail_account_id')->nullable()->constrained('gmail_accounts')->onDelete('set null');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('total_count')->default(0);
            $table->boolean('have_use')->default(false);
            $table->boolean('have_page')->default(false);
            $table->boolean('have_post')->default(false);
            $table->enum('status', ['pending', 'processing', 'active', 'inactive', 'remove'])->default('pending');
            $table->json('order_link_uid')->nullable();
            $table->string('lang')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('facebook_accounts');
    }
}; 