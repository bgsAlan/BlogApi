<?php

namespace App\Http\Controllers;

use App\Events\CommentCreated;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post)
    {
        $comment = new \App\Models\Comment($request->validated());
        $comment->post_id = $post->id;
        $comment->user_id = auth()->id(); // set manual, bypass fillable
        $comment->save();

        event(new CommentCreated($comment));

        return new CommentResource($comment->load('user'));
    }

    
}
