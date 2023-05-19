<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Ory\Hydra\Client\Api\OAuth2Api;

/**
 * 透過 Client Credentials Grant 產生 Token
 */
class OAuth2Token extends Command
{
    protected $signature = 'oauth2:token {--debug} {--introspect}';

    protected $description = 'Generate OAuth 2.0 by Client Credentials Grant';

    public function handle(OAuth2Api $hydra): int
    {
        $tokenEndpoint = 'http://127.0.0.1:4444/oauth2/token';

        $tokenResponse = Http::asForm()
            ->withBasicAuth(config('hydra.client_id'), config('hydra.client_secret'))
            ->post($tokenEndpoint, [
                'grant_type' => 'client_credentials',
            ]);

        if ($this->option('debug')) {
            dump($tokenResponse);
        }

        $token = $tokenResponse->json('access_token');

        $this->line($token);

        if ($this->option('introspect')) {
            dump($hydra->introspectOAuth2Token($token));
        }

        return 0;
    }
}
