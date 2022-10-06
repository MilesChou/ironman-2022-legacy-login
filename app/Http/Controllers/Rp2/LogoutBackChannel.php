<?php

namespace App\Http\Controllers\Rp2;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response as ResponseFactory;

class LogoutBackChannel
{
    public function __invoke(Request $request): Response
    {
        $logoutToken = $request->input('logout_token');

        Log::debug('Logout Token for RP2: ', [
            'token' => $logoutToken,
        ]);

        return ResponseFactory::make();
    }
}
