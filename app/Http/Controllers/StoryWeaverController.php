<?php

/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Http\Controllers;

use App\Models\UserProfile;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class StoryWeaverController extends Controller
{
    public function storyWeaverConfirmation($landing_page = 'storyweaver_default')
    {
        $user_profile = UserProfile::where('user_id', auth()->id())->first();

        $previous_url = URL::previous();
        session(
            [
                'previous_url' => $previous_url,
                'landing_page' => $landing_page,
            ]
        );

        if ($user_profile->visited_storyweaver_disclaimer) {
            return redirect(route('storyweaver-auth'));
        }
        $email = false;
        if (auth()->user()->email) {
            $email = true;
        }

        return view('storyweaver.confirmation', compact('email', 'landing_page'));
    }

    public function storyWeaverAuth(): RedirectResponse
    {
        $user_profile = UserProfile::where('user_id', auth()->id())->first();
        $user_profile->visited_storyweaver_disclaimer = true;
        $user_profile->save();
        $storyweaver_url = config('constants.storyweaver_url');
        $secret = config('storyweaver.config.secret');

        if (! $storyweaver_url or ! $secret) {
            Log::info(
                'StoryWeaver URL or Org Token not properly configured.'
            );
            abort(500);
        }

        $user = auth()->user();
        $user_email = $user->email;
        abort_unless($user_email, 405, __('You cannot access the Darkht-e Danesh StoryWeaver Library without a registered email.'));
        $user_id = $user->id;

        $user = UserProfile::where('user_id', $user_id)->first();
        $first_name = $user->first_name;
        $last_name = $user->last_name;

        $language = config('app.locale');
        $redirect_home = session()->pull('previous_url');
        if (! $redirect_home) {
            $redirect_home = URL::to('/');
        }

        $landing_page = session()->pull('landing_page');
        if ($landing_page) {
            if (array_key_exists($landing_page, config('constants'))) {
                $landing_page = config('constants.'.$landing_page);
            } else {
                $landing_page = config('constants.storyweaver_default');
            }
        }

        $client = app(Client::class);
        try {
            $response = $client->request(
                'POST', $storyweaver_url, [
                    'multipart' => [
                        [
                            'name' => 'org_token',
                            'contents' => $secret,
                        ],
                        [
                            'name' => 'firstname',
                            'contents' => $first_name,
                        ],
                        [
                            'name' => 'lastname',
                            'contents' => $last_name,
                        ],
                        [
                            'name' => 'email',
                            'contents' => $user_email,
                        ],
                        [
                            'name' => 'language_preferences',
                            'contents' => $language,
                        ],
                        [
                            'name' => 'locale_preferences',
                            'contents' => $language,
                        ],
                        [
                            'name' => 'landing_page',
                            'contents' => $landing_page,
                        ],
                        [
                            'name' => 'redirect_home',
                            'contents' => $redirect_home,
                        ],
                    ],
                ]
            );
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();

            Log::info(
                $first_name.' '.$last_name.' was unable to authenticate to StoryWeaver.
                Email: '.$user_email.' and user id: '.$user_id.'. Response: '.$status.'.'
            );

            abort(
                $status,
                __('Something went wrong while redirecting you to StoryWeaver.')
            );
        } catch (TransferException $e) {
            Log::info(
                $first_name.' '.$last_name.' was unable to authenticate to StoryWeaver. 
                Server 500.'
            );
            abort(500);
        }

        $response_contents = json_decode($response->getBody());
        if (! $response_contents) {
            Log::info('StoryWeaver returned an unparsable response body.');
            abort(500);
        }

        if (
            $response->getStatusCode() == 200
            && $response_contents->status == 'success'
            && $response_contents->redirect_url
        ) {
            return redirect()->away($response_contents->redirect_url);
        }

        Log::info(
            $first_name.' '.$last_name.' was not redirected to StoryWeaver.
                    Email: '.$user_email.' and user id: '.$user_id.'. 
                    Status code: '.$response->getStatusCode().'
                    Status: '.$response_contents->status.'
                    Redirect URL: '.$response_contents->redirect_url
        );

        return redirect(route('home'));
    }
}
