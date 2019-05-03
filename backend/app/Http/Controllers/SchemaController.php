<?php

namespace App\Http\Controllers;


use App\API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SchemaController extends Controller
{
    public function index(Request $request) {
        return view('schema.index');
    }


    private function get($type) {
        return [
            'operationId' => 'get'.$type,
            'summary' => 'Show API version details',
            'tags' => [$type],
            'parameters' => [
                [
                    'name' => 'id',
                    'type' => 'integer',
                    'in' => 'path',
                    'description' => "Id of the $type to read.",
                ],
            ],
            'produces' =>
                [
                    0 => 'application/json',
                ],
            'responses' =>
                [
                    200 => [
                        'description' => "The specified $type item.",
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => "#/components/schemas/$type",
                                ]
                            ]
                        ]
                    ]
                ]
        ];
    }

    private function post($type) {
        return [
            'operationId' => 'post'.$type,
            'summary' => 'Show API version details',
            'tags' => [$type],
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

    private function model($columns) {
        return [
            'properties' => $columns
        ];
    }

    public function schema(Request $request) {

        $connection = Schema::getConnection();

        $tables = $connection->select("select table_name as name, table_comment as comment from information_schema.tables where table_schema=?", [$connection->getDatabaseName()]);

        $paths = [];
        $schemas = [];

        foreach ($tables as $table) {
            $type = $table->name;
            $columns = $this->getColumns($connection, $type);
            $path = [];
            $path['get'] = $this->get($type, $columns);
            $path['post'] = $this->post($type);
            $paths["/$type/{id}"] = $path;
            $schemas[$type] = $this->model($type, $columns);
        }

        $schema = array (
            'openapi' => '3.0.0',
            'info' =>
                [
                    'title' => 'Simple API overview',
                    'version' => 'v1.0',
                ],
            'servers' => [
                ['url' => 'http://'.$request->getHost() . $request->getBaseUrl() . '/api/v1.0'],
            ],
            'paths' => $paths,
            'components' => [
                'schemas' => $schemas,
            ],
            'consumes' =>
                array (
                    0 => 'application/json',
                ),
        );

        return response()->json($schema);
    }

    /**
     * @param $connection
     * @param $table
     * @return array
     */
    private function getColumns($connection, $type): array {
        $typeMap = [
            'int' => 'integer',
            'bigint' => 'integer',
            'varchar' => 'string',
            'timestamp' => 'string',
            'text' => 'string',
            'tinyint' => 'boolean',
        ];
        $result = $connection->select('select column_name as name, data_type as type, character_maximum_length as length, column_comment as comment  from information_schema.columns where table_schema = ? and table_name = ?', [$connection->getDatabaseName(), $type]);
        $columns = [];
        foreach ($result as $col) {
            $colType = $col->type;
            if (isset($typeMap[$colType])) $colType = $typeMap[$colType];
            $columns[$col->name] = [
                'type' => $colType,
                'description' => $col->comment,
            ];
            if ($colType == 'text') {
                $columns[$col->name]['maxLength'] = $col->length;
            }
            if ($col->type == 'timestamp') {
                $columns[$col->name]['format'] = 'date-time';
            }
        }
        return $columns;
    }

}

