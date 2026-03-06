<?php

namespace Database\Seeders;

use App\Enums\Asset\AssetTypeEnum;
use App\Imports\AssetsImport;
use App\Models\AssetModel;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
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

        $filePath = 'C:\Users\Cechriza\Documents\Inventario de Activos.xlsx';
        // storage_path('app/assets.xlsx');

        // Convertir el Excel a un Array
        $rows = Excel::toArray(new AssetsImport(), $filePath);





        $cechLimaModels = $this->getModels($rows[0], 43);
        $cechProvModels = $this->getModels($rows[1], 39);
        $ydiezaModels = $this->getModels($rows[2], 9);

        $extraModels = [
            [
                'name' => 'MK235',
                'brand_id' => Brand::where('name', 'Logitech')->where('type_id', AssetTypeEnum::KEYBOARD)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
                // 'brand' => 'Logitech',
                // 'type' => 'TECLADO',
            ],
            [
                'name' => 'SlimStar Q200',
                'brand_id' => Brand::where('name', 'Genius')->where('type_id', AssetTypeEnum::KEYBOARD)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
                
            ], 
            [
                'name' => 'ERGO KB-700',
                'brand_id' => Brand::where('name', 'Genius')->where('type_id', AssetTypeEnum::KEYBOARD)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        $models = $this->getUniqueModels(array_merge($cechLimaModels, $cechProvModels, $ydiezaModels));

        if (!empty($models)) {
            AssetModel::insert([...$models, ...$extraModels]);
        }

        Schema::enableForeignKeyConstraints();


    }

    private function getModels($rows, $length)
    {
        $data = function ($row) {
            return [
                'type' => $row[2] ? trim((string) $row[2]) : null,
                'brand' => $row[3] ? trim((string) $row[3]) : null,
                'model' => $row[4] ? trim((string) $row[4]) : null,
            ];
        };

        return array_filter(
            array_map(fn($row) => $data($row), array_slice($rows, 1, $length)),
            fn($asset) => !is_null($asset['model'])
        );
    }

    private function getUniqueModels($models)
    {
        $unique = [];
        foreach ($models as $model) {
            $typeId = $this->resolveTypeId($model['type'] ?? null);
            $brandId = $this->resolveBrandId($model['brand'] ?? null, $typeId);

            if (!$brandId || empty($model['model'])) {
                continue;
            }

            $key = mb_strtolower(trim((string) $model['model'])) . '|' . $brandId;
            $unique[$key] = [
                'name' => trim((string) $model['model']),
                'brand_id' => $brandId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return array_values($unique);
    }

    private function resolveBrandId(?string $brandName, ?int $typeId): ?int
    {
        if (!$brandName || !$typeId) {
            return null;
        }

        return Brand::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($brandName))])
            ->where('type_id', $typeId)
            ->value('id');
    }

    private function resolveTypeId(?string $typeName): ?int
    {
        if (!$typeName) {
            return null;
        }

        return match (mb_strtoupper(trim($typeName))) {
            'LAPTOP', 'LATOP' => AssetTypeEnum::LAPTOP,
            'PC' => AssetTypeEnum::PC,
            'MINI PC' => AssetTypeEnum::MINI_PC,
            'CELULAR' => AssetTypeEnum::CELL_PHONE,
            'MONITOR' => AssetTypeEnum::MONITOR,
            'TABLET', 'TABLET TECLADO' => AssetTypeEnum::TABLET,
            'MOUSE' => AssetTypeEnum::MOUSE,
            'TECLADO' => AssetTypeEnum::KEYBOARD,
            'AUDIFONOS', 'AUDIFONOS USB', 'AURICULARES' => AssetTypeEnum::HEADPHONES,
            'PATCHCORD', 'PATCHCOARD' => AssetTypeEnum::PATCH_CABLE,
            'CARGADOR LAPTOP', 'CARGADOR' => AssetTypeEnum::LAPTOP_CHARGER,
            'CARGADOR CELULAR' => AssetTypeEnum::CELL_PHONE_CHARGER,
            default => null,
        };
    }

}
