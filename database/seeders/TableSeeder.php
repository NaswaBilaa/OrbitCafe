<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Table;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            'A1',
            'A2',
            'A3',
            'A4',
            'A5',
            'A6',
        ];

        foreach ($tables as $table) {
            Table::create([
                'no_meja' => $table,
            ]);
        }
    }
}
