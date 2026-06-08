<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

class Handler extends ExceptionHandler
{

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return parent::render($request, $exception);
    }
}
