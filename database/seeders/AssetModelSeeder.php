<?php

namespace Database\Seeders;

use App\Models\AssetModel;
use Illuminate\Database\Seeder;
use Schema;

class AssetModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AssetModel::truncate();

        $data = [
            ['name' => 'MacBook Pro 16" M1 Pro'],
            ['name' => 'Dell XPS 13'],
            ['name' => 'HP Spectre x360'],
            ['name' => 'Lenovo ThinkPad X1 Carbon'],
            ['name' => 'Asus ZenBook 14'],
            ['name' => 'Acer Swift 3'],
            ['name' => 'Microsoft Surface Laptop 4'],
            ['name' => 'Samsung Galaxy Book Pro'],
            ['name' => 'Google Pixelbook Go'],
            ['name' => 'Sony VAIO SX14'],
        ];

        AssetModel::insert($data);

        Schema::enableForeignKeyConstraints();

    }
}
