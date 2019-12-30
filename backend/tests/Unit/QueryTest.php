<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QueryTest extends TestCase
{

    public function testQuery() {
        // This query fetches all users with email 'admin' along with their rights on the table 'users' and their role
        $response = $this->withUser()->json('POST', '/api/v1.0/users/query', [
            'and' => [
                'email' => 'admin'
            ],
            "with" => [
                "rights" => [
                    "many" => "right",
                    "this" => "role_id",
                    "that" => "role_id",
                    "query" => [
                        "and" => [
                            "tables" => "*"
                        ],
                        "with" => [
                            "role" => [
                                "one" => "role",
                                "this" => "role_id"
                            ]
                        ]
                    ]
                ],
                "role" => [
                    "one" => "role",
                    "this" => "role_id"
                ]

            ]
        ]);
        $this->assertStatus($response);
        $users = $response->json();
        $this->assertCount(1, $users);
        $user = $users[0];
        $this->assertEquals('admin', $user['email']);
        $this->assertCount(1, $user['rights']);
        $this->assertEquals('*', $user['rights'][0]['tables']);
        $this->assertEquals('Admin', $user['role']['name']);
        $this->assertEquals(true, $user['_meta']['rights']['ignore']);
        $this->assertEquals('right', $user['_meta']['rights']['many']);
    }

    public function testAndOr() {
        // This query fetches all users with email 'admin' along with their rights on the table 'users' and their role
        $response = $this->withUser()->json('POST', '/api/v1.0/users/query', [
            'and' => [
                'email' => 'admin',
                'or' => [
                    'name' => 'admin',
                    'email' => 'Tom',
                    'and' => [
                        'client_id' => 1000,
                        'role_id' => 8
                    ]
                ]
            ],
        ]);
        $this->assertStatus($response);
        $users = $response->json();
        $this->assertCount(1, $users);
        $user = $users[0];
        $this->assertEquals('admin', $user['email']);
    }

    public function testPaging() {
        $response = $this->withUser()->json('POST', '/api/v1.0/right/query', [
            'and' => [
            ],
            "page" => [
                "skip" => 2,
                "take" => 2,
                "count" => true
            ]
        ]);
        $this->assertStatus($response);
        $result = $response->json();
        $this->assertCount(2, $result['result']);
        $this->assertGreaterThan(2, $result['count']);
    }

}
