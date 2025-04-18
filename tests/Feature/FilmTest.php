<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Language;
use App\Models\Film;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase;
    public function test_whenCreatingFilmWithValidInformationAndAuthenticatedAsAdmin_thenStoreNewFilm(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        $json = [
            "title" => "Potato Film", 
            "release_year" => 1990,
            "length" => 60,
            "description" => "A film",
            "rating" => "PG",
            "special_features" => "Deleted Scenes,Behind the Scenes",
            "image" => "asd",
            "language_id" => 1
        ];

        $response = $this->postJson('/api/films', $json);

        $response->assertStatus(CREATED)
                 ->assertJsonFragment(
                    [
                        "title" => $json['title'], 
                        "release_year" => $json['release_year'],
                        "length" => $json['length'],
                        "description" => $json['description'],
                        "rating" => $json['rating'],
                        "special_features" => $json['special_features'],
                        "image" => $json['image'],
                        "language_id" => $json['language_id']
                    ]
                 );

        $this->assertDatabaseHas('films', [
            "title" => $json['title'], 
            "release_year" => $json['release_year'],
            "length" => $json['length'],
            "description" => $json['description'],
            "rating" => $json['rating'],
            "special_features" => $json['special_features'],
            "image" => $json['image'],
            "language_id" => $json['language_id']
        ]);
    }

    public function test_whenCreatingFilmWithInvalidInformationAndAuthenticatedAsAdmin_thenReturnInvalidData(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        $json = [
            "title" => "Potato Film", 
            "release_year" => 1990,
            "length" => "a",
            "description" => "A film",
            "rating" => "PG",
            "special_features" => "Deleted Scenes,Behind the Scenes",
        ];

        $response = $this->postJson('/api/films', $json);

        $response->assertStatus(INVALID_DATA);

        $this->assertDatabaseMissing('films', [
            "title" => $json['title'], 
            "release_year" => $json['release_year'],
            "length" => $json['length'],
            "description" => $json['description'],
            "rating" => $json['rating'],
            "special_features" => $json['special_features']
        ]);
    }

    public function test_whenCreatingFilmAndNotAuthenticatedAsAdmin_thenReturnForbiden(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'user', 'password' => 'password']);

        $json = [
            "title" => "Potato Film", 
            "release_year" => 1990,
            "length" => 60,
            "description" => "A film",
            "rating" => "PG",
            "special_features" => "Deleted Scenes,Behind the Scenes",
            "image" => "asd",
            "language_id" => 1
        ];

        $response = $this->postJson('/api/films', $json);

        $response->assertStatus(FORBIDDEN);

        $this->assertDatabaseMissing('films', [
            "title" => $json['title'], 
            "release_year" => $json['release_year'],
            "length" => $json['length'],
            "description" => $json['description'],
            "rating" => $json['rating'],
            "special_features" => $json['special_features'],
            "image" => $json['image'],
            "language_id" => $json['language_id']
        ]);
    }

    public function test_whenCreatingFilmAndNotAuthenticated_thenReturnUnauthorized(): void
    {
        $this->seed();

        $json = [
            "title" => "Potato Film", 
            "release_year" => 1990,
            "length" => 60,
            "description" => "A film",
            "rating" => "PG",
            "special_features" => "Deleted Scenes,Behind the Scenes",
            "image" => "asd",
            "language_id" => 1
        ];

        $response = $this->postJson('/api/films', $json);

        $response->assertStatus(UNAUTHORIZED);

        $this->assertDatabaseMissing('films', [
            "title" => $json['title'], 
            "release_year" => $json['release_year'],
            "length" => $json['length'],
            "description" => $json['description'],
            "rating" => $json['rating'],
            "special_features" => $json['special_features'],
            "image" => $json['image'],
            "language_id" => $json['language_id']
        ]);
    }

    public function test_whenCreatingFilmPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        $json = [
            "title" => "Potato Film", 
            "release_year" => 1990,
            "length" => 60,
            "description" => "A film",
            "rating" => "PG",
            "special_features" => "Deleted Scenes,Behind the Scenes",
            "image" => "asd",
            "language_id" => 1
        ];

        for($i = 0; $i < THROTTLE_LIMIT; $i++){
            $response = $this->postJson('/api/films', $json);
            $response->assertStatus(CREATED);
        } 

        $response = $this->postJson('/api/films', $json);
        $response->assertStatus(TOO_MANY_REQUESTS);
    }
}
