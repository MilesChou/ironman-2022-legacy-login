<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Model\RejectRequest;
use RuntimeException;

class RejectConsent
{
    public function __invoke(Request $request, AdminApi $adminApi): RedirectResponse
    {
        $consentChallenge = $request->input('challenge');

        if (empty($consentChallenge)) {
            throw new RuntimeException('No consent_challenge');
        }

        $rejectRequest = new RejectRequest([
            'error' => 'access_denied',
            'errorDescription' => 'The request was rejected by end-user',
        ]);

        Log::debug('Reject consent Request', json_decode((string)$rejectRequest, true));

        try {
            $completedRequest = $adminApi->rejectConsentRequest($consentChallenge, $rejectRequest);
        } catch (\Throwable $e) {
            throw new RuntimeException('Hydra Server error: ' . $e->getMessage());
        }

        Log::debug('Consent Completed Request', json_decode((string)$completedRequest, true));

        return Redirect::away($completedRequest->getRedirectTo());
    }
}
