<?php

namespace Botble\Comment\Repositories\Caches;

use Botble\Support\Repositories\Caches\CacheAbstractDecorator;
use Botble\Comment\Repositories\Interfaces\CommentInterface;

class CommentCacheDecorator extends CacheAbstractDecorator implements CommentInterface
{

    /**
     * @inheritDoc
     */
    public function storageComment(array $input)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getComments(array $reference = [], int $parentId = 0, int $page = 1, int $limit = 20, string $sort = 'newest')
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
