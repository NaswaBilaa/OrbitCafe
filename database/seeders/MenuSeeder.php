<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            ['name' => 'Hazelnut Latte', 'description' => 'Espresso dengan sirup hazelnut dan susu.', 'price' => 25000, 'category_id' => '2', 'image' => 'hazelnut_latte.jpg'],
            ['name' => 'Classic Frappe', 'description' => 'Minuman frappe klasik yang menyegarkan.', 'price' => 27000, 'category_id' => '4', 'image' => 'classic_frappe.jpg'],
            ['name' => 'Brown Sugar Milk', 'description' => 'Susu dengan brown sugar dan es.', 'price' => 23000, 'category_id' => '3', 'image' => 'brown_sugar_milk.jpg'],
            ['name' => 'Matcha Tea', 'description' => 'Minuman teh matcha Jepang.', 'price' => 24000, 'category_id' => '6', 'image' => 'matcha_tea.jpg'],
            ['name' => 'Tiramisu Cup', 'description' => 'Dessert Tiramisu khas Italia.', 'price' => 28000, 'category_id' => '5', 'image' => 'tiramisu_cup.jpg'],
            ['name' => 'Signature Blend', 'description' => 'Minuman kopi khas Orbit Cafe.', 'price' => 26000, 'category_id' => '1', 'image' => 'signature_blend.jpg'],
        ];

        foreach ($menus as $menuData) {
            $sourcePath = public_path('img/' . $menuData['image']);
            $targetPath = 'menus/' . $menuData['image'];

            // Copy gambar ke storage/public/menus hanya jika belum ada
            if (!Storage::disk('public')->exists($targetPath)) {
                Storage::disk('public')->put($targetPath, File::get($sourcePath));
            }

            // Simpan path relatif ke storage
            $menuData['image'] = $targetPath;

            Menu::create($menuData);
        }
    }
}
