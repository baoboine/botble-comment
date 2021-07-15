<?php

namespace Botble\Comment\Repositories\Caches;

use Botble\Comment\Models\Comment;
use Botble\Comment\Repositories\Interfaces\CommentLikeInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class CommentLikeCacheDecorator extends CacheAbstractDecorator implements CommentLikeInterface
{

    /**
     * @inheritDoc
     */
    public function likeThisComment(Comment $comment, $user)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }
}
