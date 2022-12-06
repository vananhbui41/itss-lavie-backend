<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models as Database;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $examples = [
        	['1','1','image','This is a example'],
            ['2','5','image','This is a example'],
            ['3','4','image','This is a example'],
            ['4','2','image','This is a example'],
            ['5','3','image','This is a example'],
            ['6','3','image','This is a example'],
            ['7','1','image','This is a example'],
        ];

        foreach ($examples as $example) {
            Database\Example::create([
                'id' => $example[0],
                'meaning_id' => $example[1],
                'image' => $example[2],
                'meaning' => $example[3]
            ]);
        }
    }
}
