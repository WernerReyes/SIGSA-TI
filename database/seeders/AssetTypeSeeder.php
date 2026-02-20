<?php

namespace Database\Seeders;

use App\Enums\Asset\AssetTypeCategory;
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
            [
                'id' => 1,
                'name' => 'Laptop',
                'created_at' => now(),
                'updated_at' => now(),
                
                'doc_category' => AssetTypeCategory::LAPTOP->value,
                'is_deletable' => false
            ],
            [
                'id' => 2,
                'name' => 'PC',
                'created_at' => now(),
                'updated_at' => now(),
                
                'doc_category' => AssetTypeCategory::LAPTOP->value,
                'is_deletable' => false
            ],
            [
                'id' => 3,
                'name' => 'Celular',
                'created_at' => now(),
                'updated_at' => now(),
                
                'doc_category' => AssetTypeCategory::CELL_PHONE->value,
                'is_deletable' => false
            ],
            [
                'id' => 4,
                'name' => 'Mini PC',
                'created_at' => now(),
                'updated_at' => now(),
                
                'doc_category' => AssetTypeCategory::LAPTOP->value,
                'is_deletable' => false
            ],
            [
                'id' => 5,
                'name' => 'Monitor',
                'created_at' => now(),
                'updated_at' => now(),
                
                'doc_category' => AssetTypeCategory::LAPTOP->value,
                'is_deletable' => false
            ],
            [
                'id' => 6,
                'name' => 'Tablet',
                'created_at' => now(),
                'updated_at' => now(),
                
                'doc_category' => AssetTypeCategory::CELL_PHONE->value,
                'is_deletable' => false
            ],

            ['id' => 7, 'name' => 'Mouse', 'created_at' => now(), 'updated_at' => now(), 'doc_category' => AssetTypeCategory::ACCESSORY->value, 'is_deletable' => false],
            ['id' => 8, 'name' => 'Teclado', 'created_at' => now(), 'updated_at' => now(), 'doc_category' => AssetTypeCategory::ACCESSORY->value, 'is_deletable' => false],
            ['id' => 9, 'name' => 'Audífonos', 'created_at' => now(), 'updated_at' => now(), 'doc_category' => AssetTypeCategory::ACCESSORY->value, 'is_deletable' => false],
            ['id' => 10, 'name' => 'Cable de Red', 'created_at' => now(), 'updated_at' => now(), 'doc_category' => AssetTypeCategory::ACCESSORY->value, 'is_deletable' => false],
            ['id' => 11, 'name' => 'Cargador de Laptop', 'created_at' => now(), 'updated_at' => now(), 'doc_category' => AssetTypeCategory::ACCESSORY->value, 'is_deletable' => false],
            ['id' => 12, 'name' => 'Cargador de Celular', 'created_at' => now(), 'updated_at' => now(), 'doc_category' => AssetTypeCategory::ACCESSORY->value, 'is_deletable' => false],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
