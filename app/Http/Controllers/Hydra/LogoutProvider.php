<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\OAuth2Api;
use RuntimeException;
use Throwable;

class LogoutProvider
{
    public function __invoke(Request $request, OAuth2Api $hydra)
    {
        $logoutChallenge = $request->get('logout_challenge');

        if (empty($logoutChallenge)) {
            throw new RuntimeException('No login_challenge');
        }

        try {
            $logoutRequest = $hydra->getOAuth2LogoutRequest($logoutChallenge);
        } catch (Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Logout Request', json_decode((string)$logoutRequest, true));

        try {
            $completedRequest = $hydra->acceptOAuth2LogoutRequest($logoutChallenge);
        } catch (Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Logout Completed Request', json_decode((string)$completedRequest, true));

        return Redirect::away($completedRequest->getRedirectTo());
    }
}
