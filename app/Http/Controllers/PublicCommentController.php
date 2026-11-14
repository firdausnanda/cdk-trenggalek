<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Commenter;
use App\Models\Post;
use App\Rules\NotEmptyHtml;
use App\Services\CommentModeration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Chat-style comments on news posts: send (live straight away unless the filter holds it), delete
 * your own, and report someone else's. Answers JSON for the chat script and redirects for plain forms.
 */
class PublicCommentController extends Controller
{
    public function __construct(private readonly CommentModeration $moderation)
    {
    }

    public function store(Request $request, int $post): JsonResponse|RedirectResponse
    {
        $post = Post::type('post')->published()->findOrFail($post);
        $commenter = $this->commenter();

        if ($commenter->isBlocked()) {
            return $this->fail($request, __('public.comments.blocked'), 403);
        }

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000', new NotEmptyHtml()],
            'parent_id' => ['nullable', 'integer', Rule::exists('comments', 'id')->where('post_id', $post->id)],
        ], [
            'comment.required' => __('public.validation.comment_required'),
            'comment.max' => __('public.comments.too_long'),
            'parent_id.exists' => __('public.comments.reply_missing'),
        ]);

        $content = CommentModeration::clean($validated['comment']);
        if ($content === '') {
            throw ValidationException::withMessages(['comment' => __('public.validation.comment_required')]);
        }

        $key = 'komentar:'.$commenter->id;
        if (RateLimiter::tooManyAttempts($key, CommentModeration::RATE_LIMIT)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            return $this->fail($request, __('public.comments.too_many', ['minutes' => max(1, $minutes)]), 429);
        }

        if ($this->moderation->isDuplicate($commenter, $content)) {
            return $this->fail($request, __('public.comments.duplicate'), 422);
        }

        RateLimiter::hit($key, CommentModeration::RATE_LIMIT_SECONDS);

        $comment = Comment::create([
            'post_id' => $post->id,
            'commenter_id' => $commenter->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $commenter->name,
            'email' => $commenter->email,
            'content' => $content,
            'status' => $this->moderation->statusFor($content),
        ]);

        $comment->setRelation('commenter', $commenter);
        $comment->load('parent.commenter', 'parent.user');

        $message = $comment->isPublished() ? __('public.comments.sent') : __('public.comments.held');

        if (! $request->expectsJson()) {
            return redirect()->to(route('berita.show', $post->slug).'#komentar-'.$comment->id)
                ->with('komentar_success', $message);
        }

        return response()->json([
            'id' => $comment->id,
            'status' => $comment->status,
            'message' => $message,
            'date' => $comment->created_at->toDateString(),
            'date_label' => komentar_tanggal($comment->created_at),
            'html' => view('public.partials.comment-bubble', [
                'c' => $comment,
                'viewer' => $commenter,
                'visibleIds' => [$comment->parent_id => true],
            ])->render(),
        ], 201);
    }

    public function destroy(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        abort_unless($comment->isOwnedBy($this->commenter()), 403);

        $slug = $comment->post?->slug;
        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => __('public.comments.deleted')]);
        }

        return redirect()->to(($slug ? route('berita.show', $slug) : route('berita')).'#komentar')
            ->with('komentar_success', __('public.comments.deleted'));
    }

    public function report(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        $commenter = $this->commenter();

        $validated = $request->validate([
            'reason' => ['required', Rule::in(CommentReport::REASONS)],
        ]);

        if ($comment->isOwnedBy($commenter) || $comment->isFromAdmin() || ! $comment->isPublished()) {
            return $this->fail($request, __('public.comments.cannot_report'), 422);
        }

        $message = $this->moderation->report($comment, $commenter, $validated['reason'])
            ? __('public.comments.reported')
            : __('public.comments.already_reported');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'hidden' => $comment->status === Comment::STATUS_HIDDEN]);
        }

        return redirect()->to(route('berita.show', $comment->post->slug).'#komentar')
            ->with('komentar_success', $message);
    }

    private function commenter(): Commenter
    {
        /** @var Commenter */
        return Auth::guard('commenter')->user();
    }

    private function fail(Request $request, string $message, int $status): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withInput()->with('komentar_error', $message)->withFragment('komentar-tulis');
    }
}
