<?php

namespace Database\Seeders;

use App\Imports\AssetsImport;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class OsticketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
    

        $rootPath = config('app.root_local_path');

        // $filePath = $rootPath . '\Downloads\Inventario_2026-03-05.xlsx';
        $filePath = $rootPath . '\Downloads\Inventario_2026-03-05.xlsx';
        // storage_path('app/assets.xlsx');

        // Convertir el Excel a un Array
        $rows = Excel::toArray(new AssetsImport(), $filePath);

        // ds($rows[0]);

        $toInsert = $this->firstDocumentParseToInsert($rows[0], 536, 522);

        ds($toInsert);

        $insetedData = [];
        $notInsetedData = [];
        foreach ($toInsert as $row) {
            try {
                DB::table('productos')->insert([
                    'codigo_producto' => $row['code'],
                    'descripcion' => $row['descripcion'],
                    'id_categoria' => $row['category'],
                    'id_marca' => $row['brand'],
                    'id_tipo_stock' => $row['stock_type'],
                    'id_modelo' => $row['model'],
                    'tipo' => $row['type'],
                ]);

                 DB::table('inventario')->insert([
                    'id_producto' => DB::table('productos')->where('codigo_producto', $row['code'])->value('id_producto'),
                    'id_area' => 7, // Asignar un área predeterminada o según corresponda
                    'stock_actual' => $row['stock'],
                    'Ubicacion' => 'Cechriza', 
                    // 'created_at' => now(),
                    // 'updated_at' => now(),
                ]);

                 
                $insetedData[] = $row;
            } catch (\Exception $e) {
                $notInsetedData[] = array_merge($row, ['error' => $e->getMessage()]);
                // Manejar el error, por ejemplo, registrarlo o mostrar un mensaje
                echo "Error al actualizar el producto con código {$row['code']}: " . $e->getMessage() . "\n";
            }
        }


        return;


        $toEdit = $this->secondDocumentParse($rows[0], 18);
        // $toEdit = $this->firstDocumentParse($rows[0], 34);

        // ds($toEdit);

        // return;
                $updatedData = [];
                $notUpdatedData = [];
            foreach ($toEdit as $row) {
               try {
                    DB::table('inventario as i')
                        ->join('productos as p', 'i.id_producto', '=', 'p.id_producto')
                        // ->join('marca as m', 'p.id_marca', '=', 'm.id_marca')
                        // ->join('modelo as mo', 'p.id_modelo', '=', 'mo.id_modelo')
                        // ->join('categorias_inventario as c', 'p.id_categoria', '=', 'c.id_categoria')
                        // ->join('tipos_stock as ts', 'p.id_tipo_stock', '=', 'ts.id_tipo_stock')
                        ->where('p.codigo_producto', $row['code'])
                        ->update([
                            'i.stock_actual' => $row['stock'],
                            'p.tipo' => $row['type'],
                            // 'p.id_marca' => $row['brand'],
                            // 'p.id_modelo' => $row['model'],
                            // 'p.id_categoria' => $row['category'],
                            'p.id_tipo_stock' => $row['stock_type'],

                        ]);

                    $updatedData[] = [
                        'code' => $row['code'],
                        'stock' => $row['stock'],
                        'type' => $row['type'],
                        'brand' => $row['brand'],
                        'model' => $row['model'],
                        'category' => $row['category'],
                        'stock_type' => $row['stock_type'],
                    ];
                } catch (\Exception $e) {
                    $notUpdatedData[] = [
                        'code' => $row['code'],
                        'stock' => $row['stock'],
                        'type' => $row['type'],
                        'brand' => $row['brand'],
                        'model' => $row['model'],
                        'category' => $row['category'],
                        'stock_type' => $row['stock_type'],
                        'error' => $e->getMessage(),
                    ];
                }
            }

            ds($updatedData);
            ds($notUpdatedData);
        
    }


    private function firstDocumentParseToInsert($data, $length = 43, $startRow = 2)
    {
        $parsedData = [];
        // ds($data);

        foreach ($data as $index => $row) {
            // if ($index  <= 1) continue; // Saltar la fila de encabezado
            $parsedData[] = [
                'code' => (string) $row[0],
                'descripcion' => $row[1],
                'stock' => $row[6],
                'type' => $this->parseType($row[8]),
                'brand' => null,
                'model' => null,
                'category' => $row[4] ? DB::table('categorias_inventario')->where('nombre_categoria', trim($row[4]))->value('id_categoria') : null,
                'stock_type' => $row[5] ? DB::table('tipos_stock')->where('descripcion', trim($row[5]))->value('id_tipo_stock') : null,
            ];
        }

        $filtaredData = array_filter($parsedData, function ($item) {
            return $item['code'] !== null; // Filtrar solo los elementos con código válido
        });

        return array_slice($filtaredData, $startRow, $length); // Limitar el número de filas procesadas
    }

    private function firstDocumentParse($data, $length = 43, $startRow = 2)
    {
        $parsedData = [];
        // ds($data);

        foreach ($data as $index => $row) {
            // if ($index  <= 1) continue; // Saltar la fila de encabezado
            $parsedData[] = [
                'code' => (string) $row[0],
                'stock' => $row[6],
                'type' => $this->parseType($row[8] . $row[9]),
            ];
        }

        $filtaredData = array_filter($parsedData, function ($item) {
            return $item['code'] !== null; // Filtrar solo los elementos con código válido
        });

        return array_slice($filtaredData, $startRow, $length); // Limitar el número de filas procesadas
    }

     private function secondDocumentParse($data, $length = 18, $startRow = 2)
    {
        $parsedData = [];
        // ds($data);

        

        foreach ($data as $index => $row) {
            // if ($index  <= 1) continue; // Saltar la fila de encabezado
            $row[8] = match ($row[8]) {
                'T y A' => 'TA',
                'A y T' => 'AT',
                '-' => null,
                default => $row[8],
            };

            $parsedData[] = [
                'code' => $row[0],
                'stock' => $row[6],
                'type' => $this->parseType($row[8]),
                'brand' => $row[2] ? 
                 DB::table('marca')->where('descripcion', trim($row[2]))->value('id_marca') : null,
                'model' => $row[3] ? DB::table('modelo')->where('descripcion', trim($row[3]))->value('id_modelo') : null,
                    'category' => $row[4] ? DB::table('categorias_inventario')->where('nombre_categoria', trim($row[4]))->value('id_categoria') : null,
                    'stock_type' => $row[5] ? DB::table('tipos_stock')->where('descripcion', trim($row[5]))->value('id_tipo_stock') : null,
            
            ];
        }

        $filtaredData = array_filter($parsedData, function ($item) {
            return $item['code'] !== null; // Filtrar solo los elementos con código válido
        });

        return array_slice($filtaredData, $startRow, $length); // Limitar el número de filas procesadas
    }

    private function parseType($type)
    {
       
        return match ($type) {
            'A' => 1,
            'AA' => 1,
            'T' => 2,
            'AT' => 3,
            'TA' => 3,
            'D' => 4,
            'AD' => 4,
            'TD' => 4,
            default => null,
        };
    }


    

   
}


// UPDATE inventario i
// INNER JOIN productos p ON i.id_producto = p.id_producto
// SET i.stock_actual = 2,   -- 40
//     p.tipo = 1   -- nuevo valor
// WHERE p.codigo_producto = 'CECH_ECO_003';