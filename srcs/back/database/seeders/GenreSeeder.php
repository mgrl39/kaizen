<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Genre::truncate();
        $genres = [
            'Action',
            'Adventure',
            'Animation',
            'Comedy',
            'Crime',
            'Documentary',
            'Drama',
            'Fantasy',
            'Horror',
            'Mystery',
            'Romance',
            'Science Fiction',
            'Thriller',
            'War',
            'Western',
            'Musical',
            'Biography',
            'History',
            'Family',
            'Sport'
        ];
        foreach ($genres as $genre) {
            Genre::create([
                'name' => $genre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('Genres table seeded with 20 movie genres in English!');
    }
}
