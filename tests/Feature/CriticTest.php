<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Film;
use App\Models\Critic;
use App\Models\Language;

class CriticTest extends TestCase
{

    use RefreshDatabase;
    public function test_whenCreatingCriticWithValidInformationAndAuthenticated_thenStoreNewCritic(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);
        $json = [
            "user_id" => 1, 
            "film_id" => 7,
            "score" => 100.0,
            "comment" => "aaaaaaaaaaaaaaaaaaaaaa"
        ];

        $response = $this->postJson('/api/critics', $json);

        $response->assertStatus(CREATED)
                 ->assertJsonFragment(
                    [
                        'user_id' => $json['user_id'],
                        'film_id' => $json['film_id'],
                        'score' => $json['score'],
                        'comment' => $json['comment']
                    ]
                 );
        $this->assertDatabaseHas('critics', [
            'user_id' => $json['user_id'],
            'film_id' => $json['film_id']
        ]);
    }

    public function test_whenCreatingCriticWithInvalidInformationAndAuthenticated_thenReturnInvalidData422(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);
        $json = [
            "user_id" => 1, 
            "film_id" => 7,
            "score" => 1000.0,
        ];

        $response = $this->postJson('/api/critics', $json);

        $response->assertStatus(INVALID_DATA);
        $this->assertDatabaseMissing('critics', [
            'user_id' => $json['user_id'],
            'film_id' => $json['film_id']
        ]);
    }

    public function test_whenCreatingCriticOnFilmThatAlreadyHasCritic_thenReturnConflict409(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);
        $json = [
            "user_id" => 1, 
            "film_id" => 7,
            "score" => 100.0,
            "comment" => "aaaaaaaaaaaaaaaaaaaaaa"
        ];

        $this->postJson('/api/critics', $json);
        $response = $this->postJson('/api/critics', $json);

        $response->assertStatus(CONFLICT);
    }

    public function test_whenCreatingCriticWithValidInformationButNotAuthenticated_thenReturnUnauthorized(): void
    {
        $this->seed();
        $json = [
            "user_id" => 1, 
            "film_id" => 7,
            "score" => 100.0,
            "comment" => "aaaaaaaaaaaaaaaaaaaaaa"
        ];

        $response = $this->postJson('/api/critics', $json);

        $response->assertStatus(UNAUTHORIZED);
        $this->assertDatabaseMissing('critics', [
            'user_id' => $json['user_id'],
            'film_id' => $json['film_id']
        ]);
    }

    public function test_whenCreatingCriticPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);
        $json = [
            "user_id" => 1, 
            "film_id" => 7,
            "score" => 100.0,
            "comment" => "aaaaaaaaaaaaaaaaaaaaaa"
        ];

        for($i = 0; $i < THROTTLE_LIMIT; $i++){
            $film_id = $i + 1;
            $json['film_id'] = $film_id;
            $response = $this->postJson('/api/critics', $json);
            $response->assertStatus(CREATED);
        } 

        $json['film_id'] = THROTTLE_LIMIT + 1;
        $response = $this->postJson('/api/signup', $json);
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

}
