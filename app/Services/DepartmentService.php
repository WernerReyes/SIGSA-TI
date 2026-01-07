<?php
namespace App\Services;

use App\Models\Department;


class DepartmentService
{
    function getTechDepartmentUsers()
    {
        $techDept = Department::select(
            'id',
            'name'
        )->whereIn('id', [9, 10])->get();
        if ($techDept->isEmpty()) {
            return collect();
        }

        
        $deptWithUsers = $techDept->load('users:staff_id,firstname,lastname,dept_id');
        return $deptWithUsers;
    }

    function getAll() {
        return Department::select('id', 'name')->get();
    }
}