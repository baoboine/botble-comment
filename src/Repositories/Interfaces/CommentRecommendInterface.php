<?php

namespace Botble\Comment\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface CommentRecommendInterface extends RepositoryInterface
{
    /**
     * @param array $reference
     * @param $user
     * @return mixed
     */
    public function getRecommendOfArticle(array $reference, $user);
}
