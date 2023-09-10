<?php

namespace Tests\Feature;

use App\Models\User;
use http\Header;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Get user without authentication must fail with 401
     */
    public function test_get_user_must_fail(): void
    {
        $response = $this
            ->withHeader('Accept', 'application/json')
            ->get('/api/user');

        self::assertEquals(401, $response->status());
    }

    /**
     * Get user with authentication must succees
     *
     * @return void
     */
//    public function test_get_user()
//    {
//        $user = User::factory()->make(['password' => 'pass1234']);
//
//        $this->post(
//            '/api/register',
//            $user->toArray()
//        );
//
//        $loginResponse = $this->post(
//            '/api/login',
//            [
//                'email'    => $user->email,
//                'password' => 'pass1234',
//            ]
//        );
//
//        $loginResponse->assertStatus(200);
//
//        $token = $loginResponse->json('token');
//        self::assertNotEmpty($token);
//
//        $response = $this
//            ->withHeader('Accept', 'application/json')
//            ->withHeader('Authorization', 'Bearer ' . $token)
//            ->get('/api/user');
//
//        self::assertEquals(200, $response->status());
//    }

    public function test_register_user()
    {
        $newUser = User::factory()->make();
        $response = $this->post(
            '/api/register',
            [
                'first_name' => $newUser->first_name,
                'last_name'  => $newUser->last_name,
                'role'       => $newUser->role,
                'email'      => $newUser->email,
                'password'   => $newUser->password,
            ]);
        $response->assertStatus(201);

        $user = User::query()->where('email', $newUser->email)->first();

        self::assertNotNull($user);
    }

    public function test_register_existing_user()
    {
        $newUser = User::factory()->create();

        $response = $this->post(
            '/api/register',
            $newUser->toArray()
        );
        $response->assertStatus(302);
    }

    public function test_login_user()
    {
        $newUser = User::factory()->make();
        $response = $this->post(
            '/api/register',
            [
                'first_name' => $newUser->first_name,
                'last_name'  => $newUser->last_name,
                'role'       => $newUser->role,
                'email'      => $newUser->email,
                'password'   => 'pass1234',
            ]
        );
        $response->assertStatus(201);

        $loginResponse = $this->post(
            '/api/login',
            [
                'email'    => $newUser->email,
                'password' => 'pass1234',
            ]
        );

        self::assertNotEmpty($loginResponse->json('token'));
    }

    public function test_login_user_wrong_password()
    {
        $newUser = User::factory()->make();
        $this->post(
            '/api/register',
            [
                'first_name' => $newUser->first_name,
                'last_name'  => $newUser->last_name,
                'role'       => $newUser->role,
                'email'      => $newUser->email,
                'password'   => 'pass1234',
            ]);

        $loginResponse = $this->post(
            '/api/login',
            [
                'email'    => $newUser->email,
                'password' => 'wrongpass',
            ]
        );

        Log::info($loginResponse->json());

        $loginResponse->assertStatus(401);
    }

}
