<?php

namespace Botble\Comment\Repositories\Eloquent;

use Botble\Comment\Repositories\Interfaces\CommentRatingInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Arr;

class CommentRatingRepository extends RepositoriesAbstract implements CommentRatingInterface
{
    /**
     * @inheritDoc
     */
    public function getRatingOfArticle(array $reference, $user)
    {
        $rated = null;
        $params = Arr::only($reference, [
            'reference_type', 'reference_id'
        ]);

        if ($user && $item = $this->getFirstBy(array_merge($params, ['user_id' => $user->id]))) {
            $rated = $item->rating;
        }
        $lists = $this->allBy($params);

        $rating = $lists->average('rating');
        $data = $lists->pluck('rating', 'user_id');
        $count = count($lists);

        return compact('rated', 'rating', 'data', 'count');
    }

    /**
     * @inheritDoc
     */
    public function storageRating(array $reference, $user, int $rating = 0)
    {
        if ((int)$rating > 0) {
            $params = array_merge(Arr::only($reference, ['reference_type', 'reference_id']), ['user_id' => $user->id]);
            return $this->createOrUpdate(array_merge($params, compact('rating')), $params);
        }
        return null;
    }
}
