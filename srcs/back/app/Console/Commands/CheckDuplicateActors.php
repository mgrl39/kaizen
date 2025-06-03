<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Actor;
use Illuminate\Support\Facades\DB;

class CheckDuplicateActors extends Command
{
    protected $signature = 'actors:check-duplicates';
    protected $description = 'Verifica y muestra actores duplicados';

    public function handle()
    {
        $this->info('Buscando actores duplicados...');

        // Corregido para PostgreSQL
        $duplicates = DB::table('actors')
            ->select('name', 
                    DB::raw('COUNT(*) as total'), 
                    DB::raw('string_agg(id::text, \',\') as ids'))
            ->groupBy('name')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No se encontraron actores duplicados.');
            return;
        }

        $this->warn('Se encontraron los siguientes duplicados:');
        foreach ($duplicates as $duplicate) {
            $this->line("\nNombre: {$duplicate->name}");
            $this->line("Cantidad: {$duplicate->total}");
            $this->line("IDs: {$duplicate->ids}");
            
            // Mostrar las películas asociadas a cada versión del actor
            $actorIds = explode(',', $duplicate->ids);
            foreach ($actorIds as $id) {
                $movies = DB::table('movies')
                    ->join('movie_actor', 'movies.id', '=', 'movie_actor.movie_id')
                    ->where('movie_actor.actor_id', $id)
                    ->pluck('title');
                    
                $this->line("ID {$id} aparece en: " . $movies->implode(', '));
            }
        }

        if ($this->confirm('¿Deseas corregir los duplicados?')) {
            foreach ($duplicates as $duplicate) {
                $actorIds = explode(',', $duplicate->ids);
                $primaryId = $actorIds[0]; // Mantener el primer ID
                
                // Actualizar las relaciones movie_actor
                DB::table('movie_actor')
                    ->whereIn('actor_id', array_slice($actorIds, 1))
                    ->update(['actor_id' => $primaryId]);
                
                // Eliminar los registros duplicados
                DB::table('actors')
                    ->whereIn('id', array_slice($actorIds, 1))
                    ->delete();
                
                $this->info("Corregido: {$duplicate->name}");
            }
            $this->info('Duplicados corregidos exitosamente.');
        }
    }
}