<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discount;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Discount::create([
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'max_discount_amount' => 500,
            'user_left' => 10,
            'valid_until' => '2024-10-20',
        ]);

        Discount::create([
            'discount_type' => 'fixed',
            'discount_value' => 100,
            'max_discount_amount' => 100,
            'user_left' => 10,
            'valid_until' => '2024-10-20',
        ]);
    }
}
