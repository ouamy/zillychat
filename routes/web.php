<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use Illuminate\Http\Request;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/chat', function () {
        return view('chat');
    })->name('chat');
});

Route::get('/chat', [ChatController::class, 'index'])->middleware(['auth'])->name('chat');

Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

Route::get('/chat/messages', [ChatController::class, 'fetchMessages'])->name('chat.fetchMessages');

Route::get('/email/verify', function (Request $request) {
    $user = $request->user();
    if (!$user->hasVerifiedEmail()) {
        if (!$request->session()->has('verification-email-sent')) {
            $user->sendEmailVerificationNotification();
            $request->session()->put('verification-email-sent', true);
        }
    }
    return view('auth.verify-email');
})->name('verification.notice')->middleware('auth');

Route::get('/login/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/login/google/callback', [GoogleController::class, 'handleGoogleCallback']);
