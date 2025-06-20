<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckScopeAndRoleMiddleware
{
    public function handle(Request $request, Closure $next, $scope, $role)
    {
        if(!$request->user()){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = $request->user();

        if(!$user->tokenCan($scope)){
            return response()->json(['message' => 'Invalid scope'], 403);
        }

        if(!$user->hasRole($role)){
            return response()->json(['message' => 'Insufficient role permissions'], 403);
        }

        return $next($request);
    }
}
