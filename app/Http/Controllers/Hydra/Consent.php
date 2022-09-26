<?php

namespace App\Http\Controllers\Hydra;

use Illuminate\Http\Request;

class Consent
{
    public function __invoke(Request $request)
    {
        dump($request->all());
        return 'OAuth 2.0 授權完成';
    }
}
