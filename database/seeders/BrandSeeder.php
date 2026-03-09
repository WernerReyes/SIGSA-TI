<?php

namespace Database\Seeders;

use App\Enums\Asset\AssetTypeEnum;
use App\Imports\AssetsImport;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
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

         $rootPath = config('app.root_local_path');

        $filePath = $rootPath . '\Documents\Inventario de Activos.xlsx';
        // storage_path('app/assets.xlsx');

        // Convertir el Excel a un Array
        $rows = Excel::toArray(new AssetsImport(), $filePath);

        $cechLimaBrands = $this->getBrands($rows[0], 43); //* Brands
        $cechProvBrands = $this->getBrands($rows[1], 39); //* Brands
        $ydiezaBrands = $this->getBrands($rows[2], 9); //* Brands

        $extraBrands = [
            ['name' => 'Logitech', 'type_id' =>AssetTypeEnum::KEYBOARD, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Genius', 'type_id' =>AssetTypeEnum::KEYBOARD, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dell', 'type_id' =>AssetTypeEnum::LAPTOP_CHARGER, 'created_at' => now(), 'updated_at' => now()],
        ];

        Brand::insert(
            array_merge(
                $this->getUniqueBrands(
                    array_merge($cechLimaBrands, $cechProvBrands, $ydiezaBrands)
                ),
                $extraBrands
            )
        );

        Schema::enableForeignKeyConstraints();

    }

    private function getBrands($rows, $length)
    {

        //* 'cargador,mouse' -> ['cargador', 'mouse']
        $data = function ($row) {
            return [
                'name' => $row[2] ? trim($row[2]) : null, // Asegurarse de que el nombre no sea nulo y eliminar espacios
                'brand' => $this->capitalizeBrand($row[3] ? trim($row[3]) : null),
                'type' => $row[2] ? trim((string) $row[2]) : null,
            ];
        };


        return
            array_filter(
                array_map(fn($row) => $data($row), array_slice($rows, 1, $length)),
                fn($asset) => !is_null($asset['name']) && $asset['name'] !== 'TABLET TECLADO'
            ); // Filtrar solo los activos con nombre no nulo


        // return $this->getUniqueBrands($array);
    }

    private function getUniqueBrands($brands)
    {
        $unique = [];
        foreach ($brands as $brand) {
            $typeId = $this->resolveTypeId($brand['type'] ?? null);
            if (!$typeId || empty($brand['brand'])) {
                continue;
            }

            $key = mb_strtolower(trim((string) $brand['brand'])) . '|' . $typeId;
            $unique[$key] = [
                'name' => trim((string) $brand['brand']),
                'type_id' => $typeId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return array_values($unique);
    }

    private function capitalizeBrand($brand, array $exceptions = ['HP', 'IBM', 'ASUS'])
    {

        if (in_array(strtoupper($brand), $exceptions)) {
            return strtoupper($brand);
        }
        return ucwords(strtolower($brand));

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
