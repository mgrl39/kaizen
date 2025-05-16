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
        $cities = ['Madrid', 'Barcelona', 'Valencia'];
        $cinemaNames = ['Cinesa', 'Yelmo Cines'];

        return [
            'name' => $this->faker->randomElement($cinemaNames) .
                ' ' . $this->faker->word(),
            'location' => $this->faker->streetAddress() .
                ', ' . $this->faker->randomElement($cities),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}
