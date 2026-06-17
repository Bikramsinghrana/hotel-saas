<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PDOException;
use Symfony\Component\HttpFoundation\Response;

class HandleDatabaseConnection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (PDOException $e) {

            if (
                str_contains($e->getMessage(), 'SQLSTATE[HY000] [2002]') ||
                str_contains($e->getMessage(), 'Connection refused')
            ) {

                return response()->view(
                    'errors.database',
                    [
                        'message' => 'Database server is currently unavailable. Please try again later.'
                    ],
                    503
                );
            }

            throw $e;
        }
    }
}
