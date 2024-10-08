<?php

namespace Database\Seeders;

use App\Models\AppointmentType;
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
        $this->call([
            UserSeeder::class,
            SettingSeeder::class,
            ParkSeeder::class,
            ParkPriceSeeder::class,
            ParkSaleSeeder::class
        ]);
    }
}
