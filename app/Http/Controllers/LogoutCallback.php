<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogoutCallback
{
    public function __invoke(Request $request)
    {
        $error = $request->input('error');

        if (null !== $error) {
            dd($request->all());
        }

        return response('登出成功');
    }
}
