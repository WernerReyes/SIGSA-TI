<?php

namespace Database\Seeders;

use App\Enums\Asset\AssetStatus;
use App\Enums\AssetHistory\AssetHistoryAction;
use App\Imports\AssetsImport;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\User;
use Carbon\Carbon;
use DB;
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
        AssetAssignment::truncate();
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

        $cechLima = $this->getAssigned($rows[0], 42); //* Cechriza Technical Lima
        $cechLimaChargers = $this->generateLaptopsChargerByLaptop($cechLima);





        $cechProvince = $this->getAssigned($rows[1], 39); //* Cechriza Technical Province
        $cechProvinceChargers = $this->generateLaptopsChargerByLaptop($cechProvince);


        $ydieza = $this->getAssigned($rows[2], 13); //* Ydieza Technical
        $ydiezaChargers = $this->generateLaptopsChargerByLaptop($ydieza);

        $decommissioned = $this->getAvailable($rows[3], 3); //* Decommissioned Assets
        // $inRepair = $this->getAvailable($rows[4], 3); //* In repair Assets
        $available = $this->getAvailable($rows[5], 3); //* Available

        $user = User::select('staff_id')->where('email', 'werner.reyes@cechriza.com')->first();


        //             $array = array_merge(array_slice($rows[0], offset: 1, length: 42), array_slice($rows[1], offset: 1, length: 39), array_slice($rows[2], offset: 1, length: 9));
//             foreach ($array as $index => $row) {
//                 if ($index === 0) {
//                     continue; // Saltar la fila de encabezados
//                 }

        //                 $assignedTo = User::select('staff_id')->where('firstname', trim($row[0]))->where('lastname', trim($row[1]))->first();

        //                 if (!$assignedTo) {
//                     ds("El usuario {$row[0]} {$row[1]} no existe en la base de datos, por lo que no se incluirá en el resultado.");
//                     continue;
//                 }

        //                 // ds("El usuario {$row[0]} {$row[1]} existe en la base de datos y se incluirá en el resultado.");
//             }
// ds($user);
// ds(array_filter(array_merge($rows[0], $rows[1], $rows[2]), function ($row) {
//     if (!User::select('staff_id')->where('firstname', trim($row[0]))->where('lastname', trim($row[1]))->exists()) {
//     ds("El usuario {$row[0]} {$row[1]} no existe en la base de datos, por lo que no se incluirá en el resultado.");
//     return false;
//     }
//     return true;
// }));



        DB::transaction(function () use ($cechLima, $cechProvince, $ydieza, $user) {
            $array = array_merge($cechLima, $cechProvince, $ydieza);
            // ds($array);
            ds(array_filter($array, fn($asset) => $asset['assigned_to'] == null));
            array_map(function ($data) use ($user) {
                $accessories = $data['accessories'] ?? [];
                $assignedTo = $data['assigned_to'];

                unset($data['accessories']);
                unset($data['assigned_to']);


                //* Create Asset and Charger
                $asset = Asset::create($data);
                // $charger = Asset::create($chargerData);
                $accesoriesCreated = [];
                foreach ($accessories as $accesory) {
                    $accessoryAsset = Asset::create($accesory);

                    AssetHistory::create([
                        'action' => AssetHistoryAction::CREATED->value,
                        'description' => 'Equipo registrado en el sistema',
                        'asset_id' => $accessoryAsset->id,
                        'performed_by' => $user?->staff_id ?? null,
                        'performed_at' => Carbon::now(),
                    ]);
                    $accesoriesCreated[] = $accessoryAsset;
                }

                AssetHistory::create([
                    'action' => AssetHistoryAction::CREATED->value,
                    'description' => 'Equipo registrado en el sistema',
                    'asset_id' => $asset->id,
                    'performed_by' => $user?->staff_id ?? null,
                    'performed_at' => Carbon::now(),
                ]);

                // AssetHistory::create([
                //     'action' => AssetHistoryAction::CREATED->value,
                //     'description' => 'Equipo registrado en el sistema',
                //     'asset_id' => $charger->id,
                //     'performed_by' => $user?->staff_id ?? null,
                //     'performed_at' => Carbon::now(),
                // ]);



                //* Assign Asset and Charger
                if ($assignedTo) {
                    $parent = AssetAssignment::create([
                        'asset_id' => $asset->id,
                        'assigned_to_id' => $assignedTo->staff_id,
                        'assigned_at' => Carbon::now(),
                        'returned_at' => null,
                        'comment' => 'Asignación inicial durante la importación',
                        'return_comment' => null,
                        'responsible_id' => null,
                        'return_reason' => null,
                        'parent_assignment_id' => null,
                    ]);

                    if (count($accesoriesCreated) > 0) {

                        AssetHistory::create([
                            'action' => AssetHistoryAction::ASSIGNED->value,
                            'description' => "Equipo asignado a {$assignedTo->full_name} junto con los accesorios: " . implode(', ', array_map(fn($a) => $a->full_name, $accesoriesCreated)),
                            'asset_id' => $asset->id,
                            'performed_by' => $user?->staff_id ?? null,
                            'performed_at' => Carbon::now(),
                        ]);

                        foreach ($accesoriesCreated as $accesory) {
                            AssetAssignment::create([
                                'asset_id' => $accesory->id,
                                'assigned_to_id' => $assignedTo->staff_id,
                                'assigned_at' => Carbon::now(),
                                'returned_at' => null,
                                'comment' => 'Asignación inicial durante la importación',
                                'return_comment' => null,
                                'responsible_id' => null,
                                'return_reason' => null,
                                'parent_assignment_id' => $parent->id,
                            ]);

                            AssetHistory::create([
                                'action' => AssetHistoryAction::ASSIGNED->value,
                                'description' => "Accesorio '{$accesory->full_name}' asignado a {$assignedTo->full_name} junto al equipo principal ({$asset->full_name})",
                                'asset_id' => $accesory->id,
                                'performed_by' => $user?->staff_id ?? null,
                                'performed_at' => Carbon::now(),
                            ]);

                            $accesory->update(['status' => AssetStatus::ASSIGNED->value]);
                        }
                    }

                    // AssetAssignment::create([
                    //     'asset_id' => $charger->id,
                    //     'assigned_to_id' => $assignedTo->staff_id,
                    //     'assigned_at' => Carbon::now(),
                    //     'returned_at' => null,
                    //     'comment' => 'Asignación inicial durante la importación',
                    //     'return_comment' => null,
                    //     'responsible_id' => $assignedTo->staff_id,
                    //     'return_reason' => null,
                    //     'parent_assignment_id' => $parent->id,
                    // ]);





                    // AssetHistory::create([
                    //     'action' => AssetHistoryAction::ASSIGNED->value,
                    //     'description' => "Accesorio asignado a {$assignedTo->full_name} junto al equipo principal ({$asset->full_name})",
                    //     'asset_id' => $charger->id,
                    //     'performed_by' => $user?->staff_id ?? null,
                    //     'performed_at' => Carbon::now(),
                    // ]);

                    $asset->update(['status' => AssetStatus::ASSIGNED->value]);
                    // $charger->update(['status' => AssetStatus::ASSIGNED->value]);
                }




            }, $array);


        });




        Schema::enableForeignKeyConstraints();
    }



    private function getAssigned($rows, $length)
    {
      
        //* 'cargador,mouse' -> ['cargador', 'mouse']
        $data = function ($row)  {
            $accesories = array_filter(explode(',', $row[9] ?? ''), fn($item) => !empty(trim($item)) && trim($item) !== '');
            ds($accesories);
            return [
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
                'assigned_to' => User::select('staff_id', 'firstname', 'lastname')->where('firstname', trim($row[0]))->where('lastname', trim($row[1]))->first(),
                'accessories' => array_map(function ($accesory)  {
                    return [
                        'name' => strtoupper($accesory),
                        'status' => AssetStatus::AVAILABLE->value,
                        'brand' => null,
                        'model' => null,
                        'color' => null,
                        'serial_number' => null,
                        'processor' => null,
                        'ram' => null,
                        'storage' => null,
                        'purchase_date' => null,
                        'warranty_expiration' => null,
                        'type_id' => 4, // Assuming 4 is accessory type
                        'is_new' => false,
                        'invoice_path' => null,
                        'phone' => null,
                        'imei' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $accesories),
                // 'charger' => [
                // 'name' => 'CARGADOR DE ' . $row[2],
                // 'status' => AssetStatus::AVAILABLE->value,
                // 'brand' => $row[3],
                // 'model' => null,
                // 'color' => null,
                // 'serial_number' => null,
                // 'processor' => null,
                // 'ram' => null,
                // 'storage' => null,
                // 'purchase_date' => null,
                // 'warranty_expiration' => null,
                // 'type_id' => 4, // Assuming 4 is accessory type
                // 'is_new' => false,
                // 'invoice_path' => null,
                // 'phone' => null,
                // 'imei' => null,
                // 'created_at' => now(),
                // 'updated_at' => now(),
                // ]

            ];
        };


        return
            array_filter(
                array_map(fn($row) => $data($row), array_slice($rows, 1, $length)),
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

    private function generateLaptopsChargerByLaptop(array $assets)
    {

        $laptops = array_filter($assets, fn($asset) => $asset['type_id'] === 1);
        return array_map(fn($asset) => [
            'name' => 'CARGADOR',
            'status' => AssetStatus::AVAILABLE->value,
            'brand' => $asset['brand'],
            'model' => null,
            'color' => null,
            'serial_number' => null,
            'processor' => null,
            'ram' => null,
            'storage' => null,
            'purchase_date' => null,
            'warranty_expiration' => null,
            'type_id' => 4, // Assuming 4 is accessory type
            'is_new' => false,
            'invoice_path' => null,
            'phone' => null,
            'imei' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $laptops);
    }
    // private function 


}
