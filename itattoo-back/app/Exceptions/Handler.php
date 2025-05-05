<?php

namespace App\Exceptions;

use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof NotFoundHttpException && $request->wantsJson()) {
                return response()->json(['message' => 'Not Found'], 404);
            }
            if ($e instanceof HttpException && $request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
            }
        });
    }
}
