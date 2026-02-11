<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\UpdateSlaRequest;
use App\Services\SlaPolicyService;
use Inertia\Inertia;

class SettingController extends Controller
{

    public function renderAppearanceSettings()
    {
        return Inertia::render('settings/Appearance');
    }

    public function renderSlaPolicies(SlaPolicyService $slaService)
    {
        return Inertia::render('settings/SLA', [
            'slas' => $slaService->getSlaPolicies(),
        ]);
    }

    public function updateSlaPolicy(UpdateSlaRequest $request, SlaPolicyService $slaService)
    {
        try {
            $slaService->updateSlaPolicy($request->validated('slas'));

            Inertia::flash([
                'success' => 'SLA actualizado correctamente.',
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'error' => 'Ocurrió un error al actualizar la configuración de SLA. Por favor, inténtalo de nuevo.',
                'success' => null,
            ]);

        }

        return back();

    }


}
