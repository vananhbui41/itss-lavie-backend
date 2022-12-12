<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeaningTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $examples = [
        	[1,1],
            [2,2],
            [3,3],
            [3,4],
            [2,5],
            [4,6],
            [4,7],
            [5,8],
            [6,9],
            [7,1],
            [8,3],
            [9,2],
            [10,1]
        ];

        foreach ($examples as $example) {
            DB::table('meaning_tag')->insert([
                'meaning_id' => $example[0],
                'tag_id' => $example[1]
            ]);
        }
    }
}
