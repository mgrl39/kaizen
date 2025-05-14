<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class VerifyDatabaseStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify database structure and run migrations if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifying database structure...');
        
        // Get list of tables that should exist based on our migrations
        $expectedTables = [
            'users',
            'movies',
            'genres',
            'movie_genre',
            'cinemas',
            'rooms',
            'functions',
            'seats',
            'bookings',
            'booking_seats',
            'actors',
            'movie_actor'
        ];
        
        $missingTables = [];
        
        // Check which tables are missing
        foreach ($expectedTables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }
        
        if (empty($missingTables)) {
            $this->info('All expected tables exist. Database structure is valid.');
            return 0;
        }
        
        $this->warn('Missing tables detected: ' . implode(', ', $missingTables));
        
        // Check migration status
        $this->info('Checking migration status...');
        
        // Check if migrations table exists
        if (!Schema::hasTable('migrations')) {
            $this->warn('Migrations table not found. This seems to be a fresh database.');
            
            if ($this->confirm('Would you like to run all migrations?', true)) {
                $this->info('Running migrations...');
                Artisan::call('migrate');
                $this->info(Artisan::output());
                return 0;
            }
            
            return 1;
        }
        
        // Get list of migrations that have been run
        $ranMigrations = DB::table('migrations')->pluck('migration')->toArray();
        
        // Check if genres migration has been run
        $genresMigration = '2023_01_01_000011_create_genres_table';
        if (!in_array($genresMigration, $ranMigrations)) {
            $this->warn("Genres migration hasn't been executed yet");
            
            if ($this->confirm('Would you like to run all pending migrations?', true)) {
                $this->info('Running migrations...');
                Artisan::call('migrate');
                $this->info(Artisan::output());
                return 0;
            }
        } else {
            $this->warn('The migrations have been run but some tables are still missing!');
            $this->warn('This could indicate database corruption or manual table deletion.');
            
            if ($this->confirm('Would you like to try to fix this by refreshing migrations?', false)) {
                $this->info('Running migrate:refresh...');
                Artisan::call('migrate:refresh');
                $this->info(Artisan::output());
                return 0;
            }
        }
        
        return 1;
    }
} 