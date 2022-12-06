<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models as Database;

class MeaningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $meanings = [
        	['1','責任','trách','責任を果たす',''],
            ['2','与える','Cho','優先権を与える','奪う'],
            ['3','非常','Khẩn cấp; cấp bách; bức thiết','','通常'],
            ['4','全数','Toàn bộ số; mọi thứ','',''],
            ['5','会う','Gặp','逢う','別れる'],
            ['6','青','Màu xanh da trời; màu xanh nước biển','','']
        ];

        foreach ($meanings as $meaning) {
            Database\Meaning::create([
                'id' => $meaning[0],
                'word' => $meaning[1],
                'meaning' => $meaning[2],
                'dongnghia' => $meaning[3],
                'trainghia' => $meaning[4]
            ]);
        }
    }
}
