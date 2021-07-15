<?php

namespace Botble\Comment\Repositories\Eloquent;

use Botble\Comment\Models\Comment;
use Botble\Comment\Repositories\Interfaces\CommentLikeInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class CommentLikeRepository extends RepositoriesAbstract implements CommentLikeInterface
{
    /**
     * @inheritDoc
     */
    public function likeThisComment(Comment $comment,  $user)
    {
        $params = ['comment_id' => $comment->id, 'user_id' => $user->id];
        $like = $this->getFirstBy($params);

        if ($like) { // unlike
            $like->delete();
            return false;
        }

        $this->createOrUpdate($params);
        return true;
    }
}
