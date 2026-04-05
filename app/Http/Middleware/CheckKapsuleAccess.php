<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKapsuleAccess
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

        if ($kapsule->members()->where('user_id', $user->id)->wherePivot('is_banned', true)->exists()) {
            abort(403, __('you_are_banned_from_this_kapsule'));
        }

        if (! $kapsule->members()->where('user_id', $user->id)->exists() && $kapsule->user_id !== $user->id) {
            abort(404, __('kapsule_not_found'));
        }

        if ($kapsule->members()->where('user_id', $user->id)->wherePivot('accepted', false)->exists()) {
            abort(403, __('your_membership_request_is_pending'));
        }

        return $next($request);
    }
}
