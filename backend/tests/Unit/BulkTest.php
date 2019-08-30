<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BulkTest extends TestCase
{

    public function testCreate()
    {
        $response = $this->withUser()->json('POST', '/api/v1.0/users', [
                [
                    'name' => 'Sally',
                    'email' => 'sally1@test.test',
                    'password' => 'blabla',
                    'role_id' => $this->user['role_id']
                ],
                [
                'name' => 'Tony',
                'email' => 'tony@test.test',
                'password' => 'blabla',
                'role_id' => $this->user['role_id']
                ]
            ]
        );

        $this->assertStatus($response);
    }

    public function testUpdate()
    {
        $sally = $this->findUser('sally1@test.test');
        $tony = $this->findUser('tony@test.test');

        $data = [];
        $data[$sally['id']] = [
            'email' => 'sally2@test.test',
        ];
        $data[$tony['id']] = [
            'email' => 'tony2@test.test',
        ];

        $response = $this->withUser()->json('PUT', '/api/v1.0/users', $data);
        $this->assertStatus($response);

        $this->assertNotEmpty($this->findUser('sally2@test.test'));
        $this->assertNotEmpty($this->findUser('tony2@test.test'));
    }

    public function testDelete()
    {
        $sally = $this->findUser('sally2@test.test');
        $this->assertNotEmpty($sally);
        $tony = $this->findUser('tony2@test.test');
        $this->assertNotEmpty($tony);

        $ids = implode(',', [$sally['id'], $tony['id']]);

        $response = $this->withUser()->json('DELETE', '/api/v1.0/users/' . $ids, []);
        $this->assertStatus($response);

        $this->assertEmpty($this->findUser('sally2@test.test'));
        $this->assertEmpty($this->findUser('tony2@test.test'));
    }

}
