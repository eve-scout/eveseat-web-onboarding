<?php

/*
 * This file is part of Onboarding SeAT Add-on.
 *
 * Copyright (c) 2016 Johnny Splunk <johnnysplunk@eve-scout.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::group([
    'namespace' => 'EveScout\Seat\Web\Onboarding\Http\Controllers\Api',
], function () {
    Route::group(['middleware' => ['auth', 'locale']], function () {
        Route::group(['middleware' => 'mfa'], function () {
            Route::post('/api-key/check', [
                'as'   => 'api.key.check',
                'uses' => 'KeyController@checkKey'
            ]);

            Route::post('/api-key/add', [
                'as'   => 'api.key.add',
                'uses' => 'KeyController@addKey'
            ]);
        });
    });
});

Route::group([
    'namespace' => 'EveScout\Seat\Web\Onboarding\Http\Controllers\Auth',
    'middleware' => ['requirements', 'registration.status'],
    'prefix' => 'auth'
    ], function () {

    Route::get('confirm/{confirmation_token}', [
        'as'   => 'auth.register.confirm',
        'uses' => 'ConfirmController@getConfirm'
    ]);

    Route::post('register', [
        'as'   => 'auth.register.post',
        'uses' => 'RegisterController@postRegister'
    ]);
});
