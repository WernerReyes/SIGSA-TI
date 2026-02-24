<?php

namespace Database\Seeders;

use App\Enums\Asset\AssetStatus;
use App\Enums\Asset\AssetTypeEnum;
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
use function PHPUnit\Framework\isNull;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        AssetHistory::truncate();
        AssetAssignment::truncate();
        Asset::truncate();


        Schema::enableForeignKeyConstraints(); // Eliminar esta línea para ejecutar el seeder con los datos del Excel
        return;

        $filePath = storage_path('app/assets.xlsx');

        // Convertir el Excel a un Array
        $rows = Excel::toArray(new AssetsImport, $filePath);

        $cechLima = $this->getTechAssigned($rows[0], 42); //* Cechriza Technical Lima
        $cechProvince = $this->getTechAssigned($rows[1], 39); //* Cechriza Technical Province
        $ydieza = $this->getTechAssigned($rows[2], 9); //* Ydieza Technical

        $administrativeAssigned = $this->getAdministrativeAssigned($rows[3], 22); //* Administrative Assets

        // $decommissioned = $this->getAvailable($rows[4], 3); //* Decommissioned Assets
        // $inRepair = $this->getAvailable($rows[5], 3); //* In repair Assets
        // $available = $this->getAvailable($rows[6], 3); //* Available

        $user = User::select('staff_id')->where('email', 'werner.reyes@cechriza.com')->first();

        $registeredAssigmentsDates = [
            72 => Carbon::parse('2025-12-17'), // SEGUNDO MARCELO TIMANÁ,
            186 => Carbon::parse('2025-12-17'), // ANDERSON ERICK RIOS ECHEGARAY,
            61 => Carbon::parse('2025-12-13'), // Jhimi Christian PEZO MORI
            21 => Carbon::parse('2026-01-09'), // JOSE ANDRES CARHUAS QUISPE 
            143 => Carbon::parse('2026-01-12'), // RUBÉN ALEJANDRO ZÁRATE RÍOS
            146 => Carbon::parse('2026-01-30'), // TATIANA YASMIN ODALIS VERA FLORES

        ];

        $extraOutSideAssigments = [
            [
                'name' => 'Teclado + Mouse Inalambrico',
                'status' => AssetStatus::AVAILABLE->value,
                'brand' => 'Logitech',
                'model' => 'MK235',
                'color' => null,
                'serial_number' => strtoupper('2518mr21b7g9'),
                'processor' => null,
                'ram' => null,
                'storage' => null,
                'purchase_date' => null,
                'warranty_expiration' => null,
                'type_id' => AssetTypeEnum::KEYBOARD,
                'is_new' => true,
                'invoice_path' => null,
                'phone' => null,
                'imei' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'assigned_to' => User::select('staff_id', 'firstname', 'lastname')->find(146), // TATIANA YASMIN ODALIS VERA FLORES
            ]
        ];

        DB::transaction(function () use ($cechLima, $cechProvince, $ydieza, $administrativeAssigned, $user, $registeredAssigmentsDates, $extraOutSideAssigments) {
            $array = array_merge($cechLima, $cechProvince, $ydieza, $administrativeAssigned, $extraOutSideAssigments);
            array_map(function ($data) use ($user, $registeredAssigmentsDates) {
                $accessories = $data['accessories'] ?? [];
                $assignedTo = $data['assigned_to'];

                unset($data['accessories']);
                unset($data['assigned_to']);


                //* Create Asset and Charger
                $asset = Asset::create($data);
                // $charger = Asset::create($chargerData);
                $accesoriesCreated = [];
                foreach ($accessories as $accesory) {
                    if (!isset($accesory['type_id'])) {
                        continue; // O manejar de otra forma si el type_id es esencial
                    }

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

                //* Assign Asset and Charger
                if ($assignedTo) {
                    $parent = AssetAssignment::create([
                        'asset_id' => $asset->id,
                        'assigned_to_id' => $assignedTo->staff_id,
                        'assigned_at' => $registeredAssigmentsDates[$assignedTo->staff_id] ?? Carbon::now(),
                        'returned_at' => null,
                        'comment' => null,
                        'return_comment' => null,
                        'responsible_id' => null,
                        'return_reason' => null,
                        'parent_assignment_id' => null,
                    ]);

                    if (count($accesoriesCreated) > 0) {

                        $realAccesories = array_filter($accesoriesCreated, fn($a) => !str_contains($a->name, 'MONITOR'));

                        AssetHistory::create([
                            'action' => AssetHistoryAction::ASSIGNED->value,
                            'description' => "Equipo asignado a {$assignedTo->full_name} junto con los accesorios: " . implode(', ', array_map(fn($a) => $a->full_name, $realAccesories)),
                            'asset_id' => $asset->id,
                            'performed_by' => $user?->staff_id ?? null,
                            'performed_at' => Carbon::now(),
                        ]);

                        foreach ($accesoriesCreated as $accesory) {
                            $isMonitor = str_contains($accesory->name, 'MONITOR');
                            AssetAssignment::create([
                                'asset_id' => $accesory->id,
                                'assigned_to_id' => $assignedTo->staff_id,
                                'assigned_at' => $registeredAssigmentsDates[$assignedTo->staff_id] ?? Carbon::now(),
                                'returned_at' => null,
                                'comment' => null,
                                'return_comment' => null,
                                'responsible_id' => null,
                                'return_reason' => null,
                                'parent_assignment_id' => $isMonitor ? null : $parent->id,
                            ]);

                            $description = $isMonitor
                                ? "Equipo asignado a {$assignedTo->full_name}"
                                : "Accesorio asignado a {$assignedTo->full_name} junto al equipo principal ({$asset->full_name})";

                
                            AssetHistory::create([
                                'action' => AssetHistoryAction::ASSIGNED->value,
                                'description' => $description,
                                'asset_id' => $accesory->id,
                                'performed_by' => $user?->staff_id ?? null,
                                'performed_at' => Carbon::now(),
                            ]);

                            $accesory->update(['status' => AssetStatus::ASSIGNED->value]);
                        }
                    } else {
                        AssetHistory::create([
                            'action' => AssetHistoryAction::ASSIGNED->value,
                            'description' => "Equipo asignado a {$assignedTo->full_name}",
                            'asset_id' => $asset->id,
                            'performed_by' => $user?->staff_id ?? null,
                            'performed_at' => Carbon::now(),
                        ]);
                    }

                    $asset->update(['status' => AssetStatus::ASSIGNED->value]);

                }




            }, $array);
        });



        Schema::enableForeignKeyConstraints();
    }



    private function getTechAssigned($rows, $length)
    {

        //* 'cargador,mouse' -> ['cargador', 'mouse']
        $data = function ($row) {
            $accesories = array_filter(explode(',', $row[9] ?? ''), fn($item) => !empty(trim($item)) && trim($item) !== '');

            if ($row[10] && trim($row[10]) === 'cambio de equipo') {
                return ["name" => null]; // Saltar esta fila si el motivo es "cambio de equipo"
            }


            if (is_null($row[2])) {
                return ["name" => null]; // Saltar esta fila si no hay un nombre asignado
            }

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
                // 'type_id' => ($row[2] === 'TABLET TECLADO' || $row[2] === 'CELULAR') ? 3 : 1,
                'type_id' => match (strtoupper($row[2])) {
                    
                    'TABLET TECLADO' => AssetTypeEnum::TABLET,
                    'CELULAR' => AssetTypeEnum::CELL_PHONE,
                    'LAPTOP', 'LATOP' => AssetTypeEnum::LAPTOP,
                    // default => AssetTypeEnum::PC, // Por defecto, si no es ni laptop ni celular ni tablet teclado, se asume que es PC
                },
                'is_new' => false,
                'invoice_path' => null,
                'phone' => null,
                'imei' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'assigned_to' => User::select('staff_id', 'firstname', 'lastname')->where('firstname', trim($row[0]))->where('lastname', trim($row[1]))->first(),
                'accessories' => array_map(function ($accesory) use ($row) {
                    return [
                        'name' => strtoupper($accesory),
                        'status' => AssetStatus::AVAILABLE->value,
                        'brand' => $row[3], // Asumir que el accesorio tiene la misma marca que el equipo principal
                        'model' => null,
                        'color' => null,
                        'serial_number' => null,
                        'processor' => null,
                        'ram' => null,
                        'storage' => null,
                        'purchase_date' => null,
                        'warranty_expiration' => null,
                        'type_id' => match (strtoupper($accesory . " " . $row[2])) {
                            // "MOUSE {$row[2]}" => 7,
                            // "TECLADO {$row[2]}" => 8,
                            // 'TABLET TECLADO' => 6,
                            "PATCHCOARD {$row[2]}" => AssetTypeEnum::PATCH_CABLE,
                            "CARGADOR LAPTOP" => AssetTypeEnum::LAPTOP_CHARGER,
                            // "CARGADOR {$row[2]}" => AssetTypeEnum::CHARGER_PHONE,
                            default => null, // Otro
                        },
                        'is_new' => false,
                        'invoice_path' => null,
                        'phone' => null,
                        'imei' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $accesories),

            ];
        };


        return
            array_filter(
                array_map(fn($row) => $data($row), array_slice($rows, 1, $length)),
                fn($asset) => !is_null($asset['name']) && $asset['name'] !== 'TABLET TECLADO'
            ); // Filtrar solo los activos con nombre no nulo
    }


    private function getAdministrativeAssigned($rows, $length)
    {

        return array_filter(
            array_map(function ($row) {
                $mouse = $row[28] && trim(strtolower($row[28])) !== 'no aplica' ? explode(" ", $row[28], 2) : null;
                $keyboard = $row[29] && trim(strtolower($row[29])) !== 'no aplica' ? explode(" ", $row[29], 2) : null;
                $monitors = $row[30] && trim(strtolower($row[30])) !== 'no aplica' ? explode(',', $row[30]) : [];

                $type = match (strtoupper($row[7])) {
                        'MINI PC' => AssetTypeEnum::MINI_PC,
                        'PC' => AssetTypeEnum::PC,
                        // 'CELULAR' => 3,
                        'LAPTOP' => AssetTypeEnum::LAPTOP,
                    };

                $charger = $type ===  AssetTypeEnum::LAPTOP ? [ // Si es laptop, asignar cargador de laptop
                    'name' => 'CARGADOR',
                    'status' => AssetStatus::AVAILABLE->value,
                    'brand' => strtoupper($row[8]) ?? null,
                    'model' => null,
                    'color' => null,
                    'serial_number' => null,
                    'processor' => null,
                    'ram' => null,
                    'storage' => null,
                    'purchase_date' => null,
                    'warranty_expiration' => null,
                    'type_id' => AssetTypeEnum::LAPTOP_CHARGER,
                    'is_new' => false,
                    'invoice_path' => null,
                    'phone' => null,
                    'imei' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ] : null; // Si no es ni laptop ni celular, no asignar cargador

                return [
                    'name' => strtoupper($row[7]),
                    'status' => AssetStatus::AVAILABLE->value,
                    'brand' => strtoupper($row[8]) ?? null,
                    'model' => $row[10] ? (string) $row[10] : null,
                    'color' => null,
                    'serial_number' => trim(strtolower($row[19])) === 'no aplica' ? null : (string) $row[19],
                    'processor' => $row[20],
                    'ram' => $row[27],
                    'storage' => $row[22], // Asumiendo que el almacenamiento principal es el del disco 1
                    'purchase_date' => null,
                    'warranty_expiration' => null,
                    'type_id' => $type,

                    'is_new' => false,
                    'invoice_path' => null,
                    'phone' => null,
                    'imei' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'assigned_to' => $row[2] ? User::select('staff_id', 'firstname', 'lastname')->find($row[2]) : null,
                    'accessories' => array_filter([
                        $charger,
                        $mouse ? [
                            'name' => 'MOUSE',
                            'status' => AssetStatus::AVAILABLE->value,
                            'brand' => strtoupper($mouse[0] ?? null),
                            'model' => $mouse[1] ?? null,
                            'color' => null,
                            'serial_number' => null,
                            'processor' => null,
                            'ram' => null,
                            'storage' => null,
                            'purchase_date' => null,
                            'warranty_expiration' => null,
                            'type_id' => AssetTypeEnum::MOUSE,
                            'is_new' => false,
                            'invoice_path' => null,
                            'phone' => null,
                            'imei' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ] : null,
                        $keyboard ? [
                            'name' => 'TECLADO',
                            'status' => AssetStatus::AVAILABLE->value,
                            'brand' => strtoupper($keyboard[0] ?? null),
                            'model' => $keyboard[1] ?? null,
                            'color' => null,
                            'serial_number' => null,
                            'processor' => null,
                            'ram' => null,
                            'storage' => null,
                            'purchase_date' => null,
                            'warranty_expiration' => null,
                            'type_id' => AssetTypeEnum::KEYBOARD,
                            'is_new' => false,
                            'invoice_path' => null,
                            'phone' => null,
                            'imei' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ] : null,
                        ...(count($monitors) > 0 ? array_map(function ($monitor) {
                            return [
                                'name' => 'MONITOR',
                                'status' => AssetStatus::AVAILABLE->value,
                                'brand' => strtoupper($monitor ?? null),
                                'is_new' => false,
                                'type_id' => AssetTypeEnum::MONITOR,
                                // ... resto de tus campos
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }, $monitors) : [])
                    ], fn($item) => !is_null($item)),
                ];
            }, array_slice($rows, 1, $length)),
            fn($asset) => !is_null($asset['assigned_to'])
        ); // Filtrar solo los activos con nombre no nulo

        //   ]

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



}
