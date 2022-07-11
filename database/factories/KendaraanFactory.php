<?php
namespace Database\Factories;

use App\Models\Kendaraan;
use Illuminate\Database\Eloquent\Factories\Factory;

class KendaraanFactory extends Factory
{
    protected $model = Kendaraan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tahun_keluaran' => $this->faker->numberBetween(1950, 2022),
            'warna' => $this->faker->text(),
            'harga' => $this->faker->numberBetween(10000000, 1000000000)
        ];
    }
}
