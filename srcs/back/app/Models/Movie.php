<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Movie extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',                // título de la película
        'synopsis',             // sinopsis
        'duration',             // duración en minutos (entero)
        'rating',               // clasificación de edad
        'release_date',         // fecha de estreno
        'photo_url',            // URL de la imagen/poster
        'slug'                  // slug para URLs amigables
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'release_date' => 'date',
        'duration' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movie) {
            if (empty($movie->slug)) {
                $movie->slug = Str::slug($movie->title);
            }
        });

        static::updating(function ($movie) {
            if ($movie->isDirty('title')) {
                $movie->slug = Str::slug($movie->title);
            }
        });
    }

    /**
     * Convierte una duración en formato texto (1h 55m) a minutos
     *
     * @param string $durationText
     * @return int
     */
    public static function convertDurationToMinutes($durationText)
    {
        $minutes = 0;
        
        // Buscar horas
        if (preg_match('/(\d+)h/', $durationText, $matches)) {
            $minutes += (int)$matches[1] * 60;
        }
        
        // Buscar minutos
        if (preg_match('/(\d+)m/', $durationText, $matches)) {
            $minutes += (int)$matches[1];
        }
        
        return $minutes;
    }
    
    /**
     * Parsea una fecha en formato texto (21 mayo 2025) a objeto Carbon
     *
     * @param string $dateText
     * @return \Carbon\Carbon
     */
    public static function parseReleaseDate($dateText)
    {
        // Convertir nombres de meses en español a inglés para Carbon
        $monthTranslations = [
            'enero' => 'January',
            'febrero' => 'February',
            'marzo' => 'March',
            'abril' => 'April',
            'mayo' => 'May',
            'junio' => 'June',
            'julio' => 'July',
            'agosto' => 'August',
            'septiembre' => 'September',
            'octubre' => 'October',
            'noviembre' => 'November',
            'diciembre' => 'December'
        ];
        
        foreach ($monthTranslations as $es => $en) {
            $dateText = str_ireplace($es, $en, $dateText);
        }
        
        return Carbon::parse($dateText);
    }

    /**
     * Importar datos de scraping al modelo
     *
     * @param array $scrapedData
     * @return Movie
     */
    public static function fromScrapedData(array $scrapedData)
    {
        $movie = new self();
        $movie->title = $scrapedData['titulo'] ?? null;
        $movie->synopsis = $scrapedData['sinopsis'] ?? null;
        
        // Convertir duración de texto a minutos
        if (!empty($scrapedData['duracion'])) {
            $movie->duration = self::convertDurationToMinutes($scrapedData['duracion']);
        }
        
        $movie->rating = $scrapedData['clasificacion'] ?? null;
        
        // Convertir fecha de texto a objeto date
        if (!empty($scrapedData['fecha_estreno'])) {
            $movie->release_date = self::parseReleaseDate($scrapedData['fecha_estreno']);
        }
        
        // Usar photo_url para almacenar la ruta local de la imagen
        $movie->photo_url = $scrapedData['poster_local'] ?? $scrapedData['imagen_local'] ?? null;
        
        return $movie;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Obtener los géneros asociados con esta película.
     */
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    /**
     * Obtener los actores asociados con esta película.
     */
    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'movie_actor');
    }

    /**
     * Obtener las funciones programadas para esta película.
     */
    public function functions()
    {
        return $this->hasMany(Functions::class);
    }
    
    /**
     * Sincronizar géneros desde un array de nombres
     * 
     * @param array $genreNames
     * @return void
     */
    public function syncGenresByName(array $genreNames)
    {
        $genreIds = [];
        
        foreach ($genreNames as $name) {
            $genre = Genre::firstOrCreate(['name' => $name]);
            $genreIds[] = $genre->id;
        }
        
        $this->genres()->sync($genreIds);
    }
    
    /**
     * Sincronizar actores desde un array de nombres
     * 
     * @param array $actorNames
     * @return void
     */
    public function syncActorsByName(array $actorNames)
    {
        $actorIds = [];
        
        foreach ($actorNames as $name) {
            $actor = Actor::firstOrCreate(['name' => $name]);
            $actorIds[] = $actor->id;
        }
        
        $this->actors()->sync($actorIds);
    }

    /**
     * Obtener la URL completa de la imagen
     *
     * @return string
     */
    public function getPhotoUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // Si la URL ya es completa (comienza con http:// o https://)
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        // Si comienza con una barra, quitar la barra inicial
        $path = ltrim($value, '/');
        
        // Generar URL completa para la API de imágenes
        return url("/api/v1/images/{$path}");
    }
}
