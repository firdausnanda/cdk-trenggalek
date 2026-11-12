<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Indexes for the post lists: the admin list filters by post_type and sorts by created_at,
     * the public site filters by post_type + status and sorts by published_at, and the filter
     * dropdowns look terms up by taxonomy.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['post_type', 'created_at'], 'posts_type_created_index');
            $table->index(['post_type', 'status', 'published_at'], 'posts_type_status_published_index');
        });

        Schema::table('terms', function (Blueprint $table) {
            $table->index('taxonomy', 'terms_taxonomy_index');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_type_created_index');
            $table->dropIndex('posts_type_status_published_index');
        });

        Schema::table('terms', function (Blueprint $table) {
            $table->dropIndex('terms_taxonomy_index');
        });
    }
};
