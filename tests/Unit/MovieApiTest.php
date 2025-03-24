<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Movie;

class MovieApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_all_movies()
    {
        Movie::factory()->count(3)->create();

        $response = $this->getJson('/api/movies');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_fetch_a_single_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->getJson("/api/movies/{$movie->id}");

        $response->assertStatus(200)
                 ->assertJson(['id' => $movie->id]);
    }

    /** @test */
    /*
    public function it_can_create_a_movie()
    {
        $movieData = [
            'title' => 'Inception',
            'description' => 'A mind-bending thriller',
            'year' => 2010
        ];

        $response = $this->postJson('/api/movies', $movieData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Inception']);

        $this->assertDatabaseHas('movies', $movieData);
    }
    */

    /** @test */
    public function it_can_update_a_movie()
    {
        $movie = Movie::factory()->create();

        $updateData = ['title' => 'Updated Title'];

        $response = $this->putJson("/api/movies/{$movie->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Title']);

        $this->assertDatabaseHas('movies', $updateData);
    }

    /** @test */
    public function it_can_delete_a_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->deleteJson("/api/movies/{$movie->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);
    }
}

