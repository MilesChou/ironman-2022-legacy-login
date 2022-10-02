<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ory\Hydra\Client\Api\AdminApi;

/**
 * 檢查 Token
 */
class OAuth2TokenIntrospect extends Command
{
    protected $signature = 'oauth2:token:introspect {token}';

    protected $description = 'Introspect OAuth 2.0 Token by Admin API';

    public function handle(AdminApi $admin): int
    {
        $token = $this->argument('token');

        dump($admin->introspectOAuth2Token($token));

        return 0;
    }
}
