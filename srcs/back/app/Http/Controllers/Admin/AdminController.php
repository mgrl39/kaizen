<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password) || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials or insufficient permissions'
            ], 401);
        }

        $token = JWTAuth::fromUser($user);
        
        // Store token in database
        $user->remember_token = $token;
        $user->save();
        
        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    public function dashboard(Request $request)
    {
        return view('admin.dashboard');
    }

    public function logout(Request $request)
    {
        if ($request->header('Authorization')) {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            $user = User::where('remember_token', $token)->first();
            if ($user) {
                $user->remember_token = null;
                $user->save();
            }
        }
        
        return response()->json(['success' => true]);
    }
}
