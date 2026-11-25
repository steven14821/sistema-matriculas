<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactorAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('login.store')) {
            $request->merge(['remember' => false]);
        }

        if ($request->routeIs('two-factor.disable')) {
            abort(403, 'La autenticación en dos pasos es obligatoria.');
        }

        if ($request->routeIs('two-factor.setup', 'two-factor.enable', 'two-factor.confirm', 'logout')) {
            return $next($request);
        }

        $user = $request->user();

        if ($user instanceof User && ! $user->hasEnabledTwoFactorAuthentication()) {
            return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }
}
