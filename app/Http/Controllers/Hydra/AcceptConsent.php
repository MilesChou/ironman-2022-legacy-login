<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Model\AcceptConsentRequest;
use RuntimeException;

class AcceptConsent
{
    public function __invoke(Request $request, AdminApi $adminApi): RedirectResponse
    {
        $consentChallenge = $request->input('challenge');

        if (empty($consentChallenge)) {
            throw new RuntimeException('No consent_challenge');
        }

        $scopes = $request->input('scopes');

        if (empty($scopes)) {
            // 沒填 scope 就請回去按 reject 的按鈕
            return Redirect::back();
        }

        $acceptConsentRequest = new AcceptConsentRequest([
            'grantScope' => array_keys($scopes),
            'remember' => true,
            'rememberFor' => 120,
        ]);

        Log::debug('Accept consent Request', json_decode((string)$acceptConsentRequest, true));

        try {
            $completedRequest = $adminApi->acceptConsentRequest($consentChallenge, $acceptConsentRequest);
        } catch (\Throwable $e) {
            dd($e);
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Consent Completed Request', json_decode((string)$completedRequest, true));

        return Redirect::away($completedRequest->getRedirectTo());
    }
}
