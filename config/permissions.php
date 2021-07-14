<?php

return [
    [
        'name' => 'Comments',
        'flag' => 'comment.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'comment.create',
        'parent_flag' => 'comment.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'comment.edit',
        'parent_flag' => 'comment.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'comment.destroy',
        'parent_flag' => 'comment.index',
    ],
];
