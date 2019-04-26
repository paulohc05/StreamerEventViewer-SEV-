<?php

namespace App\Http\Controllers;

use NewTwitchApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwitchApiController extends Controller
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

    public function getStreamers()
    {
        $twitchApi = new NewTwitchApi\NewTwitchApi(new NewTwitchApi\HelixGuzzleClient(
            env('TWITCH_CLIENT_ID')), env('TWITCH_CLIENT_ID'), env('TWITCH_SECRET')
        );

        try {
            $listOfStreamers = $twitchApi->getStreamsApi()->getStreams()->getBody()->getContents();
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }

        $listOfStreamers = json_decode($listOfStreamers);

        Log::debug($listOfStreamers->data);
        return response()->json($listOfStreamers->data);
    }

    public function getStreamer(string $twitchID, string $accessToken)
    {
        $twitchApi = new NewTwitchApi\NewTwitchApi(new NewTwitchApi\HelixGuzzleClient(
            env('TWITCH_CLIENT_ID')), env('TWITCH_CLIENT_ID'), env('TWITCH_SECRET')
        );

        $callbackUri = url('/', [], true) . '/api/callback';

        $twitchApi->getWebhooksSubscriptionApi()->subscribeToStream($twitchID, $accessToken, $callbackUri);
    }

    public function callback(Request $request)
    {
        die('Great Scott! Callback!');
    }
}
