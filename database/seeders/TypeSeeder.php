<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toInsert = [
            [
                'name' => 'Sea View',
            ],
            [
                'name' => 'Beach Front',
            ],
            [
                'name' => 'Mountain View',
            ],
        ];
        foreach ($toInsert as $item) {
            DB::table('types')->insert($item);
        }
    }
}
