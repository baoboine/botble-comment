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

    // Users
    [
        'name' => 'Comment users',
        'flag' => 'comment-user.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'comment-user.create',
        'parent_flag' => 'comment-user.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'comment-user.edit',
        'parent_flag' => 'comment-user.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'comment-user.destroy',
        'parent_flag' => 'comment-user.index',
    ],

];
