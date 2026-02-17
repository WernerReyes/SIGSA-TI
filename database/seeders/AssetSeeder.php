<?php

namespace Database\Seeders;

use App\Enums\Asset\AssetStatus;
use App\Enums\AssetHistory\AssetHistoryAction;
use App\Imports\AssetsImport;
use App\Models\AssetHistory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Asset;

use Maatwebsite\Excel\Facades\Excel;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AssetHistory::truncate();
        Asset::truncate();

        $filePath = storage_path('app/assets.xlsx');

        // Convertir el Excel a un Array
        $rows = Excel::toArray(new AssetsImport, $filePath);




        // Omitir la fila de encabezados
        // ds($this->getLimaCechTechnicalAssets($rows));



        // El primer índice [0] es la primera hoja del Excel
        // foreach ($rows[0] as $index => $row) {
        //     if ($index === 0) {
        //         continue; // Saltar la fila de encabezados
        //     }

        //     ds($row);
        // }

        // User::create([
        //     'name'  => $row[0],
        //     'email' => $row[1],
        //     'password' => bcrypt('password123'),
        // ]);

        ds($this->getAvailable($rows[3], 3));
       

        Asset::insert([
            // ...$cechLima,
            ...$this->getAssigned($rows[0], 42), //* Cechriza Technical Lima
            ...$this->getAssigned($rows[1], 40), //* Cechriza Technical Province
            ...$this->getAssigned($rows[2], 13), //* Ydieza Technical
            ...$this->getAvailable($rows[3], 3), //* Decommissioned Assets
            // ...$this->getAvailable($rows[4], 3), //* In repair Assets
            ...$this->getAvailable($rows[5], 3), //* Available Assets

        ]);



        AssetHistory::insert(
            Asset::get()->map(fn($asset) => [
                'action' => AssetHistoryAction::CREATED->value,
                'description' => 'Equipo registrado en el sistema',
                'asset_id' => $asset->id,
                'performed_by' => 137,
                'performed_at' => Carbon::now(),
            ])->toArray()
        );

        Schema::enableForeignKeyConstraints();
    }

    private function getAssigned($rows, $length)
    {
        return
            array_filter(
                array_map(fn($row) => [
                    'name' => $row[2],
                    'status' => AssetStatus::AVAILABLE->value,
                    'brand' => $row[3],
                    'model' => $row[4],
                    'color' => null,
                    'serial_number' => $row[5],
                    'processor' => $row[6],
                    'ram' => $row[7],
                    'storage' => $row[8],
                    'purchase_date' => null,
                    'warranty_expiration' => null,
                    'type_id' => ($row[2] === 'TABLET TECLADO' || $row[2] === 'CELULAR') ? 3 : 1,
                    'is_new' => false,
                    'invoice_path' => null,
                    'phone' => null,
                    'imei' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], array_slice($rows, 1, $length)),
                fn($asset) => !is_null($asset['name'])
            ); // Filtrar solo los activos con nombre no nulo
    }

    private function getAvailable($rows, $length)
    {
        return
            array_filter(
                array_map(fn($row) => [
                    'name' => $row[0],
                    'status' => AssetStatus::AVAILABLE->value,
                    'brand' => $row[1],
                    'model' => $row[2],
                    'color' => null,
                    'serial_number' => $row[3],
                    'processor' => $row[4],
                    'ram' => $row[5],
                    'storage' => $row[6],
                    'purchase_date' => null,
                    'warranty_expiration' => null,
                    'type_id' => ($row[0] === 'TABLET TECLADO' || $row[0] === 'CELULAR') ? 3 : ($row[0] === "LAPTOP" ? 1 : 4),
                    'is_new' => false,
                    'invoice_path' => null,
                    'phone' => null,
                    'imei' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], array_slice($rows, 1, $length)),
                fn($asset) => !is_null($asset['name'])
            ); // Filtrar solo los activos con nombre no nulo
    }

    // private function 

    
}
