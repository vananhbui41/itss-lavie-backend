<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models as Database;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
        	['1','1','tag 1'],
            ['2','1','tag 2'],
            ['3','2','tag 3'],
            ['4','2', 'tag 4']
        ];

        foreach ($tags as $tag) {
            Database\Tag::create([
                'id' => $tag[0],
                'category_id' => $tag[1],
                'name' => $tag[2]
            ]);
        }
    }
}
