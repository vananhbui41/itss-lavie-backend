<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models as Database;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
        	['1','context'],
            ['2','type'],
            ['3','topic']
        ];

        foreach ($categories as $category) {
            Database\Category::create([
                'id' => $category[0],
                'name' => $category[1]
            ]);
        }
    }
}
