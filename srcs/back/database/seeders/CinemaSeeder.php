<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cinema;
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
        
        // Crear un único cine
        $cinema = [
            'name' => 'Kaizen Cinema',
            'location' => 'Madrid',
        ];

        // Verificar y añadir campos adicionales si existen en la tabla
        if (in_array('address', $columns)) {
            $cinema['address'] = 'Calle Gran Vía, 42, 28013';
        }
        if (in_array('phone', $columns)) {
            $cinema['phone'] = '+34 912 345 678';
        }
        if (in_array('image_url', $columns)) {
            $cinema['image_url'] = 'https://source.unsplash.com/random/500x300/?cinema,theater';
        }
        if (in_array('rooms_count', $columns)) {
            $cinema['rooms_count'] = 8;
        }
        if (in_array('has_3d', $columns)) {
            $cinema['has_3d'] = true;
        }
        if (in_array('has_imax', $columns)) {
            $cinema['has_imax'] = true;
        }
        if (in_array('has_vip', $columns)) {
            $cinema['has_vip'] = true;
        }

        Cinema::create($cinema);
        
        $this->command->info('Se ha creado el cine "Kaizen Cinema" correctamente.');
    }
} 