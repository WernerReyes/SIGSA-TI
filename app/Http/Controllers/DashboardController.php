<?php
namespace App\Http\Controllers;

use App\Enums\Department\Allowed;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\RRHHDashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function renderView(Request $request, DashboardService $dashboardService)
    {
        $isFromRRHH = auth()->user()->dept_id === Allowed::RRHH->value;
        if ($isFromRRHH) {
            return redirect()->route('dashboard.rrhh');
        }

        $start = $request->input('start_date', now()->startOfWeek()->toDateString());
        $end = $request->input('end_date', now()->endOfWeek()->toDateString());

        return Inertia::render('Dashboard', [
            'stats' => Inertia::once(fn() => $dashboardService->stats()),
            'tickets_by_priority' => Inertia::once(fn() => $dashboardService->ticketsByPriority()),
            'recent_tickets' => Inertia::once(fn() => $dashboardService->recentTickets()),
            'sla_compliance' => Inertia::once(fn() => $dashboardService->getSlaComplianceByDateRange($start, $end)),
            'recent_contract_notifications' => Inertia::once(fn() => $dashboardService->recentContractNotifications())
        ]);
    }


    public function renderRRHHView(RRHHDashboardService $service)
    {
    
        return Inertia::render('DashboardRRHH', [
            'stats' => Inertia::once(fn() => $service->getStats()),
            'assetsByBrand' => Inertia::once(fn() => $service->getSmartphonesByBrand()),
            'assetsByStatus' => Inertia::once(fn() => $service->getAssetByStatus()),
            'smartphonesWarrantyStatus' => Inertia::once(fn() => $service->getSmartphonesWarrantyStatus()),
            'assignedProfiRate' => Inertia::once(fn() => $service->getAssignedProfiRate()),
            'monthlyAssignments' => Inertia::once(fn() => $service->getMonthlyAssignments()),
            'monthlyAcquisitions' => Inertia::once(fn() => $service->getMonthlyAcquisitions()),
            'assetsByDepartment' => Inertia::once(fn() => $service->getAssetsByDepartment()),
            'recentAssets' => Inertia::once(fn() => $service->getRecentAssets()),

        ]);
    }
}


// getSlaComplianceByDateRange