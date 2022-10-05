<?php

namespace App\Http\Controllers\Rp2;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class Login
{
    public function __invoke(): RedirectResponse
    {
        $authorizeUri = 'http://127.0.0.1:4444/oauth2/auth';

        $query = Arr::query([
            'client_id' => 'rp2',
            'redirect_uri' => 'http://127.0.0.1:8000/rp2/callback',
            'scope' => 'openid',
            'response_type' => 'code',
            'state' => '1a2b3c4d',
        ]);

        $authenticationRequest = $authorizeUri . '?' . $query;

        Log::info('Authentication Request: ' . $authenticationRequest);

        return Redirect::away($authenticationRequest);
    }
}
