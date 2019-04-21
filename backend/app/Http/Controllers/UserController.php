<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response('Forbidden', 403);
        }
        return resonse()->json(Auth::user());
    }
}

