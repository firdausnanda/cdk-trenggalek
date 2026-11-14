<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Commenter;
use App\Services\CommentModeration;
use App\Services\SettingService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Comments go live without approval; admins work through what visitors reported or the filter held
 * back, reply as the office, block visitors, and set the filter words and report threshold.
 */
class CommentController extends Controller
{
    private const FILTERS = ['needs_review', 'reported', 'hidden', 'pending_review', 'published'];

    private const SORTABLE = ['created_at', 'reports_count'];

    public function __construct(private readonly CommentModeration $moderation)
    {
    }

    public function index(Request $request): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['comment.view']);

        $filter = in_array($request->query('status'), self::FILTERS, true) ? $request->query('status') : null;

        $query = Comment::query()
            ->with([
                'post:id,title,slug',
                'commenter:id,name,email,avatar,blocked_at',
                'user:id,name',
                'parent:id,content,name,commenter_id,user_id',
                'parent.commenter:id,name',
                'reports:id,comment_id,reason',
            ]);

        if ($request->filled('search')) {
            $q = (string) $request->query('search');
            $query->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%")
                    ->orWhereHas('commenter', fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
            });
        }

        match ($filter) {
            'needs_review' => $query->needsReview(),
            'reported' => $query->where('reports_count', '>', 0),
            'hidden', 'pending_review', 'published' => $query->where('status', $filter),
            default => null,
        };

        $sort = ltrim((string) $request->query('sort', ''), '-');
        if (in_array($sort, self::SORTABLE, true)) {
            $query->orderBy($sort, str_starts_with((string) $request->query('sort'), '-') ? 'desc' : 'asc');
        } else {
            // What needs a decision first, then newest.
            $query->orderByRaw("CASE WHEN status <> 'published' OR reports_count > 0 THEN 0 ELSE 1 END")
                ->latest('id');
        }

        $counts = [
            'needs_review' => Comment::needsReview()->count(),
            'reported' => Comment::where('reports_count', '>', 0)->count(),
            'hidden' => Comment::where('status', Comment::STATUS_HIDDEN)->count(),
            'pending_review' => Comment::where('status', Comment::STATUS_PENDING_REVIEW)->count(),
        ];

        return view('backend.pages.comment.index', [
            'comments' => $query->paginate(15)->withQueryString(),
            'filter' => $filter,
            'counts' => $counts,
            'blockedWords' => implode("\n", $this->moderation->blockedWords()),
            'reportThreshold' => $this->moderation->reportThreshold(),
            'breadcrumbs' => [
                'title' => __('Comment'),
            ],
        ]);
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['comment.delete']);

        $comment->delete();

        return back()->with('success', __('Comment deleted successfully.'));
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['comment.delete']);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:comments,id',
        ]);

        Comment::whereIn('id', $request->ids)->delete();

        return back()->with('success', __('Selected comments deleted successfully.'));
    }

    public function publish(Comment $comment): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['comment.edit']);

        $this->moderation->publish($comment);

        return back()->with('success', __('Comment is visible on the site again.'));
    }

    public function hide(Comment $comment): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['comment.edit']);

        $this->moderation->hide($comment);

        return back()->with('success', __('Comment hidden from the site.'));
    }

    public function reply(Request $request, Comment $comment): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['comment.create']);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $content = CommentModeration::clean($validated['content']);
        if ($content === '') {
            return back()->withErrors(['content' => __('validation.required', ['attribute' => 'content'])]);
        }

        Comment::create([
            'post_id' => $comment->post_id,
            'user_id' => Auth::id(),
            'parent_id' => $comment->id,
            'name' => (string) config('cdk.nama_singkat', 'Admin'),
            'content' => $content,
            'status' => Comment::STATUS_PUBLISHED,
        ]);

        return back()->with('success', __('Reply posted.'));
    }

    public function block(Commenter $commenter): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['comment.edit']);

        $commenter->update(['blocked_at' => now()]);

        return back()->with('success', __(':name is blocked. Their comments are no longer shown.', ['name' => $commenter->name]));
    }

    public function unblock(Commenter $commenter): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['comment.edit']);

        $commenter->update(['blocked_at' => null]);

        return back()->with('success', __(':name can comment again.', ['name' => $commenter->name]));
    }

    public function updateSettings(Request $request, SettingService $settings): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['comment.edit']);

        $validated = $request->validate([
            'blocked_words' => 'nullable|string|max:10000',
            'report_threshold' => 'required|integer|min:1|max:50',
        ]);

        $words = collect(preg_split('/[\r\n,]+/', (string) ($validated['blocked_words'] ?? '')) ?: [])
            ->map(fn (string $word) => trim($word))
            ->filter()
            ->unique(fn (string $word) => mb_strtolower($word))
            ->implode("\n");

        $settings->addSetting(CommentModeration::SETTING_BLOCKED_WORDS, $words);
        $settings->addSetting(CommentModeration::SETTING_REPORT_THRESHOLD, (string) $validated['report_threshold']);

        return back()->with('success', __('Comment settings saved.'));
    }
}
