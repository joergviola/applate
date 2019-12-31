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
        $item = $request->json()->all();
        if (isset($item[0])) {
            $ids = API::bulkCreate($type, $item);
            return response()->json(['ids' => $ids]);
        } else {
            $id = API::create($type, $item);
            return response()->json(['id' => $id]);
        }
    }

    public function update(Request $request, $type, $id) {
        $item = $request->json();
        $count = API::update($type, $id, $item->all());
        // Also 0 if no changes
        //if ($count==0)  return response()->json("", 404);
        return response()->json(['id' => $id]);
    }

    public function bulkUpdate(Request $request, $type) {
        $item = $request->json();
        $count = API::bulkUpdate($type, $item->all());
        return response()->json(['count' => $count]);
    }

    public function delete(Request $request, $type, $id) {
        $ids = explode(',', $id);
        if (count($ids)==1) {
            $count = API::delete($type, $ids[0]);
        } else {
            $count = API::bulkDelete($type, $ids);
        }
        return response()->json(['count' => $count]);
    }

    public function deleteQuery(Request $request, $type) {
        $query = $request->all();
        $count = API::deleteQuery($type, $query);
        return response()->json(['count' => $count]);
    }
}

