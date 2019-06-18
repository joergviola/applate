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

    private function assertFiles($actual, $expected) {
        $this->assertCount(count($expected), $actual);
        foreach ($actual as $file) {
            $this->assertEquals($expected[$file['original']], $file['path']);
        }
    }

    public function testDocument()
    {
        $id = $this->sally['id'];

        Storage::fake('avatars');

        $file1 = UploadedFile::fake()->image('file1.jpg');
        $file2 = UploadedFile::fake()->image('file2.jpg');
        $file3 = UploadedFile::fake()->image('file3.jpg');
        $file4 = UploadedFile::fake()->image('file4.jpg');
        $file5 = UploadedFile::fake()->image('file2.jpg');
        $file6 = UploadedFile::fake()->image('file6.jpg');

        $response = $this->withUser()->json('POST', "/api/v1.0/users/$id/documents" , [
            'file1' => $file1,
            'files' => [$file2, $file3]
        ]);
        $this->assertStatus($response);

        $response = $this->withUser()->json('GET', "/api/v1.0/users/$id/documents" , []);
        $this->assertStatus($response);

        $this->assertFiles($response->json(), [
            'file1.jpg' => 'file1',
            'file2.jpg' => 'files',
            'file3.jpg' => 'files',
        ]);

        $response = $this->withUser()->json('POST', "/api/v1.0/users/$id/documents" , [
            'file1' => $file4,
            'files' => [$file5, $file6]
        ]);
        $this->assertStatus($response);

        $response = $this->withUser()->json('GET', "/api/v1.0/users/$id/documents" , []);
        $this->assertStatus($response);
        $files = $response->json();
        $this->assertFiles($files, [
            'file4.jpg' => 'file1',
            'file2.jpg' => 'files',
            'file3.jpg' => 'files',
            'file6.jpg' => 'files',
        ]);

        $ids = array_map(function ($file) {return $file['id'];}, $files);

        $response = $this->withUser()->json('DELETE', "/api/v1.0/users/$id/documents" , $ids);
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
