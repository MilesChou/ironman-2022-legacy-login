<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Jose\Component\Checker\AudienceChecker;
use Jose\Component\Checker\ClaimCheckerManagerFactory;
use Jose\Component\Checker\ExpirationTimeChecker;
use Jose\Component\Checker\IssuedAtChecker;
use Jose\Component\Checker\IssuerChecker;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSLoader;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Api\PublicApi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PublicApi::class, function () {
            return tap(new PublicApi(), function (PublicApi $instance) {
                $instance->getConfig()
                    ->setHost('http://127.0.0.1:4444')
                    ->setUsername('my-rp')
                    ->setPassword('my-secret')
                    ->setAccessToken(null);
            });
        });

        $this->app->singleton(AdminApi::class, function () {
            return tap(new AdminApi(), function (AdminApi $instance) {
                $instance->getConfig()->setHost('http://127.0.0.1:4445');
            });
        });

        $this->app->singleton(ClaimCheckerManagerFactory::class, function () {
            return tap(new ClaimCheckerManagerFactory(), function (ClaimCheckerManagerFactory $instance) {
                $instance->add('aud', new AudienceChecker('my-rp'));
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
                    new RS256()
                ])),
                null,
            );
        });

        // 目前 Key 只有一把，所以先偷懶這樣寫
        $this->app->singleton(JWK::class, function () {
            $jwkJson = '{
  "use": "sig",
  "kty": "RSA",
  "kid": "public:hydra.openid.id-token",
  "alg": "RS256",
  "n": "3fLBH5AZuoJurOEDA8_MAodU9slUs7AQaeus3C6C7JdSpo7JjgyNMgNV5Fnu53gQlY3Pr5ZyWpfmzJwIFRLrfvT-iQcktXjnZIcFvkX67nAwoUiqBoppprQyTju56ZxrAZnLLr8CYpaDKIjrJkFQw5BWX2X00DIo_YjG_2AJkdlxGuCtFhaUl0VpPr7PmVTxroscagtWdRbb6bitwlkcyc-0ESP2NRIWp2erQ5FJeigPtyGfqSpXUAFbgfz3-koTBpcyf73FRc3BqkuOmAsUJWHl-7s9u8pDK_H9dq-Cg_hWqGohWc_oaA0_01-um647xkMvm4FLA4UH-h1pOiZoL5hyqNGF3FRcBoOLJcFqb4P3zq22sW28dluEEht2_WV3nxAHttHD3Sxbq4uMtjVucBjTwS8x4EVUvipqQ8z-jV386v9bG2xvx6KgUEMyPOsSAYI6ww6HDrlDHBXi1Fr0x7b9bPvlJe9MtLEvFTMe8UgmrcXOJO-xu4EN5HwH6wtnnnsYuw-0duiLL0mvE0AeXZurQy_u_vbh-thkTLkdQFBY93cY3yLcp0sll2FpXSrGNtZddX3x4yIDMQLbYqUzybiVbsohhu7xSYowTX77xIZobGxnuNpbGa857RD9zox9ugSh59Yq9qr4TC2DLAunXQEaalijUjr4sYIV6NCtrRk",
  "e": "AQAB"
}';

            return JWK::createFromJson($jwkJson);
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
