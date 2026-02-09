<?php

namespace App\Http\Controllers;

use App\Enums\notification\NotificationEntity;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, Notification $notification)
    {
        $request->validate([
            'type' => 'nullable|in:'.NotificationEntity::implodeValues(),
        ]);

        try {
            
            // $notification->update(['read_at' => now()]);
            auth()->user()->notifications()
                ->where('id', $notification->id)
                ->update(['read_at' => now()]);
            
            Inertia::flash([
                'success' => 'La notificación ha sido marcada como leída.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);
        } catch (\Exception $e) {
            
            Inertia::flash([
                'success' => null,
                'error' => 'Ocurrió un error al marcar la notificación como leída.',
                'timestamp' => now()->timestamp,
            ]);
        }

       return back();
    }


    public function markAllAsRead(Request $request)
    {

   
        $request->validate([
            'type' => 'required|in:'.NotificationEntity::implodeValues(),
        ]);


        $type = $request->input('type');

       
        try {
                auth()->user()->notifications()
                    ->where('type', $type)
                    ->update(['read_at' => now()]);

            Inertia::flash([
                'success' => 'Todas las notificaciones han sido marcadas como leídas.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => 'Ocurrió un error al marcar las notificaciones como leídas.',
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }

    public function destroy(Notification $notification)
    {

        try {
            auth()->user()->notifications()
                ->where('id', $notification->id)
                ->delete();

            Inertia::flash([
                'success' => 'La notificación ha sido eliminada.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => 'Ocurrió un error al eliminar la notificación.',
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }

    public function clearRead()
    {

        try {
            auth()->user()->notifications()
                ->whereNotNull('read_at')
                ->delete();

            Inertia::flash([
                'success' => 'Se eliminó el historial de notificaciones.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);
        } catch (\Exception $e) {
            Inertia::flash([
                'success' => null,
                'error' => 'Ocurrió un error al eliminar el historial.',
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }
}
