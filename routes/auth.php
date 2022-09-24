<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Ory\Hydra\Client\Api\AdminApi;
use Ory\Hydra\Client\Model\AcceptLoginRequest;
use Ory\Hydra\Client\Model\RejectRequest;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', function () {
        $authorizeUri = 'http://127.0.0.1:4444/oauth2/auth';

        $query = \Illuminate\Support\Arr::query([
            'client_id' => 'my-rp',
            'redirect_uri' => 'http://127.0.0.1:8000/callback',
            'scope' => 'openid',
            'response_type' => 'code',
            'state' => '1a2b3c4d',
        ]);

        $authenticationRequest = $authorizeUri . '?' . $query;

        Log::info('Authentication Request: ' . $authenticationRequest);

        return redirect($authenticationRequest);
    })->name('login');

    Route::get('callback', function () {
        dump(request()->all());
        return response('拿到身分驗證回應了');
    });


    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::get('/oauth2/login', function (Request $request, AdminApi $adminApi) {
    $adminApi->getConfig()->setHost('http://127.0.0.1:4445');

    $loginChallenge = $request->input('login_challenge');

    if(empty($loginChallenge)) {
        throw new \RuntimeException('No login_challenge');
    }

    try {
        $loginRequest = $adminApi->getLoginRequest($loginChallenge);
    } catch (\Throwable $e) {
        throw new \RuntimeException('Hydra Server error: ' . $e->getMessage());
    }

    Log::debug('Login Request', json_decode((string)$loginRequest, true));

    return view('auth.login', [
        'challenge' => $loginChallenge,
    ]);
})->name('oauth2.login');

Route::post('/oauth2/login', function(Request $request, AdminApi $adminApi) {
    $adminApi->getConfig()->setHost('http://127.0.0.1:4445');

    $loginChallenge = $request->input('challenge');

    if(empty($loginChallenge)) {
        throw new \RuntimeException('No login_challenge');
    }

    if (!Auth::once($request->only('email', 'password'))) {
        return Redirect::back();

//        $rejectRequest = new RejectRequest([
//            'error' => '...',
//
//            'error_description' => '...',
//        ]);
//
//        $completedRequest = $adminApi->acceptLoginRequest($loginChallenge, $rejectRequest);
//
//        return Redirect::away($completedRequest->getRedirectTo());
    }

    $user = Auth::user();

    $acceptLoginRequest = new AcceptLoginRequest([
        'context' => new stdClass(),
        'remember' => $request->boolean('remember'),
        'rememberFor' => 0,
        'subject' => (string)$user->getAuthIdentifier(),
    ]);

    Log::debug('Accept Login Request: ', json_decode((string)$acceptLoginRequest, true));

    try {
        $completedRequest = $adminApi->acceptLoginRequest($loginChallenge, $acceptLoginRequest);
    } catch (\Throwable $e) {
        throw new \RuntimeException('Hydra Server error: ' . $e->getMessage());
    }

    Log::debug('Completed Request: ', json_decode((string)$completedRequest, true));

    return Redirect::away($completedRequest->getRedirectTo());
});

Route::get('/oauth2/consent', function () {
    return view('auth.consent');
})->name('oauth2.consent');

Route::post('/oauth2/consent', function () {
    dump(request()->all());
    return 'OAuth 2.0 授權完成';
});
