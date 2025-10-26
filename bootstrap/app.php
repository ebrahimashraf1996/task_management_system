<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $jsonResponse = function (string $message, int $status, $data = null) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
            ], $status);
        };

        $exceptions->render(function (NotFoundHttpException $e) use ($jsonResponse) {
            return $jsonResponse('Resource not found.', 404);
        });

        $exceptions->render(function (AccessDeniedHttpException $e) use ($jsonResponse) {
            return $jsonResponse('You are not authorized to perform this action.', 403);
        });

        $exceptions->render(function (AuthorizationException $e) use ($jsonResponse) {
            return $jsonResponse('You are not authorized to perform this action.', 403);
        });

        $exceptions->render(function (AuthenticationException $e) use ($jsonResponse) {
            return $jsonResponse('Unauthenticated. Please login first.', 401);
        });

    })->create();
