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
                'name' => 'Customer Order',
                'slug' => 'customer-order',
                'type' => 'debit', 
            ],
            [
                'id' => 2,
                'name' => 'Modal Awal',
                'slug' => 'modal-awal',
                'type' => 'debit', 
            ],
            [
                'id' => 3,
                'name' => 'Biaya Operasional',
                'slug' => 'biaya-operasional',
                'type' => 'credit', 
            ],
            [
                'id' => 4,
                'name' => 'Gaji Karyawan',
                'slug' => 'gaji-karyawan',
                'type' => 'credit',
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
