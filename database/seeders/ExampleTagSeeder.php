<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExampleTagSeeder extends Seeder
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
            [2,1],
            [3,2],
            [3,3],
            [2,4],
            [4,1],
            [4,4],
            [5,4],
            [6,3],
            [7,2]
        ];

        foreach ($examples as $example) {
            DB::table('example_tag')->insert([
                'example_id' => $example[0],
                'tag_id' => $example[1]
            ]);
        }
    }
}
