# Crear películas ficticias en Laravel

Para generar películas falsas (datos de prueba), podemos usar las
Factories de Laravel junto con la librería Faker. Aquí te muestro cómo:

## 1. Crear un Factory para Movie

```bash
php artisan make:factory MovieFactory --model=Movie
```

## 2. Implementar el Factory con datos realistas

```php:database/factories/MovieFactory.php
<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition()
    {
        $genres = ['Acción', 'Comedia', 'Drama', 'Ciencia Ficción', 'Terror',
            'Aventura', 'Romance', 'Animación'];
        
        return [
            'title' => $this->faker->unique()->sentence(3),
            'synopsis' => $this->faker->paragraph(3),
            'duration' => $this->faker->numberBetween(75, 210),
            'rating' => $this->faker->randomElement(['G', 'PG', 'PG-13', 'R', 'NC-17']),
            'release_date' => $this->faker->dateTimeBetween('-30 years', 'now'),
            'photo_url' => $this->faker->imageUrl(640, 480, 'movies', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
```

## 3. Crear un Seeder para Movies

```bash
php artisan make:seeder MovieSeeder
```

## 4. Implementar el Seeder

```php:database/seeders/MovieSeeder.php
<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run()
    {
        // Crear 20 películas aleatorias
        Movie::factory(20)->create();

        // También puedes crear películas específicas
        $specificMovies = [
            [
                'title' => 'El Padrino',
                'synopsis' => 'La historia de la familia Corleone bajo el patriarcado de Don Vito Corleone.',
                'duration' => 175,
                'rating' => 'R',
                'release_date' => '1972-03-24',
                'photo_url' => 'https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
            ],
            [
                'title' => 'Pulp Fiction',
                'synopsis' => 'Las vidas de dos mafiosos, un boxeador, la esposa de un gángster y un par de bandidos se entrelazan en cuatro historias de violencia y redención.',
                'duration' => 154,
                'rating' => 'R',
                'release_date' => '1994-10-14',
                'photo_url' => 'https://m.media-amazon.com/images/M/MV5BNGNhMDIzZTUtNTBlZi00MTRlLWFjM2ItYzViMjE3YzI5MjljXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_.jpg',
            ],
            [
                'title' => 'Interestelar',
                'synopsis' => 'Un equipo de exploradores viaja a través de un agujero de gusano en el espacio en un intento de garantizar la supervivencia de la humanidad.',
                'duration' => 169,
                'rating' => 'PG-13',
                'release_date' => '2014-11-07',
                'photo_url' => 'https://m.media-amazon.com/images/M/MV5BZjdkOTU3MDktN2IxOS00OGEyLWFmMjktY2FiMmZkNWIyODZiXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_.jpg',
            ],
        ];

        foreach ($specificMovies as $movie) {
            Movie::create($movie);
        }
    }
}
```

## 5. Actualizar el DatabaseSeeder principal

```php:database/seeders/DatabaseSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            MovieSeeder::class,
            // Otros seeders aquí...
        ]);
    }
}
```

## 6. Ejecutar los seeders

Ahora puedes ejecutar:

```bash
php artisan db:seed
```

O si quieres ejecutar solo el seeder de películas:

```bash
php artisan db:seed --class=MovieSeeder
```

## 7. Comando rápido para recrear la base de datos con datos ficticios

Si quieres un atajo para resetear toda la base de datos y llenarla con datos de prueba:

```bash
php artisan migrate:fresh --seed
```

Con esto tendrás 23 películas en total: 20 generadas aleatoriamente y
3 películas específicas con datos reales. Puedes ajustar las cantidades o añadir
más películas específicas según necesites.

¿Necesitas alguna modificación en estos datos o quieres añadir otro tipo de datos ficticios?
