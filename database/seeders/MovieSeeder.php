<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run()
    {
        // Crear 20 películas aleatorias
        Movie::factory(20)->create();

        // Creacion de pelis especificas
        $specificMovies = [
            [
                'title' => 'El Padrino',
                'synopsis' => 'La historia de la familia Corleone bajo el patriarcado de Don Vito Corleone.',
                'duration' => 175,
                'rating' => 'R',
                'release_date' => '1972-03-24',
                'photo_url' => 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
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

        foreach ($specificMovies as $movie) {
            Movie::create($movie);
        }
    }
}

