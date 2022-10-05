<?php

namespace App\Http\Controllers\Rp2;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jose\Component\Checker\ClaimCheckerManagerFactory;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\JWSLoader;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Api\PublicApi;

class Callback
{
    public function __invoke(
        Request $request,
        PublicApi $hydra,
        JWK $jwk,
        JWSLoader $loader,
        ClaimCheckerManagerFactory $claimCheckerManagerFactory,
    ) {
        $error = $request->input('error');

        if (null !== $error) {
            return match ($error) {
                'access_denied' => response('使用者拒絕授權'),
                default => response('未知的 error: ' . $error),
            };
        }

        $redirectUri = 'http://127.0.0.1:8000/rp2/callback';

        $hydra->getConfig()
            ->setUsername('rp2')
            ->setPassword('secret2');

        try {
            $tokenResponse = $hydra->oauth2Token(
                grantType: 'authorization_code',
                code: $request->input('code'),
                redirectUri: $redirectUri
            );
        } catch (\Throwable $e) {
            dump($e);
            return response('請求 Token 失敗');
        }

        Log::debug('Token Response: ', json_decode((string)$tokenResponse, true));

        dump(json_decode((string)$tokenResponse, true));

        $userinfoEndpoint = $hydra->getConfig()->getHost() . '/userinfo';

        $userInfo = Http::withToken($tokenResponse->getAccessToken())
            ->get($userinfoEndpoint);

        Log::debug('User info: ', $userInfo->json());

        $idToken = $tokenResponse->getIdToken();

        $jws = $loader->loadAndVerifyWithKey($idToken, $jwk, $signature);

        $claimCheckerManager = $claimCheckerManagerFactory->create(['exp', 'iat', 'iss']);

        $claimCheckerManager->check(json_decode($jws->getPayload(), true));

        dump(json_decode($jws->getPayload(), true));

        $request->session()->put('rp2.id_token', $idToken);

        return response('拿到身分驗證回應了');
    }
}
