<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jose\Component\Checker\ClaimCheckerManagerFactory;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\JWSLoader;

class JwtCheck extends Command
{
    protected $signature = 'jwt:check {--ignore-time-checker} {jwt}';

    protected $description = 'Check JWT';

    public function handle(
        JWK $jwk,
        JWSLoader $loader,
        ClaimCheckerManagerFactory $claimCheckerManagerFactory,
    ) {
        $jwt = $this->argument('jwt');

        $jws = $loader->loadAndVerifyWithKey($jwt, $jwk, $signature);

        if ($this->option('ignore-time-checker')) {
            $claimCheckerManager = $claimCheckerManagerFactory->create(['aud', 'iss']);
        } else {
            $claimCheckerManager = $claimCheckerManagerFactory->create(['aud', 'exp', 'iat', 'iss']);
        }

        $claimCheckerManager->check(json_decode($jws->getPayload(), true));

        return 0;
    }
}
