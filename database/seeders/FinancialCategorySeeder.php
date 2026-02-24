<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Modal Awal',
                'slug' => 'modal-awal',
                'type' => 'credit', 
            ],
            [
                'id' => 2,
                'name' => 'Biaya Operasional',
                'slug' => 'biaya-operasional',
                'type' => 'debit', 
            ],
            [
                'id' => 3,
                'name' => 'Gaji Karyawan',
                'slug' => 'gaji-karyawan',
                'type' => 'debit',
            ],

        ];

        foreach ($categories as $category) {
            DB::table('financial_categories')->updateOrInsert(
                ['id' => $category['id']],
                [
                    'name'       => $category['name'],
                    'slug'       => $category['slug'],
                    'type'       => $category['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
