<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CoachSchedule;


class CoachScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        CoachSchedule::truncate();
        $csvData = fopen(base_path('database/csv/dataset.csv'), 'r');
        $transRow = true;
        while (($data = fgetcsv($csvData, 555, ',')) !== false) {
            if (!$transRow) {
                CoachSchedule::create([
                    'name' => $data['0'],
                    'timezone' => $data['1'],
                    'day_of_week' => $data['2'],
                    'available_at' => $data['3'],
                    'available_until' => $data['4']
                ]);
            }
            $transRow = false;
        }
        fclose($csvData);
    }
}
