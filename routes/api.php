<?php

if (defined('THEME_MODULE_SCREEN_NAME')) {

    Route::group([
        'prefix'     => 'api/v1/comments',
        'namespace'  => 'Botble\Comment\Http\Controllers\API',
        'middleware' => ['api'],
    ], function () {

        Route::group([
            'as' => 'public.comment.',
        ], function () {
            Route::post('login', 'LoginController@login')->name('login');
            Route::post('register', 'RegisterController@register')->name('register');
        });

        Route::group([
            'as' => 'comment.',
        ], function () {

            Route::group([
                'middleware' => ['auth:comment-api', 'throttle:comment'],
            ], function () {

                Route::post('logout', 'LoginController@logout')->name('logout');

                Route::post('postComment', 'CommentFrontController@postComment')->name('post');
                Route::post('user', 'CommentFrontController@userInfo')->name('user');
                Route::delete('delete', 'CommentFrontController@deleteComment')->name('delete');

                Route::post('like', 'CommentFrontController@likeComment')->name('like');
                Route::post('change-avatar', 'CommentFrontController@changeAvatar')->name('update-avatar');

                Route::post('recommend', 'CommentFrontController@recommend')->name('recommend');
            });

            Route::get('getComments', 'CommentFrontController@getComments')->name('list');
        });

    });
}
