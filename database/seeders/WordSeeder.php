<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models as Database;

class WordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $meanings = [
        	['1','責任','せきにん'],
            ['2','与える','あたえる'],
            ['3','非常','ひじょう'],
            ['4','全数','ぜんすう'],
            ['5','会う','あう'],
            ['6','青','あお']
        ];

        foreach ($meanings as $meaning) {
            Database\Word::create([
                'id' => $meaning[0],
                'word' => $meaning[1],
                'furigana' => $meaning[2]
            ]);
        }
    }
}
