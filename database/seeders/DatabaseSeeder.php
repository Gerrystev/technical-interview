<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mobil;
use App\Models\Motor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Mobil::factory(50)->create();
        Motor::factory(50)->create();
        User::factory(1)->create();
    }
}
