<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ory\Hydra\Client\Api\AdminApi;
use RuntimeException;

class ConsentProvider
{
    public function __invoke(Request $request, AdminApi $adminApi)
    {
        $consentChallenge = $request->input('consent_challenge');

        if (empty($consentChallenge)) {
            throw new RuntimeException('No consent_challenge');
        }

        $consentRequest = $adminApi->getConsentRequest($consentChallenge);

        Log::debug('Get consent Request', json_decode((string)$consentRequest, true));

        return view('auth.consent', [
            'challenge' => $consentChallenge,
            'scopes' => $consentRequest->getRequestedScope(),
        ]);
    }
}
