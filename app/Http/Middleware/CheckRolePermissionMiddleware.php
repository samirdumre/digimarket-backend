<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRolePermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if(!$request->user()){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = $request->user();

        foreach ($permissions as $permission){
            if($user->can($permission)){
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Insufficient permissions'
        ], 403);
    }
}
