<?php

namespace App\Http\Middleware;

use App\Models\Kapsule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfOwnerKapsule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $kapsule = $request->route('kapsule');
        $user = $request->user();

        if (! $kapsule instanceof Kapsule) {
            $kapsule = Kapsule::findOrFail($kapsule);
        }

        if ($kapsule->user_id !== $user->id) {
            abort(403, __('you_are_not_the_owner_of_this_kapsule'));
        }

        return $next($request);
    }
}
