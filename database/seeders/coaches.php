<?php

namespace Database\Seeders;

use App\Models\Coach;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class coaches extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coaches = [
            [
                'id'=>1,
                'name'=>'John Doe',
                'timezone_id'=>1
            ],
            [
                'id'=>2,
                'name'=>'Jane Doe',
                'timezone_id'=>2
            ],
            [
                'id'=>3,
                'name'=>'Rachel Green',
                'timezone_id'=>3
            ],
            [
                'id'=>4,
                'name'=>'Margaret Houlihan',
                'timezone_id'=>2
            ],
            [
                'id'=>5,
                'name'=>'Hawkeye Pierce',
                'timezone_id'=>2
            ]
            ];

            foreach($coaches as $coach){
                Coach::create($coach);
            }
       
    }
}
