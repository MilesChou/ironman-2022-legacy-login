<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\OAuth2Api;
use Ory\Hydra\Client\Model\AcceptOAuth2ConsentRequest;
use RuntimeException;

class ConsentProvider
{
    public function __invoke(Request $request, OAuth2Api $hydra)
    {
        $consentChallenge = $request->input('consent_challenge');

        if (empty($consentChallenge)) {
            throw new RuntimeException('No consent_challenge');
        }

        $consentRequest = $hydra->getOAuth2ConsentRequest($consentChallenge);

        Log::debug('Get consent Request', json_decode((string)$consentRequest, true));

        if ($consentRequest->getSkip()) {
            Log::debug('Skip Login Provider');

            $acceptConsentRequest = new AcceptOAuth2ConsentRequest([
                'grantScope' => $consentRequest->getRequestedScope(),
            ]);

            try {
                $completedRequest = $hydra->acceptOAuth2ConsentRequest($consentChallenge, $acceptConsentRequest);
            } catch (\Throwable $e) {
                throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
            }

            Log::debug('Consent Completed Request', json_decode((string)$completedRequest, true));

            return Redirect::away($completedRequest->getRedirectTo());
        }

        return view('auth.consent', [
            'challenge' => $consentChallenge,
            'scopes' => $consentRequest->getRequestedScope(),
        ]);
    }
}
