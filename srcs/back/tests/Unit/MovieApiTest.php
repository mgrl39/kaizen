<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_obtener_todas_las_peliculas()
    {
        // Crear 3 películas de prueba
        Movie::factory()->count(3)->create();

        // Hacer la petición a la API
        $response = $this->getJson('/api/v1/movies');

        // Verificar que la respuesta es correcta
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        // Verificar que hay datos en la respuesta
        $this->assertNotEmpty($response->json('data'));
    }

    /** @test */
    public function puede_obtener_una_pelicula_por_id()
    {
        // Crear una película de prueba
        $movie = Movie::factory()->create();

        // Hacer la petición a la API
        $response = $this->getJson("/api/v1/movies/{$movie->id}");

        // Verificar que la respuesta es correcta
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        // Verificar que el ID de la película en la respuesta coincide con el que pedimos
        $this->assertEquals($movie->id, $response->json('data.id'));
        
        // Verificar que el título también coincide
        $this->assertEquals($movie->title, $response->json('data.title'));
    }

    /** @test */
    public function devuelve_error_cuando_la_pelicula_no_existe()
    {
        // Intentar obtener una película que no existe
        $response = $this->getJson('/api/v1/movies/999');

        // Verificar que la respuesta es un error
        $response->assertStatus(404);
    }
}