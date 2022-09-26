<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ory\Hydra\Client\Api\AdminApi;
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

        return view('auth.login', [
            'challenge' => $loginChallenge,
        ]);
    }
}
