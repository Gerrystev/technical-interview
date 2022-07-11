<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MotorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'kendaraan'=> [
                'tahun_keluaran' => $this->faker->numberBetween(1950, 2022),
                'warna' => $this->faker->text(10),
                'harga' => $this->faker->numberBetween(10000000, 1000000000)
            ],
            'mesin'=> $this->faker->text(10),
            'tipe_suspensi' => $this->faker->text(10),
            'tipe_transmisi'=> $this->faker->text(10),
            'stok'=> $this->faker->numberBetween(1, 10)
        ];
    }
}
