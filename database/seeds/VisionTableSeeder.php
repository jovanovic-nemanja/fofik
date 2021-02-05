<?php

use Illuminate\Database\Seeder;

class VisionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('ff_vision_history')->delete();
        \DB::table('ff_vision_history')->insert(array(
            array(
                'id' => 1,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'amazon',
                'created_on' => '2021-02-05'
            ),
            array(
                'id' => 2,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-06 '
            ),
            array(
                'id' => 3,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-06'
            ),
            array(
                'id' => 4,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-06'
            ),
            array(
                'id' => 5,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'amazon',
                'created_on' => '2021-02-07'
            ),
            array(
                'id' => 6,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-07'
            ),
            array(
                'id' => 7,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-08'
            ),
            array(
                'id' => 8,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-08'
            ),
            array(
                'id' => 9,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'amazon',
                'created_on' => '2021-02-08'
            ),
            array(
                'id' => 10,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'amazon',
                'created_on' => '2021-02-08'
            ),
            array(
                'id' => 11,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-08'
            ),
            array(
                'id' => 12,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-09'
            ),
            array(
                'id' => 13,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'amazon',
                'created_on' => '2021-02-09'
            ),
            array(
                'id' => 14,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-09'
            ),
            array(
                'id' => 15,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-10'
            ),
            array(
                'id' => 24,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-09'
            ),
            array(
                'id' => 25,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'amazon',
                'created_on' => '2021-02-09'
            ),
            array(
                'id' => 26,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-09'
            ),
            array(
                'id' => 27,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-10'
            ),
            array(
                'id' => 16,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-03-09'
            ),
            array(
                'id' => 17,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'amazon',
                'created_on' => '2021-03-09'
            ),
            array(
                'id' => 18,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-03-09'
            ),
            array(
                'id' => 19,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-03-10'
            ),
            array(
                'id' => 20,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-03-09'
            ),
            array(
                'id' => 21,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'amazon',
                'created_on' => '2021-03-09'
            ),
            array(
                'id' => 22,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-03-09'
            ),
            array(
                'id' => 23,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-03-10'
            ),
            array(
                'id' => 36,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-02-10'
            ),
            array(
                'id' => 28,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-04-10'
            ),
            array(
                'id' => 29,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-05-10'
            ),
            array(
                'id' => 30,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-06-10'
            ),
            array(
                'id' => 31,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-07-10'
            ),
            array(
                'id' => 32,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-08-10'
            ),
            array(
                'id' => 33,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-09-10'
            ),
            array(
                'id' => 34,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-10-10'
            ),
            array(
                'id' => 35,
                'vision_target' => 'Tom Hanks',
                'vision_service' => 'google',
                'created_on' => '2021-11-10'
            ),
        ));
    }
}
