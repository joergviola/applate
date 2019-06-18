<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CRUDTest extends TestCase
{

    public function testCreate()
    {
        $response = $this->withUser()->json('POST', '/api/v1.0/users', [
            'name' => 'Sally',
            'email' => 'sally@test.test',
            'password' => 'blabla',
            'role_id' => $this->user['role_id']
        ]);

        $this->assertStatus($response);
    }

    public function testRead()
    {
        $this->assertNotEmpty($this->findUser('sally@test.test'));
    }

    public function testDelete()
    {
        $sally = $this->findUser('sally@test.test');
        $this->assertNotEmpty($sally);

        $response = $this->withUser()->json('DELETE', '/api/v1.0/users/' . $sally['id'], []);
        $this->assertStatus($response);

        $this->assertEmpty($this->findUser('sally@test.test'));
    }

}
