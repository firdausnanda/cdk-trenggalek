<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Marks official (admin) replies on their own, so a reply stays official after the admin account that wrote it
 * is deleted (user_id is then set to null).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_official')->default(false)->after('user_id')->index();
        });

        DB::table('comments')->whereNotNull('user_id')->update(['is_official' => true]);
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['is_official']);
            $table->dropColumn('is_official');
        });
    }
};
