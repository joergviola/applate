<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    protected $user;

    protected function setUp(): void {
        parent::setUp();

        $response = $this->login('admin', 'admin');
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

    protected function findUser($email) {
        $response = $this->withUser()->json('POST', '/api/v1.0/users/query', []);

        $this->assertStatus($response, 200);

        foreach ($response->json() as $user) {
            if ($email == $user['email']) return $user;
        }
        return null;
    }


    protected function assertStatus($response, $status=200) {
        if ($response->status()!=$status) {
            print_r($response->getContent());
        }
        $response->assertStatus($status);
    }

    /**
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function login($email, $password) {
        $response = $this
            ->withHeaders([
                "Content-Type" => "application/json",
                "Accept" => "application/json",
            ])
            ->json('POST', '/login', [
                'email' => $email,
                'password' => $password,
            ]);

        return $response;
    }

}
