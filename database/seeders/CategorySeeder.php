<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Office Supplies',
            'Industrial Components',
            'IT Hardware',
            'Packaging',
            'Maintenance',
            'Safety Equipment',
            'Furniture & Fixtures',
            'Electrical Supplies',
            'Laboratory Supplies',
            'Catering & Kitchen',
            'Printing & Stationery',
            'Tools & Equipment',
            'Automotive Supplies',
            'Medical Supplies',
            'Textiles & Uniforms',
            'Gardening & Grounds',
            'Security & Surveillance',
            'HVAC & Ventilation',
            'Janitorial Equipment',
            'Signage & Display',
            // Add more as needed
        ];

        foreach ($categories as $name) {
            DB::table('categories')->updateOrInsert(['name' => $name]);
        }
    }
}
