<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MobilFactory extends Factory
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
            'kapasitas_penumpang' => $this->faker->numberBetween(2, 8),
            'tipe'=> $this->faker->text(10),
            'stok'=> $this->faker->numberBetween(1, 10)
        ];
    }
}
