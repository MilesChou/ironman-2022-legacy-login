<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ory\Hydra\Client\Api\OAuth2Api;

/**
 * Revoke Token
 */
class OAuth2TokenRevoke extends Command
{
    protected $signature = 'oauth2:token:revoke {token}';

    protected $description = 'Revoke OAuth 2.0 Token by Public API';

    public function handle(OAuth2Api $public): int
    {
        $token = $this->argument('token');

        $public->revokeOAuth2Token($token);

        $this->line('Revoke done.');

        return 0;
    }
}
