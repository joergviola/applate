<?php

namespace App\Http\Controllers;


use App\API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchemaController extends Controller
{
    public function index(Request $request) {
        return view('schema.index');
    }


    private function schemaGet($type) {
        return [
            'operationId' => 'get'.$type,
            'summary' => 'Show API version details',
            'parameters' => [
                [
                    'name' => 'id',
                    'type' => 'integer',
                    'in' => 'path',
                    'description' => "Id of the $type to read.",
                ],
            ],
            'produces' =>
                array (
                    0 => 'application/json',
                ),
            'responses' =>
                [
                    200 => [
                        'description' => '200 response',
                        'examples' => [
                            'a' => 'a',
                            'b' => 'a',
                            'c' => 'a',
                        ]
                    ]
                ]
        ];
    }

    public function schema(Request $request) {

        $types = ['users', 'right', 'role'];

        $paths = [];

        foreach ($types as $type) {
            $path = [];
            $path['get'] = $this->schemaGet($type);
            $paths["/$type/{id}"] = $path;
        }

        $schema = array (
            'swagger' => '2.0',
            'info' =>
                array (
                    'title' => 'Simple API overview',
                    'version' => 'v2',
                ),
            'host' => 'localhost',
            'basePath' => $request->getBaseUrl() . '/api/v1.0',
            'paths' => $paths,
            'consumes' =>
                array (
                    0 => 'application/json',
                ),
        );

        return response()->json($schema);
    }

}

