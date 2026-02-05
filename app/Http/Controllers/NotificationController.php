<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function read($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);

        // Marcamos como leída para limpiar el "puntito rojo", pero la mostramos igual en la lista
        $notification->markAsRead();

        // Primero verificar action_url explícita
        if (isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }

        // Obtener ID del vehículo para redirigir (Fallback logic)
        $vehicleId = $notification->data['vehicle_id'] ?? null;

        if ($vehicleId) {
            return redirect()->route('vehicles.index')->with('success', 'Redirigiendo a vehículo...');
        }

        if (isset($notification->data['asset_code'])) {
            return redirect()->route('assets.index', ['search' => $notification->data['asset_code']]);
        }

        return back();
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notificación eliminada.');
    }
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }
}
