<?php

use Illuminate\Database\Seeder;

class PersonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('ff_persons')->delete();

        \DB::table('ff_persons')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Child1-1',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Child1-2',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Child1-3',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Child2-1',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Child2-2',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Spouse1-1',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Spouse1-2',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Spouse2-1',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Spouse2-2',
            ),
        ));
    }
}
