<?php

if (defined('THEME_MODULE_SCREEN_NAME')) {

    Route::group([
        'prefix'     => 'ajax/v1/comments',
        'middleware' => ['web'],
    ], function () {

        Route::group([
            'namespace' => 'Botble\Comment\Http\Controllers\AJAX',
        ], function () {

            Route::group([
                'as' => 'public.comment.',
            ], function () {

                Route::post('login', 'LoginController@login')->name('login');
                Route::post('register', 'RegisterController@register')->name('register');
            });

            Route::post('logout', [
                'uses'       => 'LoginController@logout',
                'middleware' => 'auth:' . COMMENT_GUARD,
            ])->name('comment.logout');

        });

        Route::group([
            'as'        => 'comment.',
            'namespace' => 'Botble\Comment\Http\Controllers\API',
        ], function () {

            Route::group([
                'middleware' => ['auth:' . COMMENT_GUARD, 'throttle:comment'],
            ], function () {

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
