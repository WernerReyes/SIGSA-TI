<?php

namespace Database\Seeders;

use App\Models\AssetType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AssetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AssetType::truncate();

        AssetType::insert([
            ['name' => 'Laptop', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Celular', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accesorio', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mini PC', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
