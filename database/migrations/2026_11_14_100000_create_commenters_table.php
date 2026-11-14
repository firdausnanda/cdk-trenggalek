<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Visitors who sign in with Google to comment on news. Kept apart from `users` (admin accounts with
 * roles) so a visitor account can never be used to reach the admin panel.
 */
return new class () extends Migration {
    public function up(): void
    {
        Schema::create('commenters', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('avatar', 1000)->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commenters');
    }
};
