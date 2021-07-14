<?php

namespace Botble\Comment\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface CommentInterface extends RepositoryInterface
{
    /**
     * @param array $input
     * @return mixed
     */
    public function storageComment(array $input);

    /**
     * @param array $reference [reference_type, reference_id]
     * @param int $parentId
     * @param int $limit
     * @param int $page
     * @param string $sort
     * @return mixed
     */
    public function getComments(array $reference = [], int $parentId = 0, int $page = 1, int $limit = 20, string $sort = 'newest');
}
