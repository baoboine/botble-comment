<?php

return [
    'email' => [
        'templates' => [
            'title'       => 'Comment',
            'description' => 'Config notification email when have new response',
            'to_user'     => [
                'title'       => 'Email send to user',
                'description' => 'Template notification to user when have new reply',
            ],
            'to_poster'   => [
                'title'       => 'Email send to article\'s author',
                'description' => 'Template notification to article\'s author when have new comment',
            ],
        ],
    ],
];
