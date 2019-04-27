<?php

namespace App\Http\Controllers;

// use NewTwitchApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;

class TwitchApiController extends Controller
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

    /**
     * New Twitch API Reference: https://dev.twitch.tv/docs/api/reference/
     */
    public function getStreamers()
    {
        try {
            // Make the API call to get Streamers.
            $listOfStreamers = $this->newTwitchApi->getStreamsApi()->getStreams()->getBody()->getContents();
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }

        $listOfStreamers = json_decode($listOfStreamers);

        return response()->json($listOfStreamers->data);
    }

    /**
     * New Twitch API Reference: https://dev.twitch.tv/docs/api/reference/
     * Getting User Data by keyword
     */
    public function getChannel(string $keyword)
    {
        // Get User Data by keyword
        try {
            // Make the API call to get User Data by keyword and return it as JSON.
            $userData = $this->newTwitchApi->getUsersApi()->getUserByUsername($keyword)->getBody()->getContents();
            $userData = json_decode($userData);
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
        
        // Return the User Data as JSON if found, yet return null if the keyword is not matched.
        return response()->json($userData->data ? $userData->data[0] : null);
    }

    /**
     * New Twitch API Reference: https://dev.twitch.tv/docs/api/reference/
     */
    public function getStreamer(string $twitchID, string $accessToken)
    {
        // Get User Data
        try {
            // Make the API call to get User Data which will be combined with Stream Data and return it as JSON.
            $userData = $this->newTwitchApi->getUsersApi()->getUserById($twitchID)->getBody()->getContents();
            $userData = json_decode($userData);
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }

        // Get Streamer Data
        try {
            // Make the API call to get Stream Data which will be combined with User Data and return it as JSON.
            $streamData = $this->newTwitchApi->getStreamsApi()->getStreamForUserId($twitchID)->getBody()->getContents();
            $streamData = json_decode($streamData);
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }

        $callbackUri = url('/', [], true) . '/api/callback';

        // Gets the Webhook subscriptions of a user identified by a User ID and Access Token.
        // Return stream broadcast by specified user ID.
        $this->newTwitchApi->getWebhooksSubscriptionApi()->subscribeToStream($twitchID, $accessToken, $callbackUri);

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

    /**
     * The callback that will be provided for the API subscription.
     */
    public function callback(Request $request)
    {
        if ('GET' === $request->method()) {
            // At the moment, I put it in Log.
            Log::debug($request->query());
        } else {
            return response()->json(['error' => "The specified method for the request is invalid", 'code' => '405'], 405);
        }

        return response()->json(['message' => 'Success'], 200);
    }
}
