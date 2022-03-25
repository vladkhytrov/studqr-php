<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

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

        $user = User::where('email', 'test@email.com')->first();

        self::assertNotNull($user);
    }
}
