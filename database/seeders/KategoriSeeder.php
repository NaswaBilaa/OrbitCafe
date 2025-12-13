<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            'Signature',
            'Coffee',
            'Milk Based',
            'Frappe',
            'Dessert',
            'Tea',
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create([
                'name' => $kategori,
            ]);
        }
    }
}
