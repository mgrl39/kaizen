<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genres = ['Acción', 'Comedia', 'Drama', 'Ciencia Ficción', 'Terror', 'Aventura', 'Romance', 'Animación'];
        
        return [
            'title' => $this->faker->unique()->sentence(3),
            'synopsis' => $this->faker->paragraph(3),
            'duration' => $this->faker->numberBetween(75, 210),
            'rating' => $this->faker->randomElement(['G', 'PG', 'PG-13', 'R', 'NC-17']),
            'release_date' => $this->faker->dateTimeBetween('-30 years', 'now'),
            'photo_url' => $this->faker->imageUrl(640, 480, 'movies', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
