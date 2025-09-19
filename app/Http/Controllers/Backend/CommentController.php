<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function index(Request $request): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['user.view']);

        $query = Comment::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('content', 'like', "%$q%");
            });
        }

        // Filter status approval
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Sort
        if ($request->filled('sort')) {
            $sort = $request->sort;
            $direction = 'asc';
            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = ltrim($sort, '-');
            }
            $query->orderBy($sort, $direction);
        } else {
            $query->join('posts', 'comments.post_id', '=', 'posts.id')
                ->select('comments.*')
                ->orderBy('comments.is_approved', 'asc')
                ->orderBy('posts.created_at', 'desc');
        }

        $comments = $query->paginate(10);

        $breadcrumbs = [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('Comment')],
        ];

        return view('backend.pages.comment.index', [
            'comments' => $comments,
            'breadcrumbs' => [
                'title' => __('Comment'),
            ],
        ]);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->route('admin.comment.index')
            ->with('success', __('Comment deleted successfully.'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:comments,id',
        ]);

        Comment::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.comment.index')
            ->with('success', __('Selected comments deleted successfully.'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        return redirect()->route('admin.comment.index')
            ->with('success', __('Comment approved successfully.'));
    }

    public function unapprove(Comment $comment)
    {
        $comment->update(['is_approved' => false]);

        return redirect()->route('admin.comment.index')
            ->with('success', __('Comment unapproved successfully.'));
    }
}
