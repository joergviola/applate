<?php

namespace Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentTest extends TestCase
{

    protected $sally;

    protected function setUp(): void {
        parent::setUp();
        $this->withUser()->json('POST', '/api/v1.0/users', [
            'name' => 'Sally',
            'email' => 'sally@test.test',
            'password' => 'blabla',
            'role_id' => $this->user['role_id']
        ]);

        $this->sally = $this->findUser('sally@test.test');
    }

    public function testDocument()
    {
        $id = $this->sally['id'];

        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->withUser()->json('POST', "/api/v1.0/users/$id/documents" , [
            'avatar' => $file,
        ]);
        $this->assertStatus($response);

        $response = $this->withUser()->json('GET', "/api/v1.0/users/$id/documents" , []);
        $this->assertStatus($response);

        $files = $response->json();
        $this->assertCount(1, $files);

        $response = $this->withUser()->json('DELETE', "/api/v1.0/users/$id/documents" , [$files[0]['id']]);
        $this->assertStatus($response);

        $response = $this->withUser()->json('GET', "/api/v1.0/users/$id/documents" , []);
        $this->assertStatus($response);

        $files = $response->json();
        $this->assertCount(0, $files);

    }

    public function testRead()
    {
    }

    public function testDelete()
    {
    }

    protected function tearDown(): void {
        $this->withUser()->json('DELETE', '/api/v1.0/users/' . $this->sally['id'], []);
        parent::tearDown();
    }
}
