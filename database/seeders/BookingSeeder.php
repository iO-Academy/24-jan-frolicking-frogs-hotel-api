<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toInsert = [
            [
                'customer' => 'Jason James',
                'guests' => '2',
                'start' => '24/06/24',
                'end' => '24/06/30',
                'created_at' => '24/04/01',
            ],
            [
                'customer' => 'Sally Simpson',
                'guests' => '1',
                'start' => '24/06/13',
                'end' => '24/06/20',
                'created_at' => '24/05/01',
            ],
            [
                'customer' => 'Lucy Lamppost',
                'guests' => '0',
                'start' => '24/06/14',
                'end' => '24/06/18',
                'created_at' => '25/04/10',
            ],
            [
                'customer' => 'Jared Jurassic',
                'guests' => '3',
                'start' => '24/07/19',
                'end' => '24/07/25',
                'created_at' => '24/03/30',
            ],
            [
                'customer' => 'Millie May',
                'guests' => '4',
                'start' => '24/04/10',
                'end' => '24/04/13',
                'created_at' => '24/03/30',
            ],
        ];

        foreach ($toInsert as $item) {
            DB::table('bookings')->insert($item);
        }
    }
}
