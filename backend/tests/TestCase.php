<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    protected $user;

    protected function setUp(): void {
        parent::setUp();

        $response = $this
            ->withHeaders([
                "Content-Type" => "application/json",
                "Accept" => "application/json",
            ])
            ->json('POST', '/login', [
                'email' => 'admin',
                'password' => 'admin',
            ]);

        $response->assertStatus(200);

        $this->user = $response->json();
    }

    protected function withUser() {
        return $this->withHeaders([
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Authorization" => "Bearer " . $this->user['token'],
        ]);
    }

    protected function assertStatus($response, $status) {
        if ($response->status()!=$status) {
            $response->dump();
        }
        $response->assertStatus($status);
    }

}
