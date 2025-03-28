<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class GeneralSystemTest extends TestCase
{
    /**
     * Test básico del sistema
     */
    public function test_system_configuration()
    {
        echo "\n🔍 Iniciando verificación del sistema...\n";

        // 1. Verificar versión de PHP
        $phpVersion = PHP_VERSION;
        $phpValid = version_compare($phpVersion, '8.1.0', '>=');
        echo ($phpValid ? "✅" : "❌") . " PHP Version: $phpVersion\n";
        $this->assertTrue($phpValid, 'PHP version must be 8.1 or higher');

        // 2. Verificar conexión MySQL
        try {
            DB::connection()->getPdo();
            echo "✅ Conexión MySQL: OK\n";
        } catch (\Exception $e) {
            echo "❌ Conexión MySQL: Error - " . $e->getMessage() . "\n";
            $this->fail('Could not connect to MySQL');
        }

        // 3. Verificar que existe la base de datos
        $dbName = config('database.connections.mysql.database');
        try {
            $hasDb = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$dbName]);
            echo ($hasDb ? "✅" : "❌") . " Base de datos '$dbName': " . ($hasDb ? "Existe" : "No existe") . "\n";
            $this->assertTrue(!empty($hasDb), "Database '$dbName' does not exist");
        } catch (\Exception $e) {
            echo "❌ Error al verificar base de datos: " . $e->getMessage() . "\n";
            $this->fail('Error checking database');
        }

        // 4. Verificar tablas del sistema
        $requiredTables = [
            'actors',
            'admin_users',
            'booking_seats',
            'bookings',
            'cinemas',
            'functions',
            'genres',
            'manages',
            'migrations',
            'movie_actor',
            'movie_genre',
            'movies',
            'rooms',
            'seats',
            'users',
        ];

        $missingTables = [];
        foreach ($requiredTables as $table) {
            try {
                $exists = DB::getSchemaBuilder()->hasTable($table);
                if (!$exists) {
                    $missingTables[] = $table;
                }
            } catch (\Exception $e) {
                $missingTables[] = $table;
            }
        }

        if (empty($missingTables)) {
            echo "✅ Tablas del sistema: OK\n";
        } else {
            echo "❌ Tablas faltantes: " . implode(', ', $missingTables) . "\n";
            $this->fail('Missing required tables: ' . implode(', ', $missingTables));
        }

        echo "\n✨ Verificación completada\n";
    }

    /**
     * Test de rendimiento básico de la base de datos
     */
    public function test_database_performance()
    {
        $startTime = microtime(true);

        // Realizar una consulta simple
        DB::table('users')->limit(1)->get();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000; // Convertir a milisegundos

        // La consulta no debería tomar más de 100ms
        $this->assertLessThan(
            100,
            $queryTime,
            "Database query took too long: {$queryTime}ms"
        );
    }
}

