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
        	['1','1','casual'],
            ['2','1','formal'],
            ['3','1','slang'],
            ['4','1', '用語'],
            ['5','2','Danh Từ'],
            ['6','2','Động Từ'],
            ['7','2','Tính Từ'],
            ['8','3','IT'],
            ['9','3','business']
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
