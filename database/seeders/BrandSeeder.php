<?php

namespace Database\Seeders;

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

        $filePath = 'C:\Users\Cechriza\Documents\Inventario de Activos.xlsx';
        // storage_path('app/assets.xlsx');

        // Convertir el Excel a un Array
        $rows = Excel::toArray(new AssetsImport(), $filePath);

        $cechLimaBrands = $this->getBrands($rows[0], 43); //* Brands
        $cechProvBrands = $this->getBrands($rows[1], 39); //* Brands
        $ydiezaBrands = $this->getBrands($rows[2], 9); //* Brands

        $extraBrands = [
            ['brand' => 'Logitech']
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
            if (!in_array($brand['brand'], array_column($unique, 'brand'), true)) {
                $unique[] = $brand;
            }
        }
        return array_map(fn($brand) => ['name' => $brand['brand']], $unique);
    }

    private function capitalizeBrand($brand, array $exceptions = ['HP', 'IBM', 'ASUS'])
    {

        if (in_array(strtoupper($brand), $exceptions)) {
            return strtoupper($brand);
        }
        return ucwords(strtolower($brand));

    }

}
