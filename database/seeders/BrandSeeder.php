<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Schema;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Brand::truncate();

        $brands = [
            ['name' => 'Apple'],
            ['name' => 'Dell'],
            ['name' => 'HP'],
            ['name' => 'Lenovo'],
            ['name' => 'Asus'],
            ['name' => 'Acer'],
            ['name' => 'Microsoft'],
            ['name' => 'Samsung'],
            ['name' => 'Google'],
            ['name' => 'Sony'],
        ];

        Brand::insert($brands);

        Schema::enableForeignKeyConstraints();

    }
}
