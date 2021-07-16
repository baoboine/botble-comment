<?php

return [
    'name' => 'plugins/comment::settings.email.templates.title',
    'description' => 'plugins/comment::settings.email.templates.description',
    'templates' => [
        'notify_email' => [
            'title' => 'plugins/comment::settings.email.templates.to_user.title',
            'description' => 'plugins/comment::settings.email.templates.to_user.description',
            'subject' => '{{ site_title }}: New reply to a comment on {{ post_name }}',
            'can_off' => true,
        ],
    ],
    'variables' => [
        'user_name' => 'User\'s name was responded to the article',
        'post_name' => 'The article name',
        'post_link' => 'The article URL',
        'comment_content' => 'The content of comment'
    ],
];
