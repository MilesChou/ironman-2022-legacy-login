<?php

namespace App\Http\Controllers\Rp1;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class Logout
{
    public function __invoke(Request $request): RedirectResponse
    {
        $idToken = $request->session()->get('rp1.id_token');

        if (null === $idToken) {
            throw new \RuntimeException('No login session');
        }

        $query = Arr::query([
            'client_id' => 'rp1',
            'id_token_hint' => $idToken,
            'post_logout_redirect_uri' => 'http://127.0.0.1:8000/rp1/logout/callback',
            'state' => '1a2b3c4d',
        ]);

        $endSessionEndpoint = 'http://127.0.0.1:4444/oauth2/sessions/logout';

        $LogoutRequest = $endSessionEndpoint . '?' . $query;

        Log::info('End session request: ' . $LogoutRequest);

        return Redirect::away($LogoutRequest);
    }
}
