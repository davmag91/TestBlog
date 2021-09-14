<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreComment;

class UserCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
    }

    public function store(User $user, StoreComment $request){
        $user->commentsOn()->create([
            'content' => $request->input('content'),
            'user_id' => $request->user()->id,
            'commentable_id' => $user->id,
            'commentable_type' => 'App\Models\User'
        ]);

        $request->session()->flash('status', 'Comment was added!');

        return redirect()->back();
    }
}
