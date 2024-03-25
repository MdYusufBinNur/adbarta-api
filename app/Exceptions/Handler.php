<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            Log::error($e->getMessage());
        });

        $this->renderable(function (AuthorizationException $e, $request) {
            return response()->json((['error' => true, 'message' => 'Insufficient privileges to perform this action.', 'data' => null]), 403);
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            return response()->json((['error' => true, 'message' => 'Method Not Allowed', 'data' => null]), 405);
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json((['error' => true, 'message' => 'Resource not found with the specific id', 'data' => null]), 404);
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json((['error' => true, 'message' => 'The requested resource was not found', 'data' => null]), 404);
        });

        $this->renderable(function (RouteNotFoundException $e, $request) {
            return response()->json((['error' => true, 'message' => 'The requested route was not found', 'data' => null]), 404);
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            return response()->json((['error' => true, 'message' => "Access Denied.", 'data' => null]), 403);
        });

        $this->renderable(function (\InvalidArgumentException $e, $request) {
            return response()->json((['error' => true, 'message' => $e->getMessage()]), 403);
        });

        $this->renderable(function (QueryException $e, $request) {
            $errorCode = $e->errorInfo[1];
            return match ($errorCode) {
                1451 => response()->json([
                    'error' => true,
                    'message' => 'Cannot delete or update a parent ID: a foreign key constraint fails',
                    'data' => null
                ], 422),
                default => response()->json([
                    'error' => true,
                    'message' => 'An error occurred while processing your request',
                    'data' => null
                ], 500),
            };
        });
    }
}
