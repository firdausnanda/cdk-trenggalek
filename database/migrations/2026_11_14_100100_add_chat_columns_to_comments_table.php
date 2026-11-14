<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Comments go live straight away (visitors are signed in with Google), so the approval flag becomes
 * a status: published, hidden (reported or hidden by an admin) or pending_review (caught by the word
 * filter). Also links a comment to its Google commenter or replying admin, and to the comment it
 * replies to.
 */
return new class () extends Migration {
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('commenter_id')->nullable()->after('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->after('commenter_id')->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('comments')->nullOnDelete();
            $table->string('status', 20)->default('published')->after('content');
            $table->unsignedInteger('reports_count')->default(0)->after('status');

            $table->index(['post_id', 'status', 'created_at']);
        });

        // Approved comments stay visible; ones still waiting for approval wait for review instead.
        DB::table('comments')->where('is_approved', true)->update(['status' => 'published']);
        DB::table('comments')->where('is_approved', false)->update(['status' => 'pending_review']);

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('content');
        });

        DB::table('comments')->update(['is_approved' => false]);
        DB::table('comments')->where('status', 'published')->update(['is_approved' => true]);

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['post_id', 'status', 'created_at']);
            $table->dropConstrainedForeignId('parent_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('commenter_id');
            $table->dropColumn(['status', 'reports_count']);
        });
    }
};
