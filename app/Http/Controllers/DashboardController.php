<?php
namespace App\Http\Controllers;

use App\Enums\Department\Allowed;
use App\Enums\Asset\AssetStatus;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\RRHHDashboardService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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


    public function renderRRHHView(Request $request, RRHHDashboardService $service)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'brand' => ['nullable', 'integer', 'exists:brands,id'],
            'status' => ['nullable', Rule::in(AssetStatus::values())],
            'warranty' => ['nullable', Rule::in(['in_warranty', 'expiring_soon', 'expired', 'unknown'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $search = isset($validated['search']) ? trim($validated['search']) : null;

        $filters = [
            'search' => $search !== '' ? $search : null,
            'brand' => isset($validated['brand']) ? (int) $validated['brand'] : null,
            'status' => $validated['status'] ?? null,
            'warranty' => $validated['warranty'] ?? null,
            'start_date' => isset($validated['start_date']) ? \Carbon\Carbon::parse($validated['start_date'])->toDateString() : null,
            'end_date' => isset($validated['end_date']) ? \Carbon\Carbon::parse($validated['end_date'])->toDateString() : null,
        ];

        return Inertia::render('DashboardRRHH', [
            'stats' => Inertia::once(fn() => $service->getStats($filters)),
            'assetsByBrand' => Inertia::once(fn() => $service->getSmartphonesByBrand($filters)),
            'assetsByStatus' => Inertia::once(fn() => $service->getAssetByStatus($filters)),
            'smartphonesWarrantyStatus' => Inertia::once(fn() => $service->getSmartphonesWarrantyStatus($filters)),
            'assignedProfiRate' => Inertia::once(fn() => $service->getAssignedProfiRate($filters)),
            'monthlyAssignments' => Inertia::once(fn() => $service->getMonthlyAssignments($filters)),
            'monthlyAcquisitions' => Inertia::once(fn() => $service->getMonthlyAcquisitions($filters)),
            'assetsByDepartment' => Inertia::once(fn() => $service->getAssetsByDepartment($filters)),
            'recentAssets' => Inertia::once(fn() => $service->getRecentAssets($filters)),
            'brands' => Inertia::once(fn() => $service->getBrands()),
            'activeFilters' => $filters,

        ]);
    }
}


// getSlaComplianceByDateRange