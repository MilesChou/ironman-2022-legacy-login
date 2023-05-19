<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\OAuth2Api;
use Ory\Hydra\Client\Model\AcceptOAuth2ConsentRequest;
use RuntimeException;

class AcceptConsent
{
    public function __invoke(Request $request, OAuth2Api $hydra): RedirectResponse
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

        $acceptConsentRequest = new AcceptOAuth2ConsentRequest([
            'grantScope' => array_keys($scopes),
            'remember' => true,
            'rememberFor' => 120,
        ]);

        Log::debug('Accept consent Request', json_decode((string)$acceptConsentRequest, true));

        try {
            $completedRequest = $hydra->acceptOAuth2ConsentRequest($consentChallenge, $acceptConsentRequest);
        } catch (\Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Consent Completed Request', json_decode((string)$completedRequest, true));

        return Redirect::away($completedRequest->getRedirectTo());
    }
}
