<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use Intervention\Image\Laravel\Facades\Image;

class MeetingRoomController extends Controller
{

/**
 * Muestra la gestión de salas y solicitudes pendientes.
 */
    public function index()
    {
        $rooms = MeetingRoom::all();
        $pendingReservations = \App\Models\RoomReservation::where('status', 'pending')
            ->with(['user', 'meetingRoom']) 
            ->orderBy('start_time', 'asc')
            ->get();
        
        return view('rooms.index', compact('rooms', 'pendingReservations'));        
    }

/**
 * Registra una nueva sala de reuniones.
 */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,maintenance',
            'image' => 'nullable|image|max:10240', 
        ]);

        $data = $request->all();

// Procesar imagen de la sala si fue enviada.       
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            $filename = time() . '_' . uniqid() . '.webp';

            $image = Image::read($file)
                ->scale(width: 1000)
                ->encodeByExtension('webp', quality: 80);

            Storage::disk('public')->put('rooms/' . $filename, (string) $image);
            
            $data['image_path'] = 'rooms/' . $filename; 
        }

        MeetingRoom::create($data);

        return redirect()->route('rooms.index')->with('success', 'Sala creada correctamente.');
    }

    public function edit(MeetingRoom $room)
    {
        return view('rooms.edit', compact('room'));
    }

    /**
 * Actualiza los datos de una sala existente.
 */
    public function update(Request $request, MeetingRoom $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,maintenance',
            'image' => 'nullable|image|max:10240',
        ]);

        $data = $request->except('image'); 

// Reemplazar imagen anterior por la nueva.
        if ($request->hasFile('image')) {
            
           
            if ($room->image_path && Storage::disk('public')->exists($room->image_path)) {
                Storage::disk('public')->delete($room->image_path);
            }

           
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.webp';

            $image = Image::read($file)
                ->scale(width: 1000)
                ->encodeByExtension('webp', quality: 80);

            Storage::disk('public')->put('rooms/' . $filename, (string) $image);
            
            $data['image_path'] = 'rooms/' . $filename;
        }

        $room->update($data);

        return redirect()->route('rooms.index')->with('success', 'Sala actualizada.');
    }

    public function destroy(MeetingRoom $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Sala movida a la papelera.');
    }

/**
 * Muestra las salas enviadas a papelera.
 */
    public function trash()
    {
        $rooms = MeetingRoom::onlyTrashed()->get();
        return view('rooms.trash', compact('rooms'));
    }

    public function restore($id)
    {
        $room = MeetingRoom::withTrashed()->findOrFail($id);
        $room->restore();
        return redirect()->route('rooms.index')->with('success', 'Sala restaurada correctamente.');
    }

    public function forceDelete($id)
    {
        $room = MeetingRoom::withTrashed()->findOrFail($id);

        if ($room->image_path) {
            Storage::disk('public')->delete($room->image_path);
        }

        $room->forceDelete(); 
        return redirect()->back()->with('success', 'Sala eliminada permanentemente.');
    }
}