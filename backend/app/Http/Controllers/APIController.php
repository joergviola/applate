<?php

namespace App\Http\Controllers;


use App\API;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function query(Request $request, $type) {
        $query = $request->json();
        $items = API::query($type, $query->all());
        return response()->json($items);
    }

    public function read(Request $request, $type, $id) {
        $item = API::read($type, $id);
        if (!$item) return response("Not found", 404);
        return response()->json($item);
    }

    public function create(Request $request, $type) {
        $item = $request->json();
        $id = API::create($type, $item->all());
        return response()->json(['id'=>$id]);
    }

    public function update(Request $request, $type, $id) {
        $item = $request->json();
        $count = API::update($type, $id, $item->all());
        return response()->json(['count'=>$count]);;
    }

}

