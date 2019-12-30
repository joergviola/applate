<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{

    public function testAdmin()
    {
        $response = $this->login('admin', 'admin');
        $response->assertStatus(200);

        $user = $response->json();

        $this->assertEquals('admin', $user['email']);
        $this->assertArrayNotHasKey('password', $user);
        $this->assertNotEmpty($user['token']);
        $this->assertCount(83, $user['rights']);
    }

    public function testSally()
    {
        $response = $this->login('Sally', 'notright');
        $response->assertStatus(403);

        $user = $response->json();

        $this->assertEquals(['message' => 'Access denied'], $user);
    }

    public function testRights()
    {
        $response = $this->login('admin', 'admin');
        $response->assertStatus(200);
        $this->user = $response->json();

//        $response = $this->withUser()->json('POST', '/api/v1.0/role', [
//            'name' => 'Test',
//        ]);
//        $this->assertStatus($response);
//        $role_id = $response->json('id');
//        $response = $this->withUser()->json('POST', '/api/v1.0/right', [
//            'role_id' => $role_id,
//            'tables' => 'right',
//            'columns' => '*',
//            'where' => '*',
//            'actions' => 'CR', // Not U
//        ]);
//        $right_id = $response->json('id');
//        $response = $this->withUser()->json('PUT', '/api/v1.0/users/' . $this->user['id'], [
//            'role_id' => $role_id,
//        ]);
//
//        $response = $this->withUser()->json('POST', '/api/v1.0/right', [
//            'role_id' => $role_id,
//            'tables' => 'users',
//            'columns' => '*',
//            'where' => '*',
//            'actions' => 'R',
//        ]);
//        $right2_id = $response->json('id');
//        $this->assertStatus($response);
//        $response = $this->withUser()->json('GET', '/api/v1.0/right/' . $right2_id, []);
//        $this->assertStatus($response);
//        $response = $this->withUser()->json('PUT', '/api/v1.0/right/' . $right2_id, [
//            'actions' => 'U',
//        ]);
//        $this->assertStatus($response, 403);
//        $response = $this->withUser()->json('DELETE', '/api/v1.0/right/' . $right2_id, []);
//        $this->assertStatus($response, 403);
//
//
//        $response = $this->withUser()->json('PUT', '/api/v1.0/users/' . $this->user['id'], [
//            'role_id' => $this->user['role_id'],
//        ]);
//        $response = $this->withUser()->json('DELETE', '/api/v1.0/right/' . $right_id, []);
//        $response = $this->withUser()->json('DELETE', '/api/v1.0/right/' . $right2_id, []);
//        $response = $this->withUser()->json('DELETE', '/api/v1.0/role/' . $role_id, []);

    }


}
