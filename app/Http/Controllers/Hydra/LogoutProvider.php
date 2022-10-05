<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\AdminApi;
use RuntimeException;
use Throwable;

class LogoutProvider
{
    public function __invoke(Request $request, AdminApi $adminApi)
    {
        $logoutChallenge = $request->get('logout_challenge');

        if (empty($logoutChallenge)) {
            throw new RuntimeException('No login_challenge');
        }

        try {
            $logoutRequest = $adminApi->getLogoutRequest($logoutChallenge);
        } catch (Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Logout Request', json_decode((string)$logoutRequest, true));

        try {
            $completedRequest = $adminApi->acceptLogoutRequest($logoutChallenge);
        } catch (Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Logout Completed Request', json_decode((string)$completedRequest, true));

        return Redirect::away($completedRequest->getRedirectTo());
    }
}
