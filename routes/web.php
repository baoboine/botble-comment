<?php

Route::group(['namespace' => 'Botble\Comment\Http\Controllers', 'middleware' => ['web', 'core']], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'comments', 'as' => 'comment.'], function () {
            Route::resource('', 'CommentController')->parameters(['' => 'comment']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'CommentController@deletes',
                'permission' => 'comment.destroy',
            ]);

            Route::post('save/setting', [
                'as'        => 'storage-settings',
                'uses'      => 'CommentController@storeSettings',
                'permission'    => 'setting.options'
            ]);

            Route::post('login/current', 'CommentController@cloneUser')->name('current-user');

        });

        Route::get('comment/settings', 'CommentController@getSettings')->name('comment.setting');
    });

});
