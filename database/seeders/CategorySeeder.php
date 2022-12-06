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
        	['1','categories 1'],
            ['2','categories 2'],
        ];

        foreach ($categories as $category) {
            Database\Category::create([
                'id' => $category[0],
                'name' => $category[1]
            ]);
        }
    }
}
