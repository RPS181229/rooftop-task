<?php

namespace Database\Seeders;

use App\Models\Timezone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timezones = [
            [
                'id' => 1,
                'name' => 'America/North_Dakota/New_Salem'
            ],
            [
                'id' => 2,
                'name' => 'America/North_Dakota/Center'// DayLight saving is UTC-5 else UTC-6
            ],
            [
                'id' => 3,
                'name' => 'America/Yakutat' // UTC -9
            ]
        ];

        foreach($timezones as $timezone)
            Timezone::create($timezone);
    }
}
