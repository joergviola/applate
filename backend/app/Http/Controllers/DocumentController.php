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
        foreach ($items as &$item) {
            $path = $item->path;
            $item->url = URL::to('/') . "/storage/$path";
        }
        return response()->json($items);
    }

    private function store($type, $id, $key, $file) {
        $path = $file->store($type . '/' . $id . '/' . $key, 'public');
        $data = [
            'type' => $type,
            'item_id' => $id,
            'path' => $path,
            'mimetype' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'original' => $file->getClientOriginalName()
        ];
        \Log::info("creating document", ['data'=>$data]);
        return API::create('document', $data);
    }

    public function upload(Request $request, $type, $id) {
        $ids = [];
        foreach ($request->allFiles() as $key => $files) {
            foreach ($files as $file) {
                $ids[] = $this->store($type, $id, $key, $file);
            }
        }
        return response()->json($ids);
    }

    public function delete(Request $request, $type, $id) {
        return response()->json();
    }
}

