<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CRUDTest extends TestCase
{

    private $sally;

    public function testCreate()
    {
        $response = $this->withUser()->json('POST', '/api/v1.0/users', [
            'name' => 'Sally',
            'email' => 'sally@test.test',
            'password' => 'blabla',
            'role_id' => $this->user['role_id']
        ]);

        $response
            ->assertStatus(200);
    }


    private function findUser($email) {
        $response = $this->withUser()->json('POST', '/api/v1.0/users/query', []);

        $this->assertStatus($response, 200);

        foreach ($response->json() as $user) {
            if ($email == $user['email']) return $user;
        }
        return null;
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
        $this->assertStatus($response, 200);

        $this->assertEmpty($this->findUser('sally@test.test'));
    }

}
