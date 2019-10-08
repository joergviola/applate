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
            'summary' => "Read a $type by id.",
            'security' => [
                'BearerAuth' => [],
            ],
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
            'responses' => $this->responses([
                    200 => [
                        'description' => "The specified $type item.",
                        'content' => $this->content([
                            '$ref' => "#/components/schemas/$type",
                        ]),
                    ]
                ], [400, 403, 404])
        ];
    }

    private function post($type) {
        return [
            'operationId' => 'post'.$type,
            'summary' => "Create a new $type",
            'tags' => [$type],
            'requestBody' => [
                'description' => "$type to create.",
                'required' => true,
                'content' => $this->content([
                    '$ref' => "#/components/schemas/$type",
                ]),
            ],
            'produces' =>
                array (
                    0 => 'application/json',
                ),
            'responses' => $this->responses([
                    200 => [
                        'description' => "$type has been successfully created",
                        'content' => $this->objectContent([
                            'id' => [
                                'type' => 'integer',
                                'description' => "Id of the $type created"
                            ]
                        ]),
                    ]
                ], [400, 403])
        ];
    }

    private function put($type) {
        return [
            'operationId' => 'put'.$type,
            'summary' => "Update a $type",
            'tags' => [$type],
            'parameters' => [
                [
                    'name' => 'id',
                    'type' => 'integer',
                    'in' => 'path',
                    'description' => "Id of the $type to update.",
                ],
            ],
            'requestBody' => [
                'description' => "$type to update.",
                'required' => true,
                'content' => $this->content([
                    '$ref' => "#/components/schemas/$type",
                ]),
            ],
            'produces' =>
                array (
                    0 => 'application/json',
                ),
            'responses' => $this->responses([
                    200 => [
                        'description' => "$type has been successfully updated",
                        'content' => []
                    ]
                ], [400, 403, 404])
        ];
    }

    private function delete($type) {
        return [
            'operationId' => 'delete' . $type,
            'summary' => "Delete a $type",
            'tags' => [$type],
            'parameters' => [
                [
                    'name' => 'id',
                    'type' => 'integer',
                    'in' => 'path',
                    'description' => "Id of the $type to delete.",
                ],
            ],
            'produces' =>
                array(
                    0 => 'application/json',
                ),
            'responses' => $this->responses([
                    200 => [
                        'description' => "$type has been successfully deleted.",
                        'content' => [],
                    ]
                ], [400, 403, 404])
        ];
    }

    private function query($type) {
        return [
            'operationId' => 'query'.$type,
            'summary' => "Query $type",
            'tags' => [$type],
            'requestBody' => [
                'description' => "Query.",
                'required' => true,
                'content' => $this->content([
                    '$ref' => "#/components/schemas/query",
                ]),
            ],
            'produces' =>
                array (
                    0 => 'application/json',
                ),
            'responses' => $this->responses([
                    200 => [
                        'description' => "List of matching object of $type.",
                        'content' => $this->content([
                            'type' => 'array',
                            'items' => [
                                '$ref' => "#/components/schemas/query"
                            ]
                        ]),
                    ]
                ], [400, 403])
        ];
    }

    private function responses($explicit, $codes) {
        $standard = [
            400 => [
                'description' => "Illegal request",
                'content' => $this->objectContent([
                    'message' => [
                        'type' => 'string',
                        'description' => "Explanation of the error."
                    ]
                ]),
            ],
            403 => [
                'description' => "Not allowed",
                'content' => $this->objectContent([
                    'message' => [
                        'type' => 'string',
                        'description' => "Explanation of the error."
                    ]
                ]),
            ],
            404 => [
                'description' => "Not found.",
                'content' => $this->objectContent([
                    'message' => [
                        'type' => 'string',
                        'description' => "Explanation of the error."
                    ]
                ]),
            ],
        ];
        $result = $explicit;
        foreach ($codes as $code) {
            $result[$code] = $standard[$code];
        }
        return $result;
    }

    private function objectContent($properties) {
        return $this->content([
            'type' => 'object',
            'properties' => $properties,
        ]);
    }

    private function content($content) {
        return [
            'application/json' => [
                'schema' => $content,
            ]
        ];
    }

    private function model($columns, $description) {
        return [
            'description' => $description,
            'properties' => $columns
        ];
    }

    private function login() {
        return [
            'operationId' => 'login',
            'summary' => "Sign in a user",
            'tags' => ['authorisation'],
            'requestBody' => [
                'description' => "Credentials for sign-in.",
                'required' => true,
                'content' => $this->objectContent([
                    'email' => [
                        'type' => 'string',
                        'description' => "E-Mail of the user."
                    ],
                    'password' => [
                        'type' => 'string',
                        'description' => "Password of the user, clear text."
                    ],
                ]),
            ],
            'produces' =>
                array (
                    0 => 'application/json',
                ),
            'responses' => $this->responses([
                200 => [
                    'description' => "Successful login. The logged in user is return with the token property set. It contains a newly created authentication token.",
                    'content' => $this->content([
                        '$ref' => "#/components/schemas/users",
                    ]),
                ]
            ], [400, 403])
        ];
    }

    private function notifications() {
            return [
                    'operationId' => 'notification',
                    'summary' => "Get notifications",
                    'tags' => ['notification'],
                    'requestBody' => [
                            'description' => "",
                            'required' => false,
                            'content' => []
                            ],
                    'produces' =>
                            array (
                                    0 => 'application/json',
                                ),
                    'responses' => $this->responses([
                                200 => [
                                            'description' => "A list of all pending notifications for the user.",
                                            'content' => $this->content([
                                                    '$ref' => "#/components/schemas/notification",
                                                ]),
                                        ]
                                ], [400, 403])
                    ];
    }


    public function schema(Request $request) {

        $connection = Schema::getConnection();
        $forbidden = "'" . implode("','", API::FORBIDDEN) . "'";
        $tables = $connection->select("select table_name as name, table_comment as comment from information_schema.tables where table_schema=? and table_name not in($forbidden)", [$connection->getDatabaseName()]);

        $paths = [
            '/../../login' => [
                'post' => $this->login()
            ],
            '/notifications' => [
                'get' => $this->notifications()
            ]
        ];
        $schemas = [];

        foreach ($tables as $table) {
            $type = $table->name;
            $comment = $table->comment;
            $columns = $this->getColumns($connection, $type);
            $paths["/$type/{id}"] = [
                'get' => $this->get($type),
                'put' => $this->put($type),
                'delete' => $this->delete($type),
            ];
            $paths["/$type"] = [
                'post' => $this->post($type),
            ];
            $paths["/$type/query"] = [
                'post' => $this->query($type),
            ];
            $schemas[$type] = $this->model($columns, $comment);
        }
        $schemas['query'] = $this->model([
            'and' => [
                'type' => 'object',
                'properties' => [
                    'any' => [
                        'type' => 'any',
                        'description' => 'Each property of the type possible, AND-connected'
                    ]
                ],
            ],
            'with' => [
                'type' => 'object',
                'properties' => [
                    'new-field' => [
                        'type' => 'object',
                        'description' => 'The new field is set in each instance found based on the query specified in the aatributes',
                        'properties' => [
                            'type' => [
                                'type' => 'string',
                                'description' => 'The type, the new field is to be filled from',
                            ],
                            'from' => [
                                'type' => 'string',
                                'description' => 'The field in the instance holding the id value for the reference',
                            ],
                        ],
                    ]
                ],
            ],
        ], 'Query for items.');

        $protocol = $request->isSecure() ? 'https' : 'http';

        $schema = array (
            'openapi' => '3.0.0',
            'info' =>
                [
                    'title' => 'Simple API overview',
                    'version' => 'v1.0',
                ],
            'servers' => [
                ['url' => $protocol . '://'.$request->getHost() . $request->getBaseUrl() . '/api/v1.0'],
            ],
            'paths' => $paths,
            'components' => [
                'schemas' => $schemas,
                'securitySchemes' => [
                    'BearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                    ]
                ]
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

