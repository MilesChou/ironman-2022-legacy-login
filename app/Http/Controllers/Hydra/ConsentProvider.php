<?php

namespace App\Http\Controllers\Hydra;

class ConsentProvider
{
    public function __invoke()
    {
        return view('auth.consent');
    }
}
