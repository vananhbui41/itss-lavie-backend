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
        	['1','trách nhiệm','trách nhiệm','〜に関する政府の責任','Trách nhiệm của chính phủ về'],
            ['2','Cho','','',''],
            ['2','Đưa ra; gây ra; đem đến','Đưa ra; gây ra; đem đến','楽しみを与る','Đem đến niềm vui'],
            ['3','Khẩn cấp; cấp bách; bức thiết','','非常
            の場合を除いて、非常口を使わないで下さい。','Trừ trường hợp khẩn cấp ra thì đừng dùng cửa thoát hiểm.'],
            ['3','Phi thường; cực kỳ; đặc biệt','','非常な愛妻家','Người chồng yêu vợ say đắm .'],
            ['4','Toàn bộ số; mọi thứ','100%','',''],
            ['5','Gặp','','会うたびに砂糖さんはしゃべりまくるんだ','Mỗi lần gặp tôi, cô Satou đều nói liên tục.'],
            ['6','Màu xanh da trời; màu xanh nước biển','','青信号で道路を渡りましょう。','Chỉ băng qua đường khi đèn xanh.'],
            ['6','Trẻ','','青二才の時代','Thời non trẻ .'],
        ];

        foreach ($meanings as $meaning) {
            Database\Meaning::create([
                'word_id' => $meaning[0],
                'meaning' => $meaning[1],
                'explanation_of_meaning' => $meaning[2],
                'example' => $meaning[3],
                'example_meaning' => $meaning[4]
            ]);
        }
    }
}
