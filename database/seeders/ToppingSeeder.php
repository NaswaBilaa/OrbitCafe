<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topping;

class ToppingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toppings = [
            ['name' => 'Less', 'price' => 0, 'type' => 'sugar'],
            ['name' => 'Normal', 'price' => 0, 'type' => 'sugar'],
            ['name' => 'Extra', 'price' => 0, 'type' => 'sugar'],

            ['name' => 'Small', 'price' => 0, 'type' => 'size'],      
            ['name' => 'Medium', 'price' => 3000, 'type' => 'size'],  
            ['name' => 'Large', 'price' => 5000, 'type' => 'size'],   
            
            ['name' => 'Less', 'price' => 0, 'type' => 'ice'],
            ['name' => 'Normal', 'price' => 0, 'type' => 'ice'],
            ['name' => 'Extra', 'price' => 0, 'type' => 'ice'],
        ];

        foreach ($toppings as $topping) {
            Topping::create($topping);
        }
    }
}
