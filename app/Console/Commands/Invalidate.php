<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Model\OAuth2Client;
use stdClass;

class Invalidate extends Command
{
    protected $signature = 'invalidate {sub}';

    protected $description = 'Invalidate subject';

    public function handle(AdminApi $admin)
    {
        $response = $admin->getJsonWebKey('private:hydra.openid.id-token', 'hydra.openid.id-token');
        $sub = $this->argument('sub');

        $privateKey = (array)$response->getKeys()[0]->jsonSerialize();

        $clients = $admin->listOAuth2Clients();

        collect($clients)
            ->filter(fn(OAuth2Client $client) => !empty($client->getBackchannelLogoutUri()))
            ->each(function (OAuth2Client $client) use ($sub, $privateKey) {
                $logoutToken = $this->buildLogoutToken($privateKey, $client->getClientId(), $sub);
                $uri = $client->getBackchannelLogoutUri();

                Http::post($uri, [
                    'logout_token' => $logoutToken,
                ]);
            });

        $admin->revokeAuthenticationSession($sub);

        return 0;
    }

    private function buildLogoutToken(array $privateKey, string $client, string $sub): string
    {
        $jwsBuilder = new JWSBuilder(new AlgorithmManager([
            new RS256(),
        ]));

        $jws = $jwsBuilder
            ->withPayload(json_encode([
                'aud' => [
                    $client,
                ],
                'events' => [
                    'http://schemas.openid.net/event/backchannel-logout' => new stdClass(),
                ],
                'iat' => time(),
                'iss' => 'http://127.0.0.1:4444/',
                'jti' => Str::uuid()->toString(),
                'sub' => $sub,
            ]))
            ->addSignature(new JWK($privateKey), ['alg' => 'RS256',])
            ->build();

        $serializer = new CompactSerializer();

        return $serializer->serialize($jws);
    }
}
