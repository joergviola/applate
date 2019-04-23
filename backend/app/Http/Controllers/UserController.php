<?php

namespace App\Http\Controllers;


use App\API;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['message'=>'Access denied'], 403);
        }
        $user = Auth::user();
        $user->token = $user->createToken('Personal')->accessToken;
        return response()->json($user);
    }

    public function register(Request $request) {
        $data = [
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
        ];
        API::create('user', $data);
        return response();
    }

}

