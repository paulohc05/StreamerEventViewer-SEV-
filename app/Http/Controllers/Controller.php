<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use NewTwitchApi;

class Controller extends BaseController
{
    protected $newTwitchApi;
    protected $helixGuzzleClient;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * Using Twitch API client for PHP (https://github.com/nicklaw5/twitch-api-php)
         */
        $this->helixGuzzleClient = new NewTwitchApi\HelixGuzzleClient(env('TWITCH_CLIENT_ID'));
        $this->newTwitchApi = new NewTwitchApi\NewTwitchApi($this->helixGuzzleClient, env('TWITCH_CLIENT_ID'), env('TWITCH_SECRET'));
    }
}
