<?php
namespace App\Services;

use App\Models\Department;
use Cache;


class DepartmentService
{

    function getBasicInfo() {
        $departments = Cache::remember('departments_basic_info', 3600, function() {
            return Department::select('id', 'name')->get();
        });
        return $departments;
    }
}