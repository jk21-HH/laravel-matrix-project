<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;


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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
       // Handle validation exception
       if ($exception instanceof ValidationException) {
           return response()->json([
               'error' => 'Validation failed',
               'message' => $exception->validator->errors(),
           ], Response::HTTP_BAD_REQUEST);
       }

       // Handle other exceptions as needed

       return parent::render($request, $exception);
    }
}
