<?php

namespace Database\Seeders;

use App\Enums\Asset\AssetStatus;
use App\Enums\AssetHistory\AssetHistoryAction;
use App\Models\AssetHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Asset;

use Carbon\Carbon;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AssetHistory::truncate();
        Asset::truncate();

        $base = [
            'phone' => null,
            'imei' => null,
            'invoice_path' => null,
            'processor' => null,
            'ram' => null,
            'storage' => null,
        ];

        Asset::insert([
            array_merge($base, [
                'name' => 'Laptop Dell Latitude 5420',
                'color' => 'Negro',
                'status' => AssetStatus::AVAILABLE->value,
                'brand' => 'Dell',
                'model' => 'Latitude 5420',
                'serial_number' => 'DL5420-001',
                'processor' => 'Intel i5 1145G7',
                'ram' => '16GB',
                'storage' => '512GB SSD',
                'purchase_date' => now()->subYears(2),
                'warranty_expiration' => now()->addYear(),
                'is_new' => false,
                'type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]),

            array_merge($base, [
                'name' => 'iPhone 13',
                'color' => 'Azul',
                'status' => AssetStatus::AVAILABLE->value,
                'brand' => 'Apple',
                'model' => 'iPhone 13',
                'serial_number' => 'IP13-005',
                'ram' => '4GB',
                'storage' => '128GB',
                'phone' => '987654321',
                'imei' => '356789012345678',
                'purchase_date' => now()->subYear(),
                'warranty_expiration' => now()->addMonths(6),
                'is_new' => false,
                'type_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]),

            // GENERADOS
            ...collect(range(1, 2000))->map(function ($i) use ($base) {
                return array_merge($base, [
                    'name' => ['Cargador', 'Mouse', 'Teclado', 'Monitor'][rand(0, 3)] . " " . rand(100, 999),
                    'color' => ['Negro', 'Blanco', 'Plateado'][rand(0, 2)],
                    'status' => AssetStatus::AVAILABLE->value,
                    'brand' => ['Dell', 'HP', 'Lenovo', 'Asus'][rand(0, 3)],
                    'model' => 'Model-' . rand(100, 999),
                    'serial_number' => 'GEN-' . uniqid(),
                    'processor' => 'Intel i5',
                    'ram' => rand(8, 32) . 'GB',
                    'storage' => rand(256, 1024) . 'GB SSD',
                    'purchase_date' => now()->subDays(rand(100, 1500)),
                    'warranty_expiration' => now()->addDays(rand(100, 900)),
                    'is_new' => rand(0, 1),
                    'type_id' => rand(1, 4),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })->toArray(),
        ]);

        AssetHistory::insert(
            Asset::get()->map(fn($asset) => [
                'action' => AssetHistoryAction::CREATED->value,
                'description' => 'Equipo registrado en el sistema',
                'asset_id' => $asset->id,
                'performed_by' => 1, 
                'performed_at' => Carbon::now(),
            ])->toArray()
        );

        Schema::enableForeignKeyConstraints();
    }
}
