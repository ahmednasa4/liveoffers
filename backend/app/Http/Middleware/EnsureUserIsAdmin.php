<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح. هذا القسم مخصص للمشرفين فقط.',
                ], Response::HTTP_FORBIDDEN);
            }
            return redirect()->route('login')->with('error', 'غير مصرح. هذا القسم مخصص للمشرفين فقط.');
        }

        return $next($request);
    }
}