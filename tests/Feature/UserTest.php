<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function test_whenGettingUserWhileAuthenticated_thenShowAuthenticatedUser(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);
        $user = Auth::user();

        $response = $this->getJson("/api/user");
        
        $response->assertStatus(OK)
                ->assertJsonFragment(
                    [
                        "login" => $user->login,
                        "password" => $user->password,
                        "email" => $user->email,
                        "first_name" => $user->first_name,
                        "last_name" => $user->last_name
                    ]
                );
    }

    public function test_whenGettingUserWhileNotAuthenticated_thenReturnUnauthorized(): void
    {
        $this->seed();

        $response = $this->getJson("/api/user");
        
        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_whenGettingUserAndPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        for($i = 0; $i < THROTTLE_LIMIT; $i++){
            $response = $this->getJson("/api/user");
            $response->assertStatus(OK);
        } 

        $response = $this->getJson("/api/user");
        $response->assertStatus(TOO_MANY_REQUESTS);
    } 

    public function test_whenUpdatingUserPasswordWithValidPasswordAndConfirmationWhileAuthenticated_thenUpdateUserPassword(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);
        $user = Auth::user();

        $json = [
            "password" => "newPassword",
            "password_confirmation" => "newPassword"
        ];

        $response = $this->patchJson("/api/user", $json);
        
        $response->assertStatus(OK)
                ->assertJsonFragment(
                    [
                        "login" => "adminUser",
                        "email" => $user->email,
                        "first_name" => $user->first_name,
                        "last_name" => $user->last_name
                    ]
                );
                
        $this->assertDatabaseHas('users', [
            "login" => "adminUser",
            "email" => $user->email,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name
        ]); 

        $user->refresh();
        $this->assertTrue(Hash::check($json['password'], $user->password));
    }

    public function test_whenUpdatingUserPasswordWithValidPasswordAndNotMatchingConfirmationWhileAuthenticated_thenReturnInvalidData(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);
        $user = Auth::user();

        $json = [
            "password" => "newPassword",
            "password_confirmation" => "new"
        ];

        $response = $this->patchJson("/api/user", $json);
        
        $response->assertStatus(INVALID_DATA);
    }

    public function test_whenUpdatingUserPasswordWithValidPasswordAndConfirmationWhileNotAuthenticated_thenReturnUnauthorized(): void
    {
        $this->seed();

        $json = [
            "password" => "newPassword",
            "password_confirmation" => "newPassword"
        ];

        $response = $this->patchJson("/api/user", $json);
        
        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_whenUpdatingUserPasswordAndPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        $json = [
            "password" => "newPassword",
            "password_confirmation" => "newPassword"
        ];

        
        for($i = 0; $i < THROTTLE_LIMIT; $i++){
            $response = $this->patchJson("/api/user", $json);
            $response->assertStatus(OK);
        } 

        $response = $this->patchJson("/api/user", $json);
        $response->assertStatus(TOO_MANY_REQUESTS);
    } 
}
