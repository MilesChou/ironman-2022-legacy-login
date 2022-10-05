<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Model\AcceptLoginRequest;
use RuntimeException;
use Throwable;

class LoginProvider
{
    public function __invoke(Request $request, AdminApi $adminApi)
    {
        $loginChallenge = $request->get('login_challenge');

        if (empty($loginChallenge)) {
            throw new RuntimeException('No login_challenge');
        }

        try {
            $loginRequest = $adminApi->getLoginRequest($loginChallenge);
        } catch (Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Login Request', json_decode((string)$loginRequest, true));

        if ($loginRequest->getSkip()) {
            Log::debug('Skip Login Provider');

    $acceptLoginRequest = new AcceptLoginRequest([
        'subject' => $loginRequest->getSubject(),
    ]);

            try {
                $completedRequest = $adminApi->acceptLoginRequest($loginChallenge, $acceptLoginRequest);
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
