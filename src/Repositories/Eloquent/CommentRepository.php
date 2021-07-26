<?php

namespace Botble\Comment\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Botble\Comment\Repositories\Interfaces\CommentInterface;

class CommentRepository extends RepositoriesAbstract implements CommentInterface
{
    /**
     * @inheritDoc
     */
    public function storageComment(array $input)
    {
        $condition = [];

        if (!empty($input['comment_id'])) {
            $condition = ['id' => $input['comment_id']];
        }

        $input['comment'] = clean($input['comment']);
        return $this->createOrUpdate($input, $condition);
    }

    /**
     * @inheritDoc
     */
    public function getComments(array $reference = [], int $parentId = 0, int $page = 1, int $limit = 20, string $sort = 'newest')
    {
        $condition = [
            'status'           => BaseStatusEnum::PUBLISHED,
            'reference_type'   => $reference['reference_type'],
            'reference_id'     => $reference['reference_id'],
        ];

        $orderBy = (function() use ($sort) {
            switch($sort) {
                default:
                case 'newest':
                    return ['created_at' => 'desc'];
                case 'oldest':
                    return ['created_at' => 'asc'];
                case 'best':
                    return ['like_count' => 'desc'];
            }
        })();

        $params = [
            'condition' => array_merge($condition, [
                'parent_id'        => $parentId,
            ]),
            'order_by'  => $orderBy,
            'paginate'  => [
                'per_page'      => $limit,
                'current_paged' => $page,
            ],
            'select'    => [
                'id',
                'comment',
                'user_id',
                'ip_address',
                'created_at',
                'reply_count',
                'like_count',
                'parent_id',
            ],
        ];

        $count = -1;

        if ($parentId === 0 && $page === 1) {
            $count = $this->count($condition);
        }

        $result = $this->advancedGet($params);

        return [
            $result->getCollection(),
            [
                'total'         => $result->total(),
                'per_page'      => $result->perPage(),
                'current_page'  => $result->currentPage(),
                'last_page'     => $result->lastPage(),
                'from'          => $result->firstItem(),
                'to'            => $result->lastItem(),
                'count_all'     => $count,
            ]
        ];
    }
}
