<?php

namespace App\Http\Controllers;


use App\API;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function read(Request $request, $type, $id) {
        $item = API::read($type, $id);
        if (!$item) return response("Not found", 404);
        return response()->json($item);
    }

    public function craete(Request $request) {
        $data = [
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
        ];
        API::create('user', $data);
        return response();
    }

}

