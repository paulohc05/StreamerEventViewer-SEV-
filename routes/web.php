<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group([
    'prefix' => 'api'
], function ($router) {
    $router->get('/auth', 'AuthTwitchController@getAuthURL');
    $router->get('/channel/{keyword}', 'TwitchApiController@getChannel');
    $router->get('/streamers', 'TwitchApiController@getStreamers');
    $router->get('/streamer/{twitchID}/{accessToken}', 'TwitchApiController@getStreamer');
    $router->get('/callback', 'TwitchApiController@callback');
});

$router->get('/{route:.*}/', function () use ($router) {
    return view('home');
});
