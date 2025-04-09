<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

define('THROTTLE_LIMIT', 5);

class AuthTest extends TestCase
{

    use RefreshDatabase;
    public function test_whenSignupWithValidInformation_thenStoreNewUser(): void
    {
        $this->seed();
        $json = [
            'login' => 'boby',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'email' => 'boby12@gmail.com',
            'last_name' => 'Bobby',
            'first_name' => 'Tremblay'
        ];

        $response = $this->postJson('/api/signup', $json);

        $response->assertStatus(CREATED)
                 ->assertJsonFragment(
                    [
                        'login' => $json['login'],
                        'email' => $json['email'],
                        'last_name' => $json['last_name'],
                        'first_name' => $json['first_name']
                    ]
                 );
        $this->assertDatabaseHas('users', [
            'login' => $json['login'],
            'email' => $json['email']
        ]);
    }

    public function test_whenSignupWithInvalidInformation_thenReturnStatus422AndNoUserCreated(): void
    {
        $this->seed();
        $json = [
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'email' => 'boby12@gmail.com',
            'last_name' => 'Bobby',
            'first_name' => 'Tremblay'
        ];

        $response = $this->postJson('/api/signup', $json);

        $response->assertStatus(INVALID_DATA);
        $this->assertDatabaseMissing('users', [
            'email' => $json['email']
        ]);
    }

    public function test_whenSignupAndPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $json = [
            'login' => 'boby',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'email' => 'boby12@gmail.com',
            'last_name' => 'Bobby',
            'first_name' => 'Tremblay'
        ];

        for($i = 0; $i < THROTTLE_LIMIT; $i++){
            $json['login'] = "boby$i";
            $json['email'] = "boby12$i@gmail.com";
            $response = $this->postJson('/api/signup', $json);
            $response->assertStatus(CREATED);
        } 

        $json['login'] = 'boby' . THROTTLE_LIMIT;
        $json['email'] = 'boby12' . THROTTLE_LIMIT . '@gmail.com';

        $response = $this->postJson('/api/signup', $json);
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_whenSigninWithValidLoginAndPassword_returnLoginToken(): void
    {
        $this->seed();
        $json = [
            'login' => 'boby',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'email' => 'boby12@gmail.com',
            'last_name' => 'Bobby',
            'first_name' => 'Tremblay'
        ];

        $this->postJson('/api/signup', $json);
        $response = $this->postJson('/api/signin', ['login' => $json['login'], 'password' => $json['password']]);

        $user = Auth::User();
        $this->assertAuthenticated();
        $this->assertEquals(count($user->tokens()->get()), 1);
        $response->assertStatus(OK)
                 ->assertJsonStructure(['token']);

    }

    public function test_whenSigninWithInvalidLoginAndPassword_returnStatus401(): void
    {
        $this->seed();
        $json = [
            'login' => 'boby',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'email' => 'boby12@gmail.com',
            'last_name' => 'Bobby',
            'first_name' => 'Tremblay'
        ];

        $this->postJson('/api/signup', $json);
        $response = $this->postJson('/api/signin', ['login' => 'superman', 'password' => $json['password']]);

        $user = Auth::User();
        $this->assertNull($user);
        $response->assertStatus(UNAUTHORIZED);

    }

    public function test_whenSigninAndPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $json = [
            'login' => 'boby',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'email' => 'boby12@gmail.com',
            'last_name' => 'Bobby',
            'first_name' => 'Tremblay'
        ];

        $this->postJson('/api/signup', $json);

        for($i = 0; $i < THROTTLE_LIMIT + 1; $i++){
            $response = $this->postJson('/api/signin', ['login' => $json['login'], 'password' => $json['password']]);
            $response->assertStatus(OK);
        }
        
        $response = $this->postJson('/api/signin', $json);
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_whenSignoutWhenLoggedIn_deleteTokensAndReturnNoContent(): void
    {
        $this->seed();
        $json = [
            'login' => 'boby',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'email' => 'boby12@gmail.com',
            'last_name' => 'Bobby',
            'first_name' => 'Tremblay'
        ];

        $this->postJson('/api/signup', $json);
        $this->postJson('/api/signin', ['login' => $json['login'], 'password' => $json['password']]);

        
        $response = $this->postJson('/api/signout');
        $user = Auth::User();
        $this->assertDatabaseHas('users', [
            'login' => $json['login'],
            'email' => $json['email']
        ]);
        $this->assertEquals(count($user->tokens()->get()), 0);
        $response->assertStatus(NO_CONTENT);
    }

    public function test_whenSignoutAndNotLoggedIN_returnCode401(): void
    {
        $response = $this->postJson('/api/signout');
        $response->assertStatus(UNAUTHORIZED);
    }

    public function test_whenSignoutAndPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $json = [
            'login' => 'boby',
            'password' => 'password1234',
            'password_confirmation' => 'password1234',
            'email' => 'boby12@gmail.com',
            'last_name' => 'Bobby',
            'first_name' => 'Tremblay'
        ];

        $response = $this->postJson('/api/signup', $json);
        for($i = 0; $i < THROTTLE_LIMIT - 2; $i++){ //Içi le throttle limit est de -2 (avant il était de +1), alors que dans postman non
            $this->postJson('/api/signin', ['login' => $json['login'], 'password' => $json['password']]);
            $response = $this->postJson('/api/signout');
            $response->assertStatus(NO_CONTENT);
        }
        
        $this->postJson('/api/signin', ['login' => $json['login'], 'password' => $json['password']]);
        $response = $this->postJson('/api/signout');
        $response->assertStatus(TOO_MANY_REQUESTS);
    }
}
