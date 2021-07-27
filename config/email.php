<?php

return [
    'name'        => 'plugins/comment::settings.email.templates.title',
    'description' => 'plugins/comment::settings.email.templates.description',
    'templates'   => [
        'new-reply' => [
            'title'       => 'plugins/comment::settings.email.templates.to_user.title',
            'description' => 'plugins/comment::settings.email.templates.to_user.description',
            'subject'     => '{{ site_title }}: New reply to a comment on {{ post_name }}',
            'can_off'     => true,
        ],
        'new-comment' => [
            'title'       => 'plugins/comment::settings.email.templates.to_poster.title',
            'description' => 'plugins/comment::settings.email.templates.to_poster.description',
            'subject'     => '{{ site_title }}: New comment to {{ post_name }}',
            'can_off'     => true,
            'enabled'     => false,
        ],
    ],
    'variables'   => [
        'user_name'       => 'User\'s name was responded to the article',
        'post_name'       => 'The article name',
        'post_link'       => 'The article URL',
        'comment_content' => 'The content of comment',
    ],
];
