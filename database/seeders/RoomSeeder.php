<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toInsert = [
            [
                'name' => 'Penthouse',
                'rate' => '550',
                'image' => 'https://image-tc.galaxy.tf/wijpeg-32vjxet37ewob7omgewyr7ytb/dachstein-suite-asublick-1920x1080_square.jpg?rotate=0&crop=529%2C0%2C1080%2C1080&width=400',
                'min_capacity' => '1',
                'max_capacity' => '6',
                'description' => 'Two king beds and two queen beds, One full size sofabed; one rollaway and one crib on request',
                'type_id' => '3'
            ],
            [
                'name' => 'Honeymoon',
                'rate' => '250',
                'image' => 'https://res.cloudinary.com/enchanting/q_70,f_auto,w_400,h_400,c_fill,g_face/hj-web/2020/10/120949-Ocean-View-Chalet-001.jpg',
                'min_capacity' => '1',
                'max_capacity' => '2',
                'description' => 'King bed with customizable mattress, One rollaway, one crib (on request) and one sofabed (included)',
                'type_id' => '2'
            ],
            [
                'name' => 'Deluxe',
                'rate' => '400',
                'image' => 'https://www.malmaison.com/media/gngjp2qe/club-sea-view.jpg?width=400&height=400&formate=webp&rnd=133505805092130000',
                'min_capacity' => '1',
                'max_capacity' => '3',
                'description' => 'High-speed internet access, Sleeps 3, 28 sq m, 1 King Bed',
                'type_id' => '1'
            ],
            [
                'name' => 'Double',
                'rate' => '200',
                'image' => 'https://www.bestloved.com/wp-content/uploads/2020/04/Kempinski-Hotel-Bahia1-400x400.jpg',
                'min_capacity' => '1',
                'max_capacity' => '2',
                'description' => 'Free WiFi, Sleeps 2, 10 sq m, 1 Double Bed',
                'type_id' => '2'
            ]
        ];
    }
}
