<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected function reportThrowable(Throwable $e): void
    {
        try {
            parent::reportThrowable($e);
        } finally {
            $root = $e;
            while ($root->getPrevious() !== null) {
                $root = $root->getPrevious();
            }

            $errorCode = (string) $root->getCode();
            if ($root instanceof \PDOException && is_array($root->errorInfo)) {
                $sqlState = $root->errorInfo[0] ?? $errorCode;
                $driverCode = $root->errorInfo[1] ?? '';
                $errorCode = $driverCode === ''
                    ? (string) $sqlState
                    : $sqlState.'/'.$driverCode;
            }

            error_log(sprintf(
                '[APP_EXCEPTION] %s; root=%s; code=%s; at=%s:%d',
                get_class($e),
                get_class($root),
                $errorCode === '' ? 'none' : $errorCode,
                basename($root->getFile()),
                $root->getLine()
            ));
        }
    }
}
