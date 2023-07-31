<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_login_endpoint(): void
    {
        $user = User::factory()->createOne();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['access_token', 'token_type', 'expires_in']);

            $json->whereAllType([
                'access_token' => 'string',
                'token_type' => 'string',
                'expires_in' => 'integer',
            ]);

            $json->whereAll([
                'token_type' => 'bearer',
                'expires_in' => 3600,
            ])->etc();
        });
    }

    public function test_auth_login_endpoint_wrong_password(): void
    {
        $user = User::factory()->createOne();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthorized',
            ]);
        });
    }

    public function test_auth_login_endpoint_validation_email_and_password_required(): void
    {
        $response = $this->postJson('/api/login');

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The email field is required. (and 1 more error)',
                'errors' => [
                    'email' => [
                        'The email field is required.',
                    ],
                    'password' => [
                        'The password field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_auth_login_endpoint_validation_email_required(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password',
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The email field is required.',
                'errors' => [
                    'email' => [
                        'The email field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_auth_login_endpoint_validation_email_is_valid(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'email',
            'password' => 'password',
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The email field must be a valid email address.',
                'errors' => [
                    'email' => [
                        'The email field must be a valid email address.',
                    ]
                ]
            ]);
        });
    }

    public function test_auth_login_endpoint_validation_password_required(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'email@email.com',
        ]);

        $response->assertStatus(422);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message', 'errors']);

            $json->whereAll([
                'message' => 'The password field is required.',
                'errors' => [
                    'password' => [
                        'The password field is required.',
                    ]
                ]
            ]);
        });
    }

    public function test_auth_logout_endpoint(): void
    {
        $user = User::factory()->createOne();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $token = $response->json('access_token');

        $response = $this->postJson('/api/logout', [], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Successfully logged out.',
            ]);
        });
    }

    public function test_auth_logout_endpoint_without_token(): void
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }

    public function test_auth_user_endpoint(): void
    {
        $user = User::factory()->createOne();

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('access_token');

        $response = $this->getJson('/api/user', [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($user) {
            $json->hasAll(['id', 'name', 'email', 'email_verified_at', 'created_at', 'updated_at']);

            $json->whereAll([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at->jsonSerialize(),
                'created_at' => $user->created_at->jsonSerialize(),
                'updated_at' => $user->updated_at->jsonSerialize(),
            ]);
        });
    }

    public function test_auth_user_endpoint_without_token(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['message']);

            $json->whereAll([
                'message' => 'Unauthenticated.',
            ]);
        });
    }
}
