<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * @file Kernel.php
 * @brief Kernel de consola que gestiona comandos y tareas programadas
 *
 * Esta clase es el componente central del sistema de consola de Laravel.
 * Se encarga de:
 * - Definir el programador de tareas de la aplicación
 * - Registrar los comandos personalizados disponibles
 * - Gestionar la ejecución de comandos programados
 *
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GenerateFunctions::class,
    ];

    /**
     * @brief Define la programación de tareas de la aplicación
     *
     * Este método permite programar comandos Artisan y tareas para que se ejecuten
     * periódicamente según un intervalo definido (cada minuto, hora, día, etc.).
     * Se ejecuta automáticamente por el programador de tareas de Laravel.
     *
     * Ejemplos de uso:
     * - $schedule->command('backup:run')->daily();
     * - $schedule->exec('php -v')->hourly();
     * - $schedule->job(new ProcessPodcast)->everyFiveMinutes();
     *
     * @param Schedule $schedule El objeto programador de tareas
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * @brief Registra los comandos disponibles para la aplicación
     *
     * Este método carga automáticamente todos los comandos ubicados en el
     * directorio Commands y registra cualquier comando definido en routes/console.php.
     * Se ejecuta durante la inicialización de la aplicación.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
