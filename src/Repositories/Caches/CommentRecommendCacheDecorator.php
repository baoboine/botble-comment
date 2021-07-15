<?php

namespace Botble\Comment\Repositories\Caches;

use Botble\Comment\Repositories\Interfaces\CommentRecommendInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class CommentRecommendCacheDecorator extends CacheAbstractDecorator implements CommentRecommendInterface
{

    /**
     * @inheritDoc
     */
    public function getRecommendOfArticle(array $reference, $user)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
