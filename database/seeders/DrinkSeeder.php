<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Drink;

class DrinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $drinks = [
            ['name' => 'Hazelnut Latte', 'description' => 'Espresso dengan sirup hazelnut dan susu.', 'price' => 25000, 'kategori_id' => '2', 'image' => 'hazelnut_latte.jpg'],
            ['name' => 'Classic Frappe', 'description' => 'Minuman frappe klasik yang menyegarkan.', 'price' => 27000, 'kategori_id' => '4', 'image' => 'classic_frappe.jpg'],
            ['name' => 'Brown Sugar Milk', 'description' => 'Susu dengan brown sugar dan es.', 'price' => 23000, 'kategori_id' => '3', 'image' => 'brown_sugar_milk.jpg'],
            ['name' => 'Matcha Tea', 'description' => 'Minuman teh matcha Jepang.', 'price' => 24000, 'kategori_id' => '6', 'image' => 'matcha_tea.jpg'],
            ['name' => 'Tiramisu Cup', 'description' => 'Dessert Tiramisu khas Italia.', 'price' => 28000, 'kategori_id' => '5', 'image' => 'tiramisu_cup.jpg'],
            ['name' => 'Signature Blend', 'description' => 'Minuman kopi khas Roastly.', 'price' => 26000, 'kategori_id' => '1', 'image' => 'signature_blend.jpg'],
        ];

        foreach ($drinks as $drinkData) {
            $sourcePath = public_path('img/' . $drinkData['image']);
            $targetPath = 'drinks/' . $drinkData['image'];

            // Copy gambar ke storage/public/drinks hanya jika belum ada
            if (!Storage::disk('public')->exists($targetPath)) {
                Storage::disk('public')->put($targetPath, File::get($sourcePath));
            }

            // Simpan path relatif ke storage
            $drinkData['image'] = $targetPath;

            Drink::create($drinkData);
        }
    }
}
