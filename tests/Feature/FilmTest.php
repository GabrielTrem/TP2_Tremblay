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

    public function test_whenUpdatingFilmWithValidInformationAndAuthenticatedAsAdmin_thenUpdateFilm(): void
    {
        $this->seed();
        $id = 1;
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

        $response = $this->putJson("/api/films/$id", $json);

        $response->assertStatus(OK)
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
            "id" => $id,
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

    public function test_whenUpdatingFilmWithInvalidInformationAndAuthenticatedAsAdmin_thenReturnInvalidData(): void
    {
        $this->seed();
        $id = 1;
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        $json = [
            "title" => "Potato Film", 
            "release_year" => 1990,
            "length" => 60,
            "description" => "A film",
            "rating" => "PG",
            "special_features" => "Deleted Scenes,Behind the Scenes"
        ];

        $response = $this->putJson("/api/films/$id", $json);

        $response->assertStatus(INVALID_DATA);

        $this->assertDatabaseMissing('films', [
            "id" => $id,
            "title" => $json['title'], 
            "release_year" => $json['release_year'],
            "length" => $json['length'],
            "description" => $json['description'],
            "rating" => $json['rating'],
            "special_features" => $json['special_features']
        ]);
    }

    public function test_whenUpdatingFilmAndNotAuthenticatedAsAdmin_thenReturnForbiden(): void
    {
        $this->seed();
        $id = 1;
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

        $response = $this->putJson("/api/films/$id", $json);

        $response->assertStatus(FORBIDDEN);

        $this->assertDatabaseMissing('films', [
            "id" => $id,
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

    public function test_whenUpdatingFilmAndNotAuthenticated_thenReturnUnauthorized(): void
    {
        $this->seed();
        $id = 1;

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

        $response = $this->putJson("/api/films/$id", $json);

        $response->assertStatus(UNAUTHORIZED);

        $this->assertDatabaseMissing('films', [
            "id" => $id,
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

    public function test_whenUpdatingFilmPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $id = 1;
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
            $response = $this->putJson("/api/films/$id", $json);
            $response->assertStatus(OK);
        } 

        $response = $this->putJson("/api/films/$id", $json);
        $response->assertStatus(TOO_MANY_REQUESTS);
    }

    public function test_whenDeletingFilmWithValidIDAndAuthenticatedAsAdmin_thenDeleteFilmAndReturnNoContent(): void
    {
        $this->seed();
        $id = 1;
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        $response = $this->delete("/api/films/$id");

        $response->assertStatus(NO_CONTENT);

        $this->assertDatabaseMissing('films', [
            "id" => $id
        ]);
    }

    public function test_whenDeletingFilmWithInvalidIDAndAuthenticatedAsAdmin_thenReturnNotFound(): void
    {
        $this->seed();
        $id = 10000;
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        $response = $this->delete("/api/films/$id");

        $response->assertStatus(NOT_FOUND);
    }

    public function test_whenDeletingFilmAndNotAuthenticatedAsAdmin_thenReturnForbiden(): void
    {
        $this->seed();
        $id = 1;
        $this->postJson('/api/signin', ['login' => 'user', 'password' => 'password']);

        $response = $this->delete("/api/films/$id");

        $response->assertStatus(FORBIDDEN);

        $this->assertDatabaseHas('films', [
            "id" => $id
        ]);
    }

    public function test_whenDeletingFilmAndNotAuthenticated_thenReturnUnauthorized(): void
    {
        $this->seed();
        $id = 1;

        $response = $this->withHeaders([
            'Accept' => 'application/json'
        ])->delete("/api/films/$id"); //Me renvoyais un 500 aussi-non. Route [login] not defined

        $response->assertStatus(UNAUTHORIZED);

        $this->assertDatabaseHas('films', [
            "id" => $id
        ]);
    }

    public function test_whenDeletingFilmPassLimitOfRequestsPerMinute_returnCode429(): void
    {
        $this->seed();
        $id = 1;
        $this->postJson('/api/signin', ['login' => 'adminUser', 'password' => 'adminPassword']);

        
        for($i = 0; $i < THROTTLE_LIMIT; $i++){
            $film_id = $i + 1;
            $response = $this->delete("/api/films/$film_id");
            $response->assertStatus(NO_CONTENT);
        } 

        $response = $this->delete("/api/films/" . (THROTTLE_LIMIT + 1));
        $response->assertStatus(TOO_MANY_REQUESTS);
    }
}
