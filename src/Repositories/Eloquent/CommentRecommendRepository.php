<?php

namespace Botble\Comment\Repositories\Eloquent;

use Botble\Comment\Repositories\Interfaces\CommentRecommendInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Arr;

class CommentRecommendRepository extends RepositoriesAbstract implements CommentRecommendInterface
{
    /**
     * @inheritDoc
     */
    public function getRecommendOfArticle(array $reference, $user)
    {
        $isRecommended = false;
        $params = Arr::only($reference, [
            'reference_type', 'reference_id'
        ]);

        if ($user && $this->getFirstBy(array_merge($params, ['user_id' => $user->id]))) {
            $isRecommended = true;
        }

        $count = $this->count($params);

        return compact('isRecommended', 'count');
    }
}
