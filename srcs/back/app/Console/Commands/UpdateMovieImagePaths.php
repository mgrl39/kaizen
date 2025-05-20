<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use Illuminate\Support\Str;

class UpdateMovieImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:update-paths 
                            {old_path? : La ruta antigua o parte de ella que deseas reemplazar}
                            {new_path? : La nueva ruta base}
                            {--dry-run : Ejecutar sin realizar cambios reales}
                            {--all : Actualizar todas las películas, incluso si no contienen la ruta antigua}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza las rutas de las imágenes de películas manteniendo el nombre del archivo';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Obtener los parámetros o solicitar al usuario
        $oldPath = $this->argument('old_path') ?? $this->ask('Introduce la ruta antigua (o parte de ella) que deseas reemplazar:');
        $newPath = $this->argument('new_path') ?? $this->ask('Introduce la nueva ruta base:');
        $dryRun = $this->option('dry-run');
        $updateAll = $this->option('all');

        // Mostrar modo de ejecución
        if ($dryRun) {
            $this->info('Ejecutando en modo simulación (no se realizarán cambios reales).');
        }

        // Confirmar la operación
        $this->info("Se reemplazará '{$oldPath}' por '{$newPath}' en las rutas de imágenes de películas.");
        if (!$this->confirm('¿Estás seguro de que deseas continuar?', true)) {
            $this->info('Operación cancelada.');
            return 0;
        }

        // Obtener películas según el criterio
        $query = Movie::whereNotNull('photo_url');
        if (!$updateAll) {
            $query->where('photo_url', 'like', "%{$oldPath}%");
        }
        $movies = $query->get();

        if ($movies->isEmpty()) {
            $this->info("No se encontraron películas con rutas que contengan '{$oldPath}'.");
            return 0;
        }

        $this->info("Se encontraron {$movies->count()} películas para procesar.");
        
        $count = 0;
        $this->output->progressStart($movies->count());

        foreach ($movies as $movie) {
            // Solo procesar si la película tiene una URL de foto
            if ($movie->photo_url) {
                $originalPath = $movie->photo_url;
                
                // Extraer el nombre del archivo de la ruta actual
                $filename = basename($movie->photo_url);
                
                // Crear la nueva ruta
                if ($updateAll || Str::contains($movie->photo_url, $oldPath)) {
                    $newFullPath = rtrim($newPath, '/') . '/' . $filename;
                    
                    // Mostrar el cambio
                    $this->newLine();
                    $this->line("Película ID: {$movie->id} - {$movie->title}");
                    $this->line("  Ruta antigua: {$originalPath}");
                    $this->line("  Ruta nueva: {$newFullPath}");
                    
                    // Actualizar la película si no es una simulación
                    if (!$dryRun) {
                        $movie->photo_url = $newFullPath;
                        $movie->save();
                    }
                    
                    $count++;
                }
            }
            
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        
        if ($dryRun) {
            $this->info("Simulación completada. Se actualizarían {$count} películas.");
        } else {
            $this->info("Se actualizaron las rutas de {$count} películas correctamente.");
        }
        
        return 0;
    }
} 