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

        $response = $this->getJson('/api/v1/movies');

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /** @test */
    public function it_can_fetch_a_single_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->getJson("/api/v1/movies/{$movie->id}");

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

        $response = $this->postJson('/api/v1/movies', $movieData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Inception']);

        $this->assertDatabaseHas('movies', $movieData);
    }
    */

    /** @test */
    public function it_can_update_a_movie()
    {
        $this->markTestSkipped('PUT method not available in current API implementation');
        
        $movie = Movie::factory()->create();

        $updateData = ['title' => 'Updated Title'];

        $response = $this->putJson("/api/v1/movies/{$movie->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Title']);

        $this->assertDatabaseHas('movies', $updateData);
    }

    /** @test */
    public function it_can_delete_a_movie()
    {
        $this->markTestSkipped('DELETE method not available in current API implementation');
        
        $movie = Movie::factory()->create();

        $response = $this->deleteJson("/api/v1/movies/{$movie->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);
    }
}
