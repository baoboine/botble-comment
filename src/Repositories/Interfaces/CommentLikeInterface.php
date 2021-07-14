<?php

namespace Botble\Comment\Repositories\Interfaces;

use Botble\Comment\Models\Comment;
use Botble\Comment\Models\CommentUser;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface CommentLikeInterface extends RepositoryInterface
{
    /**
     * @param Comment $comment
     * @param CommentUser $user
     * @return mixed
     */
    public function likeThisComment(Comment $comment, CommentUser $user);
}
