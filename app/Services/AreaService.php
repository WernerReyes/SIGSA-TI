<?php
namespace App\Services;

use App\Models\Area;
use Illuminate\Support\Facades\Cache;

class AreaService
{
    public function getAll()
    {
        ds('Fetching areas from cache or database');
        $areas = Cache::remember('areas_all', 60 * 60, function () {
            return Area::all();
        }); //* Cache for 1 hour
        return $areas;
    }
}