<?php

namespace App\Http\Controllers;


use App\API;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function log(Request $request, $type, $id) {
        $items = API::query('log', [
            'and' => [
                'type' => $type,
                'item_id' => $id,
            ],
            'with' => [
                'user' => ['type'=>'users', 'from' => 'user_id'],
            ],
        ]);
        foreach ($items as &$item) {
            $item->content = json_decode($item->content);
        }
        return response()->json($items);
    }

    public function restore(Request $request, $type, $id, $log) {
        $version = API::read('log', $log);
        $content = json_decode($version->content, true);
        API::update($type, $id, $content);
        return response()->json();
    }
}

