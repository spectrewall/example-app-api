<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use App\Traits\ResponseApi;
use Closure;
use Illuminate\Http\Request;

class OnlyAdmin
{
    use ResponseApi;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->type == 'administrator') return $next($request);
        else return $this->error('Not allowed', ResponseAlias::HTTP_FORBIDDEN);
    }
}
