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
        $csvData = fopen(base_path('database/csv/coach_schedules.csv'), 'r');
        $transRow = true;
        while (($data = fgetcsv($csvData, 555, ',')) !== false) {
            if (!$transRow) {
                CoachSchedule::create([
                    'coach_id' => $data['0'],
                    // 'timezone' => substr($data['1'], 12),
                    'day_of_week' => $data['1'],
                    'available_at' => $data['2'],
                    'available_until' => $data['3']
                ]);
            }
            $transRow = false;
        }
        fclose($csvData);
    }
}
