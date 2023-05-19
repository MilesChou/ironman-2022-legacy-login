<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jose\Component\Checker\ClaimCheckerManagerFactory;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\JWSLoader;
use Ory\Hydra\Client\Api\OAuth2Api;

class HydraCallback
{
    public function __invoke(
        Request $request,
        OAuth2Api $hydra,
        JWKSet $jwkSet,
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

        $redirectUri = 'http://127.0.0.1:8000/callback';

        $tokenEndpoint = 'http://127.0.0.1:4444/oauth2/token';

        try {
            $tokenResponse = Http::asForm()
                ->withBasicAuth(config('hydra.client_id'), config('hydra.client_secret'))
                ->post($tokenEndpoint, [
                    'grant_type' => 'authorization_code',
                    'code' => $request->input('code'),
                    'redirect_uri' => $redirectUri,
                ]);

            if ($tokenResponse->status() !== 200) {
                dd($tokenResponse->json());
            }
        } catch (\Throwable $e) {
            dump($e);
            return response('請求 Token 失敗');
        }

        Log::debug('Token Response: ', $tokenResponse->json());

        dump(json_decode((string)$tokenResponse, true));

        $accessToken = $tokenResponse->json('access_token');
        $idToken = $tokenResponse->json('id_token');

        $userinfoEndpoint = 'http://127.0.0.1:4444/userinfo';

        $userInfo = Http::withToken($accessToken)
            ->get($userinfoEndpoint);

        Log::debug('User info: ', $userInfo->json());

        $introspectToken = $hydra->introspectOAuth2Token($accessToken);

        Log::debug('Token Introspection: ', json_decode((string)$introspectToken, true));


        $jws = $loader->loadAndVerifyWithKeySet($idToken, $jwkSet, $signature);

        $claimCheckerManager = $claimCheckerManagerFactory->create(['exp', 'iat', 'iss']);

        $claimCheckerManager->check(json_decode($jws->getPayload(), true));

        $request->session()->put('id_token', $idToken);

        return response('拿到身分驗證回應了');
    }
}
