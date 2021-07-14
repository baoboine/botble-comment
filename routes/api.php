<?php

if (defined('THEME_MODULE_SCREEN_NAME')) {

    Route::group([
        'prefix'     => 'api/v1',
        'namespace'  => 'Botble\Comment\Http\Controllers\API',
        'middleware' => ['api'],
    ], function () {

        if (!is_plugin_active('member'))
        {
            Route::group([
                'as'         => 'public.member.',
            ], function() {
                Route::post('login', 'LoginController@login')->name('login.post');
                Route::post('register', 'RegisterController@register')->name('register.post');
            });
        }

        Route::group([
            'as'         => 'comment.'
        ], function () {

            Route::group([
                'middleware' => ['auth:member-api', 'throttle:comment'],
            ], function() {

                Route::post('logout', 'LoginController@logout')->name('logout.post');

                Route::post('postComment', 'CommentFrontController@postComment')->name('post');
                Route::post('user', 'CommentFrontController@userInfo')->name('user');
                Route::delete('delete', 'CommentFrontController@deleteComment')->name('delete');

                Route::post('like', 'CommentFrontController@likeComment')->name('like');
                Route::post('change-avatar', 'CommentFrontController@changeAvatar')->name('update-avatar');
            });

            Route::get('getComments', 'CommentFrontController@getComments')->name('list');
        });

    });
}
