<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Actor;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

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
    public function run(): void
    {
        // Verificar si las tablas existen
        if (!Schema::hasTable('movies') || !Schema::hasTable('genres') || !Schema::hasTable('actors')) {
            $this->command->error('Las tablas necesarias no existen. Ejecuta las migraciones primero.');
            return;
        }
        
        // Ejemplo de datos webscrapeados
        $movieData = [
            "titulo" => "28 días después",
            "url" => "https://www.cinesa.es/peliculas/28-dias-despues/HO00002240/",
            "imagen_local" => "/home/dev/gadget/data/imagenes/imagen_sin_titulo.jpg",
            "duracion" => "1h 55m",
            "fecha_estreno" => "21 mayo 2025",
            "generos" => [
                "Terror",
                "Drama",
                "Thriller"
            ],
            "clasificacion" => "No recomendada para menores de 18 años",
            "directores" => "Danny Boyle",
            "actores" => [
                "Brendan Gleeson",
                "Cillian Murphy",
                "Noah Huntley",
                "Christopher Eccleston",
                "David Schneider"
            ],
            "sinopsis" => "Cuatro semanas después de que un misterioso e incurable virus se extienda por todo el Reino Unido, un puñado de supervivientes intenta encontrar refugio.",
            "poster_local" => "/home/dev/gadget/data/imagenes/28_días_después.jpg",
            "sesiones" => [],
            "fecha_scraping" => "2025-05-20T04:41:31.424168"
        ];
        
        // Convertir duración a minutos
        $durationText = $movieData['duracion'] ?? '';
        $duration = 0;
        
        if (preg_match('/(\d+)h/', $durationText, $matches)) {
            $duration += (int)$matches[1] * 60;
        }
        
        if (preg_match('/(\d+)m/', $durationText, $matches)) {
            $duration += (int)$matches[1];
        }
        
        // Convertir fecha de estreno
        $releaseDate = null;
        if (!empty($movieData['fecha_estreno'])) {
            $dateText = $movieData['fecha_estreno'];
            $monthTranslations = [
                'enero' => 'January',
                'febrero' => 'February',
                'marzo' => 'March',
                'abril' => 'April',
                'mayo' => 'May',
                'junio' => 'June',
                'julio' => 'July',
                'agosto' => 'August',
                'septiembre' => 'September',
                'octubre' => 'October',
                'noviembre' => 'November',
                'diciembre' => 'December'
            ];
            
            foreach ($monthTranslations as $es => $en) {
                $dateText = str_ireplace($es, $en, $dateText);
            }
            
            $releaseDate = Carbon::parse($dateText);
        }
        
        // Crear la película con los campos disponibles en tu esquema
        $movie = new Movie();
        $movie->title = $movieData['titulo'];
        $movie->synopsis = $movieData['sinopsis'];
        $movie->duration = $duration;
        $movie->rating = $movieData['clasificacion'];
        $movie->release_date = $releaseDate;
        $movie->photo_url = $movieData['poster_local'] ?? $movieData['imagen_local'];
        $movie->slug = \Illuminate\Support\Str::slug($movieData['titulo']);
        $movie->save();
        
        // Sincronizar géneros
        $genreIds = [];
        foreach ($movieData['generos'] as $name) {
            $genre = Genre::firstOrCreate(['name' => $name]);
            $genreIds[] = $genre->id;
        }
        $movie->genres()->sync($genreIds);
        
        // Sincronizar actores
        $actorIds = [];
        foreach ($movieData['actores'] as $name) {
            $actor = Actor::firstOrCreate(['name' => $name]);
            $actorIds[] = $actor->id;
        }
        $movie->actors()->sync($actorIds);
        
        $this->command->info('Película "' . $movie->title . '" importada correctamente.');
    }
}
