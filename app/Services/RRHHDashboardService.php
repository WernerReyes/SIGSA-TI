<?php
namespace App\Services;

use App\Enums\Asset\AssetStatus;
use App\Models\Asset;
use App\Enums\Asset\AssetTypeEnum;
use Carbon\Carbon;

class RRHHDashboardService
{


    public function getStats()
    {
        $stats = Asset::isFromRRHH()
            ->leftJoin('assets_assignments', function ($join) {
                $join->on('assets.id', '=', 'assets_assignments.asset_id')
                    ->whereNull('assets_assignments.returned_at');
            })
            ->selectRaw("
                SUM(assets.type_id = ?) as smartphones,
                 SUM(assets.type_id = ?) as chargers,
                SUM(assets.status = ?) as decommissioned,
                SUM(assets.warranty_expiration < NOW()) as expired_warranty,
                COUNT(assets_assignments.id) as assigned,
                COUNT(CASE WHEN assets.status = ? THEN 1 END) as under_maintenance
    ", [
                AssetTypeEnum::CELL_PHONE,
                AssetTypeEnum::CELL_PHONE_CHARGER,
                AssetStatus::DECOMMISSIONED->value,
                AssetStatus::IN_REPAIR->value,
            ])
            ->first();

        $total = $stats->smartphones + $stats->chargers;
        return [
            'total' => $total,
            'smartphones' => $stats->smartphones,
            'chargers' => $stats->chargers,
            'decommissioned' => $stats->decommissioned,
            'expired_warranty' => $stats->expired_warranty ?? 0,
            'under_maintenance' => $stats->under_maintenance,
            'assigned' => $stats->assigned,
            'profi_rate' => $this->getAssignedProfiRate(),
        ];
    }

    public function getSmartphonesByBrand()
    {
        return Asset::isFromRRHH()
            ->where('type_id', AssetTypeEnum::CELL_PHONE)
            ->selectRaw('brand, COUNT(*) as total')
            ->groupBy('brand')
            ->orderByDesc('total')
            ->get();
    }


    public function getAssetByStatus()
    {
        $assets = Asset::isFromRRHH()
            ->selectRaw("
            status,
            COALESCE(SUM(type_id = ?), 0) as smartphones,
            COALESCE(SUM(type_id = ?), 0) as chargers
        ", [
                AssetTypeEnum::CELL_PHONE,
                AssetTypeEnum::CELL_PHONE_CHARGER
            ])
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $statuses = collect(AssetStatus::cases())->pluck('value');

        return $statuses->mapWithKeys(function ($status) use ($assets) {
            $asset = $assets->get($status);

            return [
                $status => [
                    'smartphones' => $asset->smartphones ?? 0,
                    'chargers' => $asset->chargers ?? 0,
                    'total' => ($asset->smartphones ?? 0) + ($asset->chargers ?? 0),
                ]
            ];
        });
    }


    public function getSmartphonesWarrantyStatus()
    {
        $data = Asset::isFromRRHH()
            ->where('type_id', AssetTypeEnum::CELL_PHONE)
            ->selectRaw("
            CASE 
                WHEN warranty_expiration < CURDATE() THEN 'expired'
                WHEN warranty_expiration >= CURDATE() 
                     AND warranty_expiration < DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring_soon'
                WHEN warranty_expiration >= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'in_warranty'  
                ELSE 'unknown'
            END as warranty_status,
            COUNT(*) as total
        ")
            ->groupBy('warranty_status')
            ->get()
            ->keyBy('warranty_status');

        // Normalizar salida
        return collect([
            'in_warranty' => 0,
            'expiring_soon' => 0,
            'expired' => 0,
            'unknown' => 0,
        ])->merge($data->mapWithKeys(function ($item) {
            return [$item->warranty_status => $item->total];
        }));
    }

    public function getAssignedProfiRate()
    {
        $stats = Asset::isFromRRHH()
            ->leftJoin('assets_assignments', function ($join) {
                $join->on('assets.id', '=', 'assets_assignments.asset_id')
                    ->whereNull('assets_assignments.returned_at');
            })
            ->where('assets.type_id', AssetTypeEnum::CELL_PHONE)
            ->selectRaw("
        COUNT(DISTINCT assets.id) as total,
        COUNT(DISTINCT assets_assignments.asset_id) as assigned
    ")
            ->first();

        $utilizationRate = $stats->total > 0
            ? round(($stats->assigned / $stats->total) * 100, 2)
            : 0;

        return $utilizationRate;
    }

    public function getMonthlyAssignments()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $data = Asset::isFromRRHH()
            ->join('assets_assignments', 'assets.id', '=', 'assets_assignments.asset_id')
            // ->whereIn('assets.type_id', [
            //     AssetTypeEnum::CELL_PHONE,
            //     AssetTypeEnum::CELL_PHONE_CHARGER
            // ])
            ->whereNull('assets_assignments.returned_at')
            ->whereYear('assets_assignments.assigned_at', $currentYear)
            ->selectRaw("
            MONTH(assets_assignments.assigned_at) as month,
            SUM(assets.type_id = ?) as smartphones,
            SUM(assets.type_id = ?) as chargers
        ", [
                AssetTypeEnum::CELL_PHONE,
                AssetTypeEnum::CELL_PHONE_CHARGER
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        return collect(range(1, $currentMonth))->map(function ($monthNumber) use ($data, $currentYear) {
            $monthData = $data->get($monthNumber);
            $monthDate = Carbon::create($currentYear, $monthNumber, 1);

            return [
                'label' => $monthDate->translatedFormat('M Y'),
                'month' => $monthNumber,
                'smartphones' => (int) ($monthData->smartphones ?? 0),
                'chargers' => (int) ($monthData->chargers ?? 0),
            ];
        })->values();
    }


    public function getMonthlyAcquisitions()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $data = Asset::isFromRRHH()
            // ->whereIn('type_id', [
            //     AssetTypeEnum::CELL_PHONE,
            //     AssetTypeEnum::CELL_PHONE_CHARGER
            // ])
            ->whereYear('purchase_date', $currentYear)
            ->selectRaw("
            MONTH(purchase_date) as month,
            SUM(type_id = ?) as smartphones,
            SUM(type_id = ?) as chargers
        ", [
                AssetTypeEnum::CELL_PHONE,
                AssetTypeEnum::CELL_PHONE_CHARGER
            ])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        return collect(range(1, $currentMonth))->map(function ($monthNumber) use ($data, $currentYear) {
            $monthData = $data->get($monthNumber);
            $monthDate = Carbon::create($currentYear, $monthNumber, 1);

            return [
                'label' => $monthDate->translatedFormat('M Y'),
                'month' => $monthNumber,
                'smartphones' => (int) ($monthData->smartphones ?? 0),
                'chargers' => (int) ($monthData->chargers ?? 0),
            ];
        })->values();
    }

    public function getAssetsByDepartment()
    {
        $assets = Asset::isFromRRHH()
            ->where('assets.status', AssetStatus::ASSIGNED->value)
            // ->whereIn('assets.type_id', [
            //     AssetTypeEnum::CELL_PHONE,
            //     AssetTypeEnum::CELL_PHONE_CHARGER
            // ])
            ->leftJoin('assets_assignments', function ($join) {
                $join->on('assets.id', '=', 'assets_assignments.asset_id')
                    ->whereNull('assets_assignments.returned_at');
            })
            ->leftJoin('ost_staff', 'assets_assignments.assigned_to_id', '=', 'ost_staff.staff_id')
            ->leftJoin('ost_department', 'ost_staff.dept_id', '=', 'ost_department.id')
            ->selectRaw("
            ost_department.id as department_id,
            ost_department.name as department,
            SUM(assets.type_id = ?) as smartphones,
            SUM(assets.type_id = ?) as chargers,
            COUNT(assets.id) as total
        ", [
                AssetTypeEnum::CELL_PHONE,
                AssetTypeEnum::CELL_PHONE_CHARGER
            ])
            ->groupBy('ost_department.id', 'ost_department.name')
            ->get()
            ->keyBy('department_id');

        $departments = app(DepartmentService::class)->getBasicInfo();

        return $departments->map(function ($dept) use ($assets) {
            $assetData = $assets->get($dept->id);

            return [
                'department' => $dept->name,
                'smartphones' => (int) ($assetData->smartphones ?? 0),
                'chargers' => (int) ($assetData->chargers ?? 0),
                'total' => (int) ($assetData->total ?? 0),
            ];
        })->values(); // opcional
    }

    public function getRecentAssets()
    {
        return Asset::isFromRRHH()
        ->select('id', 'name', 'brand', 'model', 'type_id', 'status', 'created_at')
        ->with('type')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();
    }

}
