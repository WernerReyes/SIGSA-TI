<?php
namespace App\Services;

use App\Models\Department;


class DepartmentService
{
    function getTechDepartmentUsers()
    {
        $techDept = Department::whereIn('dept_id', [9, 10])->first();
        if (!$techDept) {
            return collect();
        }
        return $techDept->users;
    }
}