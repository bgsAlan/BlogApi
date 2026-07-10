<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendCommentNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        //get comment in post and comment user
        $post = $event->comment->post;
        $commenter = $event->comment->user;

        sleep(3);
        //Use log
        Log::info("Notifikasi: {$commenter->name} komentar di post '{$post->title}' milik {$post->user->name}");

    }
}
