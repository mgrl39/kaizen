<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

/**
 * @class MovieSeeder
 * @brief Seeder para poblar la tabla de películas con datos iniciales
 * 
 * Este seeder crea dos tipos de películas:
 * 1. 20 películas aleatorias generadas por la factory
 * 2. Películas específicas con datos predefinidos
 */
class MovieSeeder extends Seeder
{
    /**
     * @brief Ejecuta el seeder
     * @return void
     * 
     * Primero crea 20 películas aleatorias usando la factory de Movie.
     * Luego crea películas específicas con datos predefinidos.
     */
    public function run()
    {
        // Crear 20 películas aleatorias usando la factory
        Movie::factory(20)->create();

        // Array de películas específicas con datos predefinidos
        $specificMovies = [
            [
                'title' => 'El Padrino',
                'synopsis' => 'La historia de la familia Corleone bajo el patriarcado de Don Vito Corleone.',
                'duration' => 175, // Duración en minutos
                'rating' => 'R', // Clasificación por edades
                'release_date' => '1972-03-24', // Fecha de estreno
                'photo_url' => 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg', // URL de la imagen
            ],
            [
                'title' => 'Pulp Fiction',
                'synopsis' => 'Las vidas de dos mafiosos, un boxeador, la esposa de un gángster y un par de bandidos se entrelazan en cuatro historias de violencia y redención.',
                'duration' => 154,
                'rating' => 'R',
                'release_date' => '1994-10-14',
                'photo_url' => 'https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
            ],
            [
                'title' => 'Interestelar',
                'synopsis' => 'Un equipo de exploradores viaja a través de un agujero de gusano en el espacio en un intento de garantizar la supervivencia de la humanidad.',
                'duration' => 169,
                'rating' => 'PG-13',
                'release_date' => '2014-11-07',
                'photo_url' => 'https://m.media-amazon.com/images/M/MV5BZjdkOTU3MDktN2IxOS00OGEyLWFmMjktY2FiMmZkNWIyODZiXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg',
            ],
        ];

        // Crear cada película específica en la base de datos
        foreach ($specificMovies as $movie) {
            Movie::create($movie);
        }
    }
}
