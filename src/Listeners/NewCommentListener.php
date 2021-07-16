<?php

namespace Botble\Comment\Listeners;

use Botble\Comment\Events\NewCommentEvent;
use Botble\Comment\Repositories\Interfaces\CommentInterface;
use EmailHandler;
use Html;
use Illuminate\Contracts\Queue\ShouldQueue;
use URL;

class NewCommentListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param NewCommentEvent $event
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Throwable
     */
    public function handle(NewCommentEvent $event)
    {
        $parentId = request()->input('parent_id');

        // on reply
        if ($parentId > 0) {
            $comment = app(CommentInterface::class)->getFirstBy(['id' => $parentId]);
            $article = $event->comment->reference()->first();
            if (
                $article &&
                $comment &&
                $comment->user->id !== $event->comment->user->id)
            {
                $sendTo = $comment->user->email;

                $mailer = EmailHandler::setModule(COMMENT_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'user_name'         => $event->commentUser->name,
                        'post_name'         => $article->name,
                        'post_link'         => $article->url,
                        'comment_content'   => $event->comment->comment,
                    ]);

                $mailer->sendUsingTemplate('notify_email', $sendTo);
            }
        }
    }
}
