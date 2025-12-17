<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class EventsController extends Controller
{
    public function show()
    {
        return Inertia::render('Events', [
            'name' => 'name from controller',
        ]);
    }
}