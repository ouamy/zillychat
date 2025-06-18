<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{

public function handle(Request $request, Closure $next): Response
{
    \Log::debug('Custom VerifyCsrfToken middleware used');
    // Log the CSRF token sent in request headers and the session token
    Log::info('CSRF Debug:', [
        'header_token' => $request->header('X-CSRF-TOKEN'),
        'session_token' => $request->session()->token(),
    ]);

    // Since you currently disable CSRF, just continue request
    return $next($request);
}

}

