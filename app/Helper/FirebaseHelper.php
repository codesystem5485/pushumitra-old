<?php

namespace App\Helper;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Cache;

class FirebaseHelper
{
    public static function getAccessToken()
    {
        return Cache::remember('firebase_access_token', 3500, function () {

            $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
            $credentials = new ServiceAccountCredentials(
                $scopes,
                storage_path('app/public/pashumitra-firebase-31dec2025.json')
            );

            $token = $credentials->fetchAuthToken();
            return $token['access_token'];
        });
    }
}
