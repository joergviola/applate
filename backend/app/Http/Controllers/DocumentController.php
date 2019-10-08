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
            $name = $item->name;
            $item->url = URL::to('/') . "/storage/$type/$id/$path/$name";
        }
        return response()->json($items);
    }

    private function store($type, $id, $key, $file, $multiple) {
        $dir = $type . '/' . $id . '/' . $key;
        $originalName = $file->getClientOriginalName();
        $doc_id = $this->remove($type, $id, $key, $multiple ? $originalName : null);

        $path = $file->store($dir, 'public');
        $name = substr($path, strlen($dir)+1);
        $data = [
            'type' => $type,
            'item_id' => $id,
            'path' => $key,
            'name' => $name,
            'mimetype' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'original' => $originalName
        ];
        if ($doc_id) {
            \Log::info("updating document", ['data'=>$data]);
            return API::update('document', $doc_id, $data);
        } else {
            \Log::info("creating document", ['data'=>$data]);
            return API::create('document', $data);
        }
    }
    private function remove($type, $id, $key, $original) {
        $query = [
            'and' => [
                'type' => $type,
                'item_id' => $id,
                'path' => $key,
            ],
        ];
        if ($original) {
            $query['and']['original'] = $original;
        }
        $items = API::query('document', $query);
        $dir = $type . '/' . $id . '/' . $key;
        $doc_id = null;
        $stamp = time();
        foreach ($items as $item) {
            // Move to archive here...
            if (Storage::disk('public')->exists($dir . '/' . $item->name)) {
                Storage::disk('public')->move($dir . '/' . $item->name, $dir . '/archive/' . $item->id . '/' . $item->name . '-' . $stamp);
            }
            if ($doc_id==null) {
                $doc_id = $item->id;
            } else {
                API::provider('document')->delete($item->id);
            }
        }
        return $doc_id;
    }

    public function upload(Request $request, $type, $id) {
        \Log::debug('file upload');
        $ids = [];
        foreach ($request->allFiles() as $key => $files) {
            if (is_array($files)) {
                foreach ($files as $file) {
                    $ids[] = $this->store($type, $id, $key, $file, true);
                }
            } else {
                $ids[] = $this->store($type, $id, $key, $files, false);
            }
        }
        return response()->json($ids);
    }

    public function delete(Request $request, $type, $id) {
        $ids = $request->json()->all();

        $query = [
            'and' => [
                'type' => $type,
                'item_id' => $id,
                'id' => ['in' => $ids],
            ],
        ];
        $items = API::query('document', $query);
        $stamp = time();
        foreach ($items as $item) {
            // Move to archive here...
            $dir = $type . '/' . $id . '/' . $item->path;
            Storage::disk('public')->move($dir . '/' . $item->name, $dir . '/archive/' . $item->id . '/' . $item->name . '-' . $stamp);
            API::provider('document')->delete($item->id);
        }

        return response()->json();
    }
}

