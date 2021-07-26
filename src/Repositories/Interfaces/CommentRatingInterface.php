<?php

namespace Botble\Comment\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface CommentRatingInterface extends RepositoryInterface
{
    /**
     * @param array $reference
     * @param $user
     * @return mixed
     */
    public function getRatingOfArticle(array $reference, $user);

    /**
     * @param array $reference
     * @param $user
     * @param int $rating
     * @return mixed
     */
    public function storageRating(array $reference, $user, int $rating = 0);
}
