<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\OAuth2Api;
use Ory\Hydra\Client\Model\RejectOAuth2Request;
use RuntimeException;

class RejectConsent
{
    public function __invoke(Request $request, OAuth2Api $hydra): RedirectResponse
    {
        $consentChallenge = $request->input('challenge');

        if (empty($consentChallenge)) {
            throw new RuntimeException('No consent_challenge');
        }

        $rejectRequest = new RejectOAuth2Request([
            'error' => 'access_denied',
            'errorDescription' => 'The request was rejected by end-user',
        ]);

        Log::debug('Reject consent Request', json_decode((string)$rejectRequest, true));

        try {
            $completedRequest = $hydra->rejectOAuth2ConsentRequest($consentChallenge, $rejectRequest);
        } catch (\Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Consent Completed Request', json_decode((string)$completedRequest, true));

        return Redirect::away($completedRequest->getRedirectTo());
    }
}
