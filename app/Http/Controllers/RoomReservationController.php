<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
use App\Models\RoomReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\NewReservationRequest;
use App\Notifications\ReservationConfirmed;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReservationCancelled;
use Barryvdh\DomPDF\Facade\Pdf;

class RoomReservationController extends Controller
{

    public function index()
    {
    
        $rooms = MeetingRoom::where('status', 'active')->get();
        
       
        foreach($rooms as $room) {
            $now = Carbon::now();
            $currentReservation = RoomReservation::where('meeting_room_id', $room->id)
                ->where('status', 'approved') 
                ->where('start_time', '<=', $now)
                ->where('end_time', '>=', $now)
                ->first();
            
            $room->is_occupied = $currentReservation ? true : false;
            $room->current_reservation_end = $currentReservation ? $currentReservation->end_time : null;
        }

        return view('reservations.catalog', compact('rooms'));
    }

    
    public function store(Request $request)
    {
        
        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'purpose' => 'required|string|max:255',
            'attendees' => 'required|integer|min:1', 
            'resources' => 'nullable|string|max:500',
        
        ]);
        $timezone = 'America/Santiago';

        //parsea en horario local
        $start = Carbon::parse($request->start_time, $timezone);
        $end = Carbon::parse($request->end_time, $timezone);
        $now = Carbon::now($timezone);

        //no modificaremos $now pero añadiremos una función que permite comprobar mejor la fecha
        if ($start->lt($now->subMinute())) {
            return back()->withErrors(['start_time' => '⚠️ No puedes reservar en una fecha u hora pasada (Hora actual: ' . $now->format('H:i') . ').']);
        }

        if ($end->lte($start)) {
            return back()->withErrors(['end_time' => '⚠️ La hora de término debe ser después del inicio.']);
        }
        
        //verificación de solapamiento (todo en horario local)
        $exists = RoomReservation::where('meeting_room_id', $request->meeting_room_id)
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
                      
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => '⚠️ Lo sentimos, ya existe una reserva en ese intervalo de horario. Por favor revisa la disponibilidad.']);
        }

        //creamos la reserva (guarda en horario local)
        $reservation = RoomReservation::create([
            'user_id' => Auth::id(),
            'meeting_room_id' => $request->meeting_room_id,
            'start_time' => $start, 
            'end_time' => $end,
            'purpose' => $request->purpose,
            'attendees' => $request->attendees,
            'resources' => $request->resources,
            'status' => 'pending' 
        ]);

        //vamos a dejar que solo los supervisores con modulo rooms les lleguen las notificaciones.
        $recipients = User::where('is_active', 1)
            ->where('role', 'supervisor')
            ->where(function($sq) {
                $sq->whereJsonContains('authorized_modules', 'rooms')
                   ->orWhereJsonContains('authorized_modules', 'all');
                })
            ->get();

        if ($recipients->count() > 0) {
            Notification::send($recipients, new NewReservationRequest($reservation));
        }

        return redirect()->route('reservations.my_reservations')->with('success', 'Solicitud enviada correctamente.');
    }

    // Aprobar Reserva
    public function approve($id)
    {
        $reservation = RoomReservation::findOrFail($id);
        
        $start = Carbon::parse($reservation->start_time);
        $end = Carbon::parse($reservation->end_time);

     
        $exists = RoomReservation::where('meeting_room_id', $reservation->meeting_room_id)
            ->where('status', 'approved') 
            ->where('id', '!=', $id) 
            ->where(function ($query) use ($start, $end) {
               
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
            })
            ->exists();

        
        if ($exists) {
            return redirect()->back()->with('error', '⛔ No se puede aprobar: Ya existe otra reserva confirmada en este horario.');
        }

        $reservation->status = 'approved';
        $reservation->save();
        $reservation->user->notify(new ReservationConfirmed($reservation));

        return redirect()->back()->with('success', 'Reserva aprobada con éxito.');
    }

    // Rechazar Reserva
    public function reject($id)
    {
        $reservation = RoomReservation::findOrFail($id);
        $reservation->status = 'rejected';
        $reservation->save();

        return redirect()->back()->with('success', 'Reserva rechazada.');
    }

    // Mostrar las reservas del usuario logueado
    public function myReservations()
    {
        $reservations = RoomReservation::where('user_id', Auth::id())
            ->with('meetingRoom') 
            ->orderBy('start_time', 'desc') 
            ->get();

        return view('reservations.my_reservations', compact('reservations'));
    }

   
    public function cancel($id)
    {
        $reservation = RoomReservation::findOrFail($id);

        
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para cancelar esta reserva.');
        }

       
        if ($reservation->status === 'cancelled') {
            return redirect()->back()->with('error', 'La reserva ya estaba cancelada.');
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        return redirect()->back()->with('success', 'Reserva cancelada correctamente.');
    }
    //actualizacion de disponibilidad de salas, porque cuando se hacia una reserva de varios días esta no se reflejaba en la agenda.
    public function availability(Request $request,MeetingRoom $room)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startOfMonth =\Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();

        $reservations = $room -> reservations()
            ->where('status', 'approved')
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('start_time', '<=', $endOfMonth)
                      ->where('end_time', '>=', $startOfMonth);
            })
            ->get();
        
        $expanded = collect();
        foreach ($reservations as $res) {
            if(!$res->start_time || !$res->end_time){
                continue;
            }

            $start = $res->start_time->copy()->startOfDay();
            $end = $res->end_time->copy()->startOfDay();

            for($date = $start->copy(); $date->lte($end); $date->addDay()){
                if($date->between($startOfMonth, $endOfMonth)){
                    $expanded->push([
                        'day'        => (int) $date->format('d'),
                        'month'      => (int) $date->format('m') - 1,
                        'year'       => (int) $date->format('Y'),
                        'start_time' => $res->start_time->format('H:i'),
                        'end_time'   => $res->end_time->format('H:i'),
                        'status'     => $res->status,
                        'purpose'    => $res->purpose,
                    ]);
                }
            }
        }

        return response()->json($expanded);
    }

    public function history()
    {
        $reservations = RoomReservation::with(['user', 'meetingRoom'])
            ->whereIn('status', ['approved', 'cancelled'])
            ->orderBy('start_time', 'desc') 
            ->paginate(20); 

        return view('rooms.history', compact('reservations'));
    }

    public function agenda(Request $request)
    {
        
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $reservationsRaw = RoomReservation::with(['user','meetingRoom'])
            ->where('status', 'approved')
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('start_time', '<=', $endOfMonth)
                      ->where('end_time', '>=', $startOfMonth);
            })
            ->orderBy('start_time', 'asc')
            ->get();

        $reservations = collect();
        
        foreach ($reservationsRaw as $reservation){
            $start = \Carbon\Carbon::parse($reservation->start_time)->startOfDay();
            $end = \Carbon\Carbon::parse($reservation->end_time)->startOfDay();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()){
                if ($date->between($startOfMonth, $endOfMonth)){
                     
                    $key = $date ->format('Y-m-d');

                    if(!reservations->has($key)){
                        $reservations->put($key, collect());
                    }

                    $reservations[$key]->push($reservation);
                }
            }
        }
        
        return view('rooms.agenda', compact('reservations', 'month', 'year'));
    }
    public function cancelByAdmin(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $reservation = RoomReservation::findOrFail($id);
        
       
        $reservation->status = 'cancelled'; 
        $reservation->save();

        
        $reservation->user->notify(new ReservationCancelled($reservation, $request->reason));

        return redirect()->back()->with('success', 'Reserva cancelada y usuario notificado.');
    }
    public function downloadMonthlyReport(\Illuminate\Http\Request $request)
    {
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $dateObj = \Carbon\Carbon::createFromDate($year, $month, 1);

        $reservations = RoomReservation::with(['user', 'meetingRoom'])
            ->whereMonth('start_time', $month)
            ->whereYear('start_time', $year)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.monthly_occupancy', [
            'reservations' => $reservations,
            'month' => $dateObj->locale('es')->monthName,
            'year' => $year
        ]);

        return $pdf->download('informe_ocupacion_' . $dateObj->format('m_Y') . '.pdf');
    }

    public function createExternal()
    {
        $rooms = MeetingRoom::where('status', 'active')->get();
        return view('reservations.create_external', compact('rooms'));
    }

    
    public function storeExternal(Request $request)
    {
        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'external_name'   => 'nullable|string|max:100', 
            'start_time'      => 'required|date',
            'end_time'        => 'required|date|after:start_time',
            'purpose'         => 'required|string|max:150',
            'attendees'       => 'required|integer|min:1',
            'resources'       => 'nullable|string|max:500',
        ]);

        $timezone = 'America/Santiago';
        $now = Carbon::now($timezone);
        
        try {
            $start = Carbon::parse($request->start_time, $timezone);
            $end = Carbon::parse($request->end_time, $timezone);
        } catch (\Exception $e) {
            return back()->with('error_modal', 'Formato de fecha inválido.');
        }

        if ($start->lt($now->subMinute())) {
            return back()->with('error_modal', '⚠️ Error: No puedes agendar en el pasado. La hora seleccionada ya pasó.');
        }

        if ($end->lte($start)) {
            return back()->with('error_modal', '⚠️ Error Lógico: La hora de término debe ser DESPUÉS de la hora de inicio.');
        }
        
        $exists = RoomReservation::where('meeting_room_id', $request->meeting_room_id)
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => '⚠️ La sala está ocupada en ese horario.']);
        }

        
        $finalPurpose = "EXTERNO: " . $request->external_name . " - " . $request->purpose;

        RoomReservation::create([
            'user_id'         => Auth::id(), 
            'meeting_room_id' => $request->meeting_room_id,
            'start_time'      => $start,
            'end_time'        => $end,
            'purpose'         => $finalPurpose, 
            'attendees'       => $request->attendees,
            'resources'       => $request->resources,
            'status'          => 'approved' 
        ]);

        return redirect()->route('rooms.agenda')->with('success', 'Reserva externa agendada correctamente.');
    }

}