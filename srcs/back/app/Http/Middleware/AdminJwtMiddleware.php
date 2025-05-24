<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AdminJwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->header('Authorization');
            if (!$token) {
                $token = $request->cookie('token');
            }
            if (!$token) {
                return redirect('/admin/login');
            }

            $token = str_replace('Bearer ', '', $token);
            $user = User::where('remember_token', $token)->where('role', 'admin')->first();
            
            if (!$user) {
                return redirect('/admin/login');
            }

            return $next($request);
        } catch (\Exception $e) {
            return redirect('/admin/login');
        }
    }
} 