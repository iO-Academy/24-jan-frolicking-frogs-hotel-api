<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toInsert = [
            [
                'room_id' => '1',
                'booking_id' => '5',
            ],
            [
                'room_id' => '3',
                'booking_id' => '4',
            ],
            [
                'room_id' => '2',
                'booking_id' => '2',
            ],
            [
                'room_id' => '4',
                'booking_id' => '1',
            ],
            [
                'room_id' => '4',
                'booking_id' => '3',
            ],
            [
                'room_id' => '4',
                'booking_id' => '5',
            ],
        ];

        foreach ($toInsert as $item) {
            DB::table('booking_room')->insert($item);
        }
    }
}
