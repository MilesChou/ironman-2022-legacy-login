<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Ory\Hydra\Client\Api\PublicApi;

class HydraCallback
{
    public function __invoke(Request $request, PublicApi $hydra)
    {
        $error = $request->input('error');

        if (null !== $error) {
            return match ($error) {
                'access_denied' => response('使用者拒絕授權'),
                default => response('未知的 error: ' . $error),
            };
        }

        $redirectUri = 'http://127.0.0.1:8000/callback';

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

        dump(json_decode((string)$tokenResponse, true));

        return response('拿到身分驗證回應了');
    }
}
