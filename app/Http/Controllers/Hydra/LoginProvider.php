<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\OAuth2Api;
use Ory\Hydra\Client\Model\AcceptOAuth2LoginRequest;
use RuntimeException;
use Throwable;

class LoginProvider
{
    public function __invoke(Request $request, OAuth2Api $hydra)
    {
        $loginChallenge = $request->get('login_challenge');

        if (empty($loginChallenge)) {
            throw new RuntimeException('No login_challenge');
        }

        try {
            $loginRequest = $hydra->getOAuth2LoginRequest($loginChallenge);
        } catch (Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Login Request', json_decode((string)$loginRequest, true));

        if ($loginRequest->getSkip()) {
            Log::debug('Skip Login Provider');

    $acceptLoginRequest = new AcceptOAuth2LoginRequest([
        'subject' => $loginRequest->getSubject(),
    ]);

            try {
                $completedRequest = $hydra->acceptOAuth2LoginRequest($loginChallenge, $acceptLoginRequest);
            } catch (\Throwable $e) {
                throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
            }

            Log::debug('Completed Request: ', json_decode((string)$completedRequest, true));

            return Redirect::away($completedRequest->getRedirectTo());
        }

        return view('auth.login', [
            'challenge' => $loginChallenge,
        ]);
    }
}
