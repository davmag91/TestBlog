<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Http\Requests\StoreComment;

class PostCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
    }

    public function store(BlogPost $post, StoreComment $request){
        $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => $request->user()->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\BlogPost'
        ]);

        $request->session()->flash('status', 'Comment was added!');

        return redirect()->back();
    }
}
