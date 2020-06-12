<?php

namespace App\Http\Controllers;


use App\API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DocumentController extends Controller
{
    public function list(Request $request, $type, $id) {
        $items = API::query('document', [
            'and' => [
                'type' => $type,
                'item_id' => $id,
            ],
        ]);
        return response()->json($items);
    }

    public function upload(Request $request, $type, $id) {
        \Log::debug('file upload');
        foreach ($request->allFiles() as $key => $files) {
            if (is_array($files)) {
                foreach ($files as $file) {
                    API::storeDocument($type, $id, $key, $file, true);
                }
            } else {
                API::storeDocument($type, $id, $key, $files, false);
            }
        }
        return response()->json();
    }

    public function delete(Request $request, $type, $id, $ids) {
        $ids = explode(',', $ids);

        API::deleteDocument($type, $id, $ids);

        return response()->json();
    }
}

