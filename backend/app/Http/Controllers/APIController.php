<?php

namespace App\Http\Controllers;


use App\API;
use Illuminate\Http\Request;

/*
 * Errors are handled in the HandleError Middleware
 */
class APIController extends Controller
{
    public function query(Request $request, $type) {
        $query = $request->json();
        $items = API::query($type, $query->all());
        return response()->json($items);
    }

    public function read(Request $request, $type, $id) {
        $item = API::read($type, $id);
        if (is_null($item)) return response()->json("", 404);
        return response()->json($item);
    }

    public function create(Request $request, $type) {
        $item = $request->json();
        $id = API::create($type, $item->all());
        return response()->json(['id' => $id]);
    }

    public function update(Request $request, $type, $id) {
        $item = $request->json();
        $count = API::update($type, $id, $item->all());
        if ($count==0)  return response()->json("", 404);
        return response()->json();
    }
}

