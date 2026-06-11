<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(): View
    {
        $comments = ResourceComment::orderBy('id', 'DESC')->paginate(10);

        return view('admin.comments.comments_list', compact('comments'));
    }

    public function published(ResourceComment $resourceComment)
    {
        $newStatus = $resourceComment->status == 1 ? 0 : 1;
        $resourceComment->update(['status' => $newStatus]);

        if ($newStatus == 1) {
            Resource::where('id', $resourceComment->resource_id)->increment('comments_count');
        } else {
            Resource::where('id', $resourceComment->resource_id)
                ->where('comments_count', '>', 0)
                ->decrement('comments_count');
        }

        return back();
    }

    public function delete(ResourceComment $resourceComment): RedirectResponse
    {
        if ($resourceComment->status == 1) {
            Resource::where('id', $resourceComment->resource_id)
                ->where('comments_count', '>', 0)
                ->decrement('comments_count');
        }
        $resourceComment->delete();

        return redirect()->back()->with('success', 'Comment has been deleted successfully!');
    }
}
