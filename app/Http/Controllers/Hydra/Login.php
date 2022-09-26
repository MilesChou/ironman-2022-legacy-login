<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Model\AcceptLoginRequest;
use RuntimeException;
use stdClass;

class Login
{
    public function __invoke(Request $request, AdminApi $adminApi)
    {
        $loginChallenge = $request->input('challenge');

        if (empty($loginChallenge)) {
            throw new RuntimeException('No login_challenge');
        }

        if (!Auth::once($request->only('email', 'password'))) {
            return Redirect::back();

//        $rejectRequest = new RejectRequest([
//            'error' => '...',
//
//            'error_description' => '...',
//        ]);
//
//        $completedRequest = $adminApi->acceptLoginRequest($loginChallenge, $rejectRequest);
//
//        return Redirect::away($completedRequest->getRedirectTo());
        }

        $user = Auth::user();

        $acceptLoginRequest = new AcceptLoginRequest([
            'context' => new stdClass(),
            'remember' => $request->boolean('remember'),
            'rememberFor' => 0,
            'subject' => (string)$user->getAuthIdentifier(),
        ]);

        Log::debug('Accept Login Request: ', json_decode((string)$acceptLoginRequest, true));

        try {
            $completedRequest = $adminApi->acceptLoginRequest($loginChallenge, $acceptLoginRequest);
        } catch (\Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Completed Request: ', json_decode((string)$completedRequest, true));

        return Redirect::away($completedRequest->getRedirectTo());
    }
}
