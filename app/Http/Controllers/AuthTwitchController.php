<?php

namespace App\Http\Controllers;

use NewTwitchApi;

class AuthTwitchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getAuthURL()
    {
        $twitchApi = new NewTwitchApi\NewTwitchApi(new NewTwitchApi\HelixGuzzleClient(
            env('TWITCH_CLIENT_ID')), env('TWITCH_CLIENT_ID'), env('TWITCH_SECRET')
        );

        $redirectUri = url('/', [], true) . 'redirecturi';

        return $twitchApi->getOauthApi()->getAuthURL($redirectUri, 'token', 'user:edit+channel_read+channel:read:subscriptions');
    }
}
