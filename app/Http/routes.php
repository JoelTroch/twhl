<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

Route::get('/home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',

    'forum' => 'Forum\ForumController',
    'thread' => 'Forum\ThreadController',
    'post' => 'Forum\PostController',

    'wiki' => 'Wiki\WikiController',
    'vault' => 'Vault\VaultController',
    'comment' => 'Comments\CommentController',
    'news' => 'News\NewsController',

    'api' => 'Api\ApiController',
]);
