<?php

namespace Botble\Comment\Repositories\Caches;

use Botble\Comment\Repositories\Interfaces\CommentRatingInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class CommentRatingCacheDecorator extends CacheAbstractDecorator implements CommentRatingInterface
{

    /**
     * @inheritDoc
     */
    public function getRatingOfArticle(array $reference, $user)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function storageRating(array $reference, $user, int $rating = 0)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }
}
