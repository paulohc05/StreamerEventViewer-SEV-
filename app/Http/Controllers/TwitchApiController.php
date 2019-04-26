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

        return response()->json($listOfStreamers->data);
    }

    public function getStreamer(string $twitchID, string $accessToken)
    {
        $twitchApi = new NewTwitchApi\NewTwitchApi(new NewTwitchApi\HelixGuzzleClient(
            env('TWITCH_CLIENT_ID')), env('TWITCH_CLIENT_ID'), env('TWITCH_SECRET')
        );

        // Get User Data
        try {
            $userData = $twitchApi->getUsersApi()->getUserById($twitchID)->getBody()->getContents();
            $userData = json_decode($userData);
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }

        // Get Streamer Data
        try {
            $streamData = $twitchApi->getStreamsApi()->getStreamForUserId($twitchID)->getBody()->getContents();
            $streamData = json_decode($streamData);
        }
        catch (\Exception $e) {
            return $e->getMessage();
        }

        $callbackUri = url('/', [], true) . '/api/callback';

        $twitchApi->getWebhooksSubscriptionApi()->subscribeToStream($twitchID, $accessToken, $callbackUri);

        return [
            'user_id' => $twitchID, 
            'display_name' => $userData->data[0]->display_name, 
            'profile_image_url' => $userData->data[0]->profile_image_url, 
            'title' => $streamData->data[0]->title, 
            'type' => $streamData->data[0]->type, 
            'language' => $streamData->data[0]->language, 
            'viewer_count' => $streamData->data[0]->viewer_count, 
            'thumbnail_url' => $streamData->data[0]->thumbnail_url
        ];
    }

    public function callback(Request $request)
    {
        if ('GET' === $request->method()) {
            Log::debug($request->query());
        } else {
            return response()->json(['error' => "The specified method for the request is invalid", 'code' => '405'], 405);
        }

        return response()->json(['message' => 'Success'], 200);
    }
}
