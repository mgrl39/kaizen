<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Actor;
use App\Models\Director;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ImportMoviesFromJson extends Command
{
    protected $signature = 'movies:import {directory?}';
    protected $description = 'Importa películas desde archivos JSON';

    public function handle()
    {
        // Directorio donde están los archivos JSON
        $directory = $this->argument('directory') ?? '/home/usuario/gadget/data/peliculas';
        
        if (!File::isDirectory($directory)) {
            $this->error("El directorio {$directory} no existe.");
            return 1;
        }

        $files = File::files($directory);
        $totalFiles = count($files);
        $importedCount = 0;
        $errorCount = 0;

        $this->info("Encontrados {$totalFiles} archivos JSON para importar.");
        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($files as $file) {
            if ($file->getExtension() !== 'json') {
                $bar->advance();
                continue;
            }

            try {
                $jsonContent = File::get($file->getPathname());
                $movieData = json_decode($jsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->error("Error al decodificar JSON en {$file->getFilename()}: " . json_last_error_msg());
                    $errorCount++;
                    $bar->advance();
                    continue;
                }

                // Verificar si la película ya existe
                $existingMovie = Movie::where('title', $movieData['titulo'])->first();
                if ($existingMovie) {
                    // Actualizar película existente
                    $this->updateMovie($existingMovie, $movieData);
                    $bar->advance();
                    $importedCount++;
                    continue;
                }

                // Crear nueva película
                $movie = $this->createMovie($movieData);
                $importedCount++;
            } catch (\Exception $e) {
                $this->error("Error al procesar {$file->getFilename()}: {$e->getMessage()}");
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Importación completada: {$importedCount} películas importadas, {$errorCount} errores.");

        return 0;
    }

    private function createMovie(array $movieData)
    {
        // Convertir duración a minutos
        $duration = $this->convertDurationToMinutes($movieData['duracion'] ?? '');
        
        // Convertir fecha de estreno
        $releaseDate = $this->parseReleaseDate($movieData['fecha_estreno'] ?? null);
        
        // Crear la película
        $movie = new Movie();
        $movie->title = $movieData['titulo'];
        $movie->synopsis = $movieData['sinopsis'] ?? '';
        $movie->duration = $duration;
        $movie->rating = $movieData['clasificacion'] ?? '';
        $movie->release_date = $releaseDate;
        
        // Guardar directores
        $movie->directors = $movieData['directores'] ?? '';
        
        // Sincronizar directores
        if (!empty($movieData['directores'])) {
            $directorNames = explode(', ', $movieData['directores']);
            $this->syncDirectors($movie, $directorNames);
        }
        
        // Manejar las rutas de imágenes
        $originalImagePath = $movieData['poster_local'] ?? $movieData['imagen_local'] ?? '';
        $movie->original_image_path = $originalImagePath;
        $movie->photo_url = basename($originalImagePath);
        
        $movie->slug = Str::slug($movieData['titulo']);
        $movie->save();
        
        // Sincronizar géneros
        $this->syncGenres($movie, $movieData['generos'] ?? []);
        
        // Sincronizar actores
        $this->syncActors($movie, $movieData['actores'] ?? []);
        
        $this->line("  <info>✓</info> Importada: {$movie->title}");
        
        return $movie;
    }
    
    private function updateMovie(Movie $movie, array $movieData)
    {
        // Actualizar datos básicos
        $movie->synopsis = $movieData['sinopsis'] ?? $movie->synopsis;
        $movie->duration = $this->convertDurationToMinutes($movieData['duracion'] ?? '') ?: $movie->duration;
        $movie->rating = $movieData['clasificacion'] ?? $movie->rating;
        $movie->directors = $movieData['directores'] ?? $movie->directors;
        
        if (!empty($movieData['fecha_estreno'])) {
            $movie->release_date = $this->parseReleaseDate($movieData['fecha_estreno']);
        }
        
        // Actualizar rutas de imágenes
        $originalImagePath = $movieData['poster_local'] ?? $movieData['imagen_local'] ?? null;
        if ($originalImagePath) {
            $movie->original_image_path = $originalImagePath;
            $movie->photo_url = basename($originalImagePath);
        }
        
        $movie->save();
        
        // Sincronizar géneros y actores
        $this->syncGenres($movie, $movieData['generos'] ?? []);
        $this->syncActors($movie, $movieData['actores'] ?? []);
        
        $this->line("  <info>↻</info> Actualizada: {$movie->title}");
        
        return $movie;
    }
    
    private function syncGenres(Movie $movie, array $genreNames)
    {
        $genreIds = [];
        
        foreach ($genreNames as $name) {
            // Buscar el género por nombre o crearlo sin usar slug
            $genre = Genre::firstOrNew(['name' => $name]);
            
            // Si es nuevo, guardarlo sin asignar slug
            if (!$genre->exists) {
                $genre->save();
            }
            
            $genreIds[] = $genre->id;
        }
        
        $movie->genres()->sync($genreIds);
    }
    
    private function syncActors(Movie $movie, array $actorNames)
    {
        $actorIds = [];
        
        foreach ($actorNames as $name) {
            // Buscar el actor por nombre o crearlo sin usar slug
            $actor = Actor::firstOrNew(['name' => $name]);
            
            // Si es nuevo, guardarlo sin asignar slug
            if (!$actor->exists) {
                $actor->save();
            }
            
            $actorIds[] = $actor->id;
        }
        
        $movie->actors()->sync($actorIds);
    }
    
    private function syncDirectors(Movie $movie, array $directorNames)
    {
        $directorIds = [];
        
        foreach ($directorNames as $name) {
            $director = Director::firstOrCreate(['name' => $name]);
            $directorIds[] = $director->id;
        }
        
        $movie->directors()->sync($directorIds);
    }
    
    private function convertDurationToMinutes($durationText)
    {
        $minutes = 0;
        
        if (preg_match('/(\d+)h/', $durationText, $matches)) {
            $minutes += (int)$matches[1] * 60;
        }
        
        if (preg_match('/(\d+)m/', $durationText, $matches)) {
            $minutes += (int)$matches[1];
        }
        
        return $minutes;
    }
    
    private function parseReleaseDate($dateText)
    {
        if (empty($dateText)) {
            return null;
        }
        
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
        
        try {
            return Carbon::parse($dateText);
        } catch (\Exception $e) {
            $this->warn("No se pudo parsear la fecha: {$dateText}");
            return null;
        }
    }
} 