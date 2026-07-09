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
            $isVehicleHistoryNotification =
                str_contains($notification->data['action_url'], 'historial-mantenimiento') ||
                str_contains($notification->data['action_url'], 'vehiculos/');

            if ($isVehicleHistoryNotification && Auth::user()->role !== 'supervisor') {
                return redirect()
                    ->route('requests.index')
                    ->with('error', 'Solo el supervisor a cargo puede ver el historial del vehículo.');
            }
            return redirect($notification->data['action_url']);
        }

        // Obtener ID del vehículo para redirigir (Fallback logic)
        $vehicleId = $notification->data['vehicle_id'] ?? null;

        if ($vehicleId) {
            if (Auth::user()->role !== 'supervisor'){
                return redirect()->route('vehicles.index')->with('success', 'Redirigiendo a vehículo...');
            }

            return redirect()
            ->route('requests.index')
            ->with('error', 'Solo el supervisor puede acceder a la gestión del vehículo.');

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

    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    public function latest()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->data['message'] ?? $notification->data['title'] ?? 'Nueva notificación',
                    'reason' => $notification->data['reason'] ?? null,
                    'action_url' => $notification->data['action_url'] ?? route('notifications.read', $notification->id),
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(null, true, true),
                ];
            });

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function destroyAll()
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'Todas las notificaciones fueron eliminadas.');
    }

}
