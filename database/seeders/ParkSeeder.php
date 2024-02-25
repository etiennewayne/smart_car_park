<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ParkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $data = [
            [
                'name' => 'PARK 1',
                'device_mac' => 'AA:BB:CC:EE:00',
                'device_ip' => '192.168.1.101',
                'is_occupied' => 0,
            ],
            [
                'name' => 'PARK 2',
                'device_mac' => 'AA:BB:CC:EE:11',
                'device_ip' => '192.168.1.102',
                'is_occupied' => 0,

            ],
            [
                'name' => 'PARK 3',
                'device_mac' => 'AA:BB:CC:EE:12',
                'device_ip' => '192.168.1.103',
                'is_occupied' => 1,

            ],
          
        ];

        \App\Models\Park::insertOrIgnore($data);

    }
}
