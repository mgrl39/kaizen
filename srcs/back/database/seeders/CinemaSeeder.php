<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cinema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CinemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si la tabla existe
        if (!Schema::hasTable('cinemas')) {
            $this->command->error('La tabla "cinemas" no existe. Ejecuta las migraciones primero con: php artisan migrate');
            return;
        }
        
        // Verificar la estructura de la tabla
        $columns = Schema::getColumnListing('cinemas');
        $requiredColumns = ['name', 'location'];
        
        // Verificar si tenemos al menos las columnas básicas
        if (!empty(array_diff($requiredColumns, $columns))) {
            $this->command->error('La tabla "cinemas" no tiene la estructura esperada. Verifica tus migraciones.');
            return;
        }
        
        // Adaptar los datos según el esquema actual
        $cinemas = [
            [
                'name' => 'Cinesa La Maquinista',
                'location' => 'Barcelona',
            ],
            [
                'name' => 'Yelmo Cines Ideal',
                'location' => 'Madrid',
            ],
            [
                'name' => 'Cines Callao',
                'location' => 'Madrid',
            ],
            [
                'name' => 'Cinesa Diagonal',
                'location' => 'Barcelona',
            ],
            [
                'name' => 'Cines Aragonia',
                'location' => 'Zaragoza',
            ],
            [
                'name' => 'OCine Aqua',
                'location' => 'Valencia',
            ],
        ];

        // Verificar y añadir campos adicionales si existen en la tabla
        foreach ($cinemas as &$cinema) {
            if (in_array('address', $columns)) {
                $cinema['address'] = $cinema['name'] . ' Address';
            }
            if (in_array('phone', $columns)) {
                $cinema['phone'] = '+34 ' . rand(900000000, 999999999);
            }
            if (in_array('image_url', $columns)) {
                $cinema['image_url'] = 'https://source.unsplash.com/random/500x300/?cinema,' . rand(1, 10);
            }
            if (in_array('rooms_count', $columns)) {
                $cinema['rooms_count'] = rand(5, 15);
            }
            if (in_array('has_3d', $columns)) {
                $cinema['has_3d'] = (bool)rand(0, 1);
            }
            if (in_array('has_imax', $columns)) {
                $cinema['has_imax'] = (bool)rand(0, 1);
            }
            if (in_array('has_vip', $columns)) {
                $cinema['has_vip'] = (bool)rand(0, 1);
            }
        }

        foreach ($cinemas as $cinema) {
            Cinema::create($cinema);
        }
        
        $this->command->info('Se han creado ' . count($cinemas) . ' cines correctamente.');
    }
} 