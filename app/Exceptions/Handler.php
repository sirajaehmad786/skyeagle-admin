<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Handler extends ExceptionHandler
{

    public function render($request, Throwable $exception)
    {
        // Catch Laravel's native authorization (403) exception
        if ($exception instanceof AuthorizationException) {
            // Optional: Log or debug
            // \Log::warning('403 AuthorizationException: ' . $exception->getMessage());

            // Redirect to fallback page with flash message
            return redirect()->route('fallback.page')
                ->with('error', 'You do not have permission to access this page.');
        }

        // Catch Spatie's unauthorized exception (permission-based)
        if ($exception instanceof UnauthorizedException) {
            return redirect()->route('fallback.page')
                ->with('error', 'Access denied. You lack the necessary permission.');
        }

        return parent::render($request, $exception);
    }
}
