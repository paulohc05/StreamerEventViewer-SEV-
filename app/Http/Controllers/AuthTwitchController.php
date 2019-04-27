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
        parent::__construct();
    }

    public function getAuthURL()
    {
        $responseType = "token";
        $scope = "user:edit+channel_read+channel:read:subscriptions";

        $redirectUri = url('/', [], true) . '/#/dashboard';

        // Generate a full authentication URL with client-id, redirect-uri, type of response, and the scopes.
        // Which will redirect User to log in using their Twitch account.
        // Once the User is successfully logged in, will be redirected them to the Dashboard page.
        return $this->newTwitchApi->getOauthApi()->getAuthURL(urlencode($redirectUri), $responseType, $scope);
    }
}
