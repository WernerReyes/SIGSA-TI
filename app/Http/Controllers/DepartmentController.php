<?php

namespace App\Http\Controllers;

use App\Services\DepartmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function renderBasicInfo(Request $request, DepartmentService $departmentService)
    {
        $component = $request->input('component', 'Departments/BasicInfo');
        $departments = $departmentService->getBasicInfo();
        return Inertia::render($component, [
            'departments' => $departments,
        ]);
    }
}
