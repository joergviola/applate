<?php

namespace App\Http\Controllers;


use App\API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $items = API::query('notification', [
            'and' => [
                'user_id' => $user->id,
            ],
        ]);
        return response()->json($items);
    }

    public function clear(Request $request) {
        $user = Auth::user();
        API::delete('notification', [
            'and' => [
                'user_id' => $user->id,
            ],
        ]);
        return response()->json();
    }
}

