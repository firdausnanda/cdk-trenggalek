<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\User;
use App\Services\Charts\PostChartService;
use App\Services\Charts\UserChartService;
use App\Services\LanguageService;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __construct(
        private readonly UserChartService $userChartService,
        private readonly LanguageService $languageService,
        private readonly PostChartService $postChartService
    ) {
    }

    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['dashboard.view']);

        return view(
            'backend.pages.dashboard.index',
            [
                'content_stats' => $this->getContentStats(),
                'popular_posts' => Post::query()
                    ->where('post_type', 'post')
                    ->where('status', 'publish')
                    ->withCount('views')
                    ->orderByDesc('views_count')
                    ->limit(5)
                    ->get(['id', 'post_type', 'title', 'featured_image', 'published_at', 'created_at']),
                'total_users' => number_format(User::count()),
                'total_roles' => number_format(Role::count()),
                'total_permissions' => number_format(Permission::count()),
                'languages' => [
                    'total' => number_format(count($this->languageService->getLanguages())),
                    'active' => number_format(count($this->languageService->getActiveLanguages())),
                ],
                'user_growth_data' => $this->userChartService->getUserGrowthData(
                    request()->get('chart_filter_period', 'last_12_months')
                )->getData(true),
                'user_history_data' => $this->userChartService->getUserHistoryData(),
                'post_stats' => $this->postChartService->getPostActivityData(
                    request()->get('post_chart_filter_period', 'last_6_months')
                ),
                'breadcrumbs' => [
                    'title' => __('Dashboard'),
                    'show_home' => false,
                    'show_current' => false,
                ],
            ]
        );
    }

    /**
     * Headline numbers for the content-focused stat cards.
     *
     * @return array<string, int>
     */
    private function getContentStats(): array
    {
        $postViews = DB::table('views')->where('viewable_type', (new Post())->getMorphClass());
        $publishedPosts = Post::where('post_type', 'post')->where('status', 'publish');
        $monthStart = now()->startOfMonth();

        return [
            'published_posts' => (clone $publishedPosts)->count(),
            'posts_this_month' => (clone $publishedPosts)->where('created_at', '>=', $monthStart)->count(),
            'total_views' => (clone $postViews)->count(),
            'views_this_month' => (clone $postViews)->where('viewed_at', '>=', $monthStart)->count(),
            'pending_comments' => Comment::where('is_approved', false)->count(),
            'total_comments' => Comment::count(),
            'contact_messages' => ContactMessage::count(),
            'messages_this_month' => ContactMessage::where('created_at', '>=', $monthStart)->count(),
        ];
    }
}
