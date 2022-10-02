<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Api\PublicApi;

/**
 * 透過 Client Credentials Grant 產生 Token
 */
class OAuth2Token extends Command
{
    protected $signature = 'oauth2:token {--debug} {--introspect}';

    protected $description = 'Generate OAuth 2.0 by Client Credentials Grant';

    public function handle(PublicApi $hydra, AdminApi $admin): int
    {
        $tokenResponse = $hydra->oauth2Token('client_credentials');

        if ($this->option('debug')) {
            dump($tokenResponse);
        }

        $token = $tokenResponse->getAccessToken();

        $this->line($token);

        if ($this->option('introspect')) {
            dump($admin->introspectOAuth2Token($token));
        }

        return 0;
    }
}
