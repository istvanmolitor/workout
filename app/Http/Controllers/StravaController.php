<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

class StravaController extends Controller
{
    /**
     * Redirect the user to Strava's OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        /** @var AbstractProvider $provider */
        $provider = Socialite::driver('strava');

        return $provider
            ->scopes(['activity:read_all', 'profile:read_all'])
            ->redirect();
    }

    /**
     * Handle the callback from Strava and connect the account to the current user.
     */
    public function callback(): RedirectResponse
    {
        /** @var SocialiteUser $stravaUser */
        $stravaUser = Socialite::driver('strava')->user();

        $user = Auth::user();

        $user->strava_id = $stravaUser->getId();
        $user->strava_token = $stravaUser->token;
        $user->strava_refresh_token = $stravaUser->refreshToken;
        $user->strava_token_expires_at = now()->addSeconds($stravaUser->expiresIn);
        $user->save();

        return redirect()->route('strava.edit')->with('status', 'strava-connected');
    }
}
