<?php

namespace App\Http\Controllers;


use App\API;
use App\User;
use App\Events\ApiAfterLoginEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        return view('login');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            \Log::info('Login attempt failed', $credentials);
            if ($request->isJson()) {
                return response()->json(['message'=>'Access denied'], 403);
            } else {
                return redirect("/login");
            }
        }
        $user = Auth::user();
        $user->token = $user->createToken('Personal')->accessToken;
        $user->rights = API::query('right', ['and' => ['role_id' => $user->role_id ]]);
        event(new ApiAfterLoginEvent($user));
        \Log::info('Login attempt successful', [$user->email]);
        if ($request->isJson()) {
            return response()->json($user);
        } else {
            session(['token' => $user->token]);
            return redirect("/schema");
        }
    }

}

