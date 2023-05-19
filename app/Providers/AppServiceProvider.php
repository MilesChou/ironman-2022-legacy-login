<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Jose\Component\Checker\ClaimCheckerManagerFactory;
use Jose\Component\Checker\ExpirationTimeChecker;
use Jose\Component\Checker\IssuedAtChecker;
use Jose\Component\Checker\IssuerChecker;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSLoader;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Ory\Hydra\Client\Api\OAuth2Api;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(OAuth2Api::class, function () {
            return tap(new OAuth2Api(), function (OAuth2Api $instance) {
                $instance->getConfig()
                    ->setHost('http://127.0.0.1:4445')
                    ->setAccessToken(null);
            });
        });

        $this->app->singleton(ClaimCheckerManagerFactory::class, function () {
            return tap(new ClaimCheckerManagerFactory(), function (ClaimCheckerManagerFactory $instance) {
                // $instance->add('aud', new AudienceChecker('my-rp'));
                $instance->add('exp', new ExpirationTimeChecker(10));
                $instance->add('iat', new IssuedAtChecker(10));
                $instance->add('iss', new IssuerChecker(['http://127.0.0.1:4444/']));
            });
        });

        $this->app->singleton(JWSLoader::class, function () {
            return new JWSLoader(
                new JWSSerializerManager([
                    new CompactSerializer(),
                ]),
                new JWSVerifier(new AlgorithmManager([
                    new RS256(),
                ])),
                null,
            );
        });

        // 目前 Key 只有一把，所以先偷懶這樣寫
        $this->app->singleton(JWKSet::class, function () {
            return JWKSet::createFromJson(
                Http::get('http://127.0.0.1:4444/.well-known/jwks.json')->body(),
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
