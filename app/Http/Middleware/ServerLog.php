<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServerLog
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        /** @var JsonResponse $response */
        $response = $next($request);

        $responseData = implode(" ", $response->getOriginalContent()['message']);

        $message =
            "[" . $request->getMethod() . " " . $request->getRequestUri() . " by " . $request->getClientIp() .
            "] [RESPONSE: {STATUS: " . $response->getStatusCode() . "} {MESSAGE: " . $responseData . "}]";

        \Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/requests.log'),
        ])->info($message);

        return $response;
    }
}
