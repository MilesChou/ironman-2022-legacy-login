<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ory\Hydra\Client\Api\OAuth2Api;

/**
 * 檢查 Token
 */
class OAuth2TokenIntrospect extends Command
{
    protected $signature = 'oauth2:token:introspect {token}';

    protected $description = 'Introspect OAuth 2.0 Token by Admin API';

    public function handle(OAuth2Api $admin): int
    {
        $token = $this->argument('token');

        dump($admin->introspectOAuth2Token($token));

        return 0;
    }
}
