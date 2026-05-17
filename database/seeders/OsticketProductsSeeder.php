<?php

namespace Database\Seeders;

use App\Imports\AssetsImport;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OsticketProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $notFound = [];
        $notFoundSSGG = $this->updateStockSSGG();
        $notFoundLOGISTICA = $this->updateStockLOGISTICA();

        $this->exportNotFoundReport($notFound, $notFoundSSGG, $notFoundLOGISTICA);

    }

    private function exportNotFoundReport(array $notFound, array $notFoundSSGG, array $notFoundLOGISTICA): void
    {
        $rows = [];

        foreach ($notFound as $codigo) {
            $rows[] = ['Inventario_2026-04-22', 'codigo_producto', $codigo];
        }

        foreach ($notFoundSSGG as $codigo) {
            $rows[] = ['Inventario_2026-05-15', 'codigo_producto', $codigo];
        }

        foreach ($notFoundLOGISTICA as $descripcion) {
            $rows[] = ['ECONOMATO STOCK 15.05.26', 'descripcion', $descripcion];
        }

        if (empty($rows)) {
            ds('No hay productos no encontrados para exportar.');
            return;
        }

        $export = new class($rows) implements FromArray, WithHeadings {
            private array $rows;

            public function __construct(array $rows)
            {
                $this->rows = $rows;
            }

            public function array(): array
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return ['origen', 'campo_busqueda', 'valor_no_encontrado'];
            }
        };

        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = "no_encontrados_{$timestamp}.xlsx";
        $downloadsPath = $this->getDownloadsPath();
        $fullPath = $downloadsPath . DIRECTORY_SEPARATOR . $fileName;

        File::ensureDirectoryExists($downloadsPath);

        $excelBinary = Excel::raw($export, ExcelFormat::XLSX);
        if (File::put($fullPath, $excelBinary) !== false) {
            ds('Reporte generado: ' . $fullPath);
            return;
        }

        ds('No se pudo escribir el reporte en Descargas.');
        ds('Ruta de Descargas: ' . $downloadsPath);
    }

    private function getDownloadsPath(): string
    {
        $basePath = getenv('USERPROFILE') ?: (getenv('HOMEDRIVE') . getenv('HOMEPATH'));
        if (!$basePath) {
            return storage_path('app/Downloads');
        }

        $basePath = rtrim($basePath, "\\/");
        return $basePath . DIRECTORY_SEPARATOR . 'Downloads';
    }
    

    private function getRows($path, $slice = 1)
    {
        $rootPath = config('app.root_local_path');
        $completePath = $rootPath . $path;

        // Convertir el Excel a un Array
        $rows = Excel::toArray(new AssetsImport(), $completePath);

        // Eliminar la primera fila (encabezados)
        $dataRows = array_slice($rows[0], $slice);

        return $dataRows;

    }


    private function updateRequiredPhotoAndDocument()
    {
        $products = $this->getRows('\Downloads\Inventario_2026-04-22.xlsx', 2);



        $parsedProducts =
            array_filter(
                array_map(function ($product) {
                    return [
                        'codigo_producto' => $product[0],
                        'descripcion' => $product[1],
                        'requiere_foto' => $product[9] === 'SI' ? 1 : 0,
                        'stock_actual' => $product[6],
                        'requiere_documento' => $product[10] === 'SI' ? 1 : 0,
                    ];
                }, $products),
                function ($product) {
                    return !empty($product['codigo_producto']);
                }
            );


        $updates = [];
        $notFound = [];

        try {
            foreach ($parsedProducts as $product) {
                $dbProductObj = DB::table('productos')
                    ->where('codigo_producto', $product['codigo_producto'])
                    ->first();

                if ($dbProductObj) {
                    DB::table('productos as p')
                        ->where('p.id_producto', $dbProductObj->id_producto)
                        ->leftJoin('inventario as i', 'p.id_producto', '=', 'i.id_producto')
                        ->update([
                            'p.requiere_foto_producto_anterior' => $product['requiere_foto'],
                            'p.requiere_documento' => $product['requiere_documento'],
                            'i.stock_actual' => $product['stock_actual'] ?? DB::raw('i.stock_actual'), // Mantener el stock actual si no se proporciona

                        ]);

                    $updates[] = [
                        'id_producto' => $dbProductObj->id_producto,
                        'codigo_producto' => $product['codigo_producto'],
                        'descripcion' => $product['descripcion'],
                        'stock_actual' => $product['stock_actual'] ?? 'No actualizado',
                        'requiere_foto_producto_anterior' => $product['requiere_foto'],
                        'requiere_documento' => $product['requiere_documento'],
                    ];
                } else {
                    $notFound[] = $product['codigo_producto'];
                }
            }

            // Mostrar resultados
            ds('Productos actualizados:');
            ds($updates);

            if (!empty($notFound)) {
                ds('Productos no encontrados:');
                ds($notFound);
            }
        } catch (\Exception $e) {
            // Manejo de errores
            ds('Error al procesar los productos: ' . $e->getMessage());
        }

        return $notFound;
    }

    private function updateStockSSGG()
    {
        $ssggProducts = $this->getRows('\Downloads\Inventario_2026-05-15.xlsx', 2);

        $parsedProducts =
            array_filter(
                array_map(function ($product) {
                    return [
                        'codigo_producto' => $product[0],
                        'descripcion' => $product[1],
                        'stock_actual' => $product[6],
                    ];
                }, $ssggProducts),
                function ($product) {
                    return !empty($product['codigo_producto']);
                }
            );

        $updates = [];
        $notFound = [];

        try {
            foreach ($parsedProducts as $product) {
                $dbProductObj = DB::table('productos')
                    ->where('codigo_producto', $product['codigo_producto'])
                    ->first();

                if ($dbProductObj) {
                    // Use query builder to perform the update since $dbProductObj is a stdClass
                    DB::table('productos as p')
                        ->where('p.id_producto', $dbProductObj->id_producto)
                        ->leftJoin('inventario as i', 'p.id_producto', '=', 'i.id_producto')
                        ->update([
                            'i.stock_actual' => $product['stock_actual'],
                        ]);


                    $updates[] = [
                        'id_producto' => $dbProductObj->id_producto,
                        'stock_actual' => $product['stock_actual'],
                    ];
                } else {
                    $notFound[] = $product['codigo_producto'];
                }
            }

            // Mostrar resultados
            ds('Productos actualizados:');
            ds($updates);

            if (!empty($notFound)) {
                ds('Productos no encontrados:');
                ds($notFound);
            }
        } catch (\Exception $e) {
            // Manejo de errores
            ds('Error al procesar los productos: ' . $e->getMessage());
        }

        return $notFound;

    }


    private function updateStockLOGISTICA()
    {
        $logisticaProducts = $this->getRows('\Downloads\ECONOMATO STOCK 15.05.26.xlsx', 3);


        // ds('Productos obtenidos de LOGISTICA:');


        $parsedProducts =
            array_filter(
                array_map(function ($product) {
                    return [
                        // 'codigo_producto' => $product[0],
                        'descripcion' => $product[1],
                        'stock_actual' => $product[5],
                    ];
                }, $logisticaProducts),
                function ($product) {
                    return isset($product['stock_actual']) && is_numeric($product['stock_actual']);
                }
            );

        $updates = [];
        $notFound = [];

        try {
            foreach ($parsedProducts as $product) {
                $dbProductObj = DB::table('productos')
                    ->where('descripcion', '=', $product['descripcion'])
                    ->first();
                if ($dbProductObj) {
                    // Use query builder to perform the update since $dbProductObj is a stdClass
                    DB::table('productos as p')
                        ->where('p.descripcion', '=', $dbProductObj->descripcion)
                        ->leftJoin('inventario as i', 'p.id_producto', '=', 'i.id_producto')
                        ->update([
                            'i.stock_actual' => $product['stock_actual'],
                        ]);

                    $updates[] = [
                        'id_producto' => $dbProductObj->id_producto,
                        'descripcion' => $dbProductObj->descripcion,
                        'stock_actual' => $product['stock_actual'],
                    ];
                } else {
                    $notFound[] = $product['descripcion'];
                }
            }
        } catch (\Exception $e) {
            // Manejo de errores
            ds('Error al procesar los productos: ' . $e->getMessage());
        }

        // Mostrar resultados
        ds('Productos actualizados:');
        ds($updates);

        if (!empty($notFound)) {
            ds('Productos no encontrados:');
            ds($notFound);
        }


        return $notFound;
    }





}


// UPDATE inventario i
// INNER JOIN productos p ON i.id_producto = p.id_producto
// SET i.stock_actual = 2,   -- 40
//     p.tipo = 1   -- nuevo valor
// WHERE p.codigo_producto = 'CECH_ECO_003';