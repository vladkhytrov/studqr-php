<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    public function test_register_user()
    {
        $response = $this->post(
            '/api/register',
            [
                'name'     => 'testName',
                'email'    => 'test@email.com',
                'password' => 'testPassword',
            ]);
        $response->assertStatus(201);

        // todo user factory
        $user = User::query()->where('email', 'test@email.com')->first();

        self::assertNotNull($user);
    }

    public function test_login_user()
    {
        // todo replace with factory
        $this->post(
            '/api/register',
            [
                'name'     => 'testName',
                'email'    => 'test@email.com',
                'password' => 'testPassword',
            ]);

        $loginResponse = $this->post(
            '/api/login',
            [
                'email'    => 'test@email.com',
                'password' => 'testPassword',
            ]
        );

        $loginResponse->assertStatus(200);
    }
}
