<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schedule::create([
            'name' => 'Schedule 1',
            'description' => 'description',
            'price' => 1599,
        ]);

        Schedule::create([
            'name' => 'Schedule 2',
            'description' => 'description',
            'price' => 1300,
        ]);

        Schedule::create([
            'name' => 'Schedule 3',
            'description' => 'description',
            'price' => 1499,
        ]);

        Schedule::create([
            'name' => 'Schedule 4',
            'description' => 'description',
            'price' => 1999,
        ]);

        Schedule::create([
            'name' => 'Schedule 5',
            'description' => 'description',
            'price' => 1399,
        ]);
    }
}
