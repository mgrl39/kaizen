<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * @class DatabaseSeeder
 * @brief Seeder principal que llama a los demás seeders
 * 
 * Este seeder es el punto de entrada para la ejecución de todos los seeders
 * de la aplicación. Se encarga de llamar a los demás seeders en el orden adecuado.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * @brief Ejecuta los seeders de la aplicación
     * @return void
     * 
     * Este método llama a los demás seeders que se necesitan para poblar
     * la base de datos con datos iniciales.
     */
    public function run(): void
    {
        $this->call(
            [
            MovieSeeder::class,
            ]
        );
    }
}
