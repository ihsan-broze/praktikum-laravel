<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opsi 1: Hapus semua data lama dulu (hati-hati jika sudah ada course terkait)
        // DB::table('categories')->truncate();
        
        // Opsi 2: Menggunakan Model dengan firstOrCreate (RECOMMENDED)
        $categories = [
            'Programming',
            'Desain', 
            'Business',
            'Marketing',
            'Data Science',
            'Other'
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['title' => $category]);
        }
        
        // Atau jika ingin tetap pakai DB::table, gunakan updateOrInsert
        /*
        $categoriesData = [
            ['title' => 'Programming'],
            ['title' => 'Desain'],
            ['title' => 'Business'],
            ['title' => 'Marketing'],
            ['title' => 'Data Science'],
            ['title' => 'Other'],
        ];

        foreach ($categoriesData as $cat) {
            DB::table('categories')->updateOrInsert(
                ['title' => $cat['title']], // Kondisi pencarian
                array_merge($cat, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
        */
    }
}