<?php

namespace Database\Factories;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cinema>
 */
class CinemaFactory extends Factory
{
    /**
     * El nombre del modelo asociado con este factory.
     *
     * @var string
     */
    protected $model = Cinema::class;

    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $cities = ['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao', 'Zaragoza', 'Málaga', 'Murcia'];
        $cinemaNames = ['Cinesa', 'Yelmo Cines', 'Kinépolis', 'Cine Capitol', 'MK2', 'Artistic Metropol', 'Cinestar', 'Ocine'];
        
        return [
            'name' => $this->faker->randomElement($cinemaNames) . ' ' . $this->faker->word(),
            'location' => $this->faker->streetAddress() . ', ' . $this->faker->randomElement($cities),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
    
    /**
     * Indica que el cine es de una cadena específica (por ejemplo, Cinesa).
     *
     * @return static
     */
    public function cinesa()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Cinesa ' . $this->faker->city(),
            ];
        });
    }
    
    /**
     * Indica que el cine está en Madrid.
     *
     * @return static
     */
    public function inMadrid()
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => $this->faker->streetAddress() . ', Madrid',
            ];
        });
    }
} 