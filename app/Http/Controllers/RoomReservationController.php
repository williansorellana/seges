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
use App\Models\RoomReservationGuest;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Notifications\RoomGuestInvitationNotification;

class RoomReservationController extends Controller
{

/**
 * Muestra el catálogo de salas disponibles.
 */
    public function index()
    {
    
        $rooms = MeetingRoom::where('status', 'active')->get();
        
       // Verificar si cada sala está ocupada actualmente.
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

    /**
 * Registra una solicitud de reserva de sala.
 */
    public function store(Request $request)
    {
        
        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'purpose' => 'required|string|max:255',
            'attendees' => 'required|integer|min:1',
            'cellphone' => [
                'required',
                'string',
                'size:9',
                'regex:/^9\d{8}$/',
            ],
            'resources' => 'nullable|string|max:500',
            'guests' => 'nullable|array|max:20',
            'guests.*.name' => 'required_with:guests.*.email|string|max:255',
            'guests.*.email' => 'required_with:guests.*.name|email|max:255',
            'cellphone.required' => 'El número de contacto es obligatorio.',
            'cellphone.size' => 'El número de contacto debe tener exactamente 9 dígitos.',
            'cellphone.regex' => 'El número de contacto debe comenzar con 9 y tener 9 dígitos.',
        
        ]);
        $timezone = 'America/Santiago';

        // Convertir fechas usando zona horaria local.
        $start = Carbon::parse($request->start_time, $timezone);
        $end = Carbon::parse($request->end_time, $timezone);
        $now = Carbon::now($timezone);

        //no modificaremos $now pero añadiremos una función que permite comprobar mejor la fecha
        if ($start->lt($now->copy()->subMinute())) {
            return back()->withErrors(['start_time' => '⚠️ No puedes reservar en una fecha u hora pasada (Hora actual: ' . $now->format('H:i') . ').']);
        }

        if ($end->lte($start)) {
            return back()->withErrors(['end_time' => '⚠️ La hora de término debe ser después del inicio.']);
        }
        
        // Validar que no exista solapamiento de reservas.
        $exists = $this->hasRoomConflict(
            $request->meeting_room_id,
            $start,
            $end
        );

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
            'cellphone' => $request->cellphone,
            'resources' => $request->resources,
            'status' => 'pending' 
        ]);

        // Registrar invitados externos de la reserva.
        if ($request->filled('guests')) {
            foreach ($request->guests as $guest) {
                if (!empty($guest['name']) && !empty($guest['email'])) {
                    RoomReservationGuest::create([
                        'room_reservation_id' => $reservation->id,
                        'name' => $guest['name'],
                        'email' => $guest['email'],
                    ]);
                }
            }
        }

        // Notificar a supervisores del módulo de salas.
        $recipients = User::where('is_active', 1)
            ->where('role', 'supervisor')
            ->where(function($sq) {
                $sq->whereJsonContains('authorized_modules', 'rooms')
                   ->orWhereJsonContains('authorized_modules', 'all');
                })
            ->get();
        try {
            if ($recipients->count() > 0) {
                Notification::send($recipients, new NewReservationRequest($reservation));
            }
        } catch (\Exception $e) {
           \Log::error('Error al enviar notificación de reserva de sala', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('reservations.my_reservations')->with('success', 'Solicitud enviada correctamente.');
    }

    /**
 * Aprueba una reserva pendiente.
 */
   public function approve($id)
    {
        $reservation = RoomReservation::findOrFail($id);

        $start = Carbon::parse($reservation->start_time);
        $end = Carbon::parse($reservation->end_time);

        // Verificar que no exista otra reserva ya aprobada
        $exists = RoomReservation::where('meeting_room_id', $reservation->meeting_room_id)
            ->where('status', 'approved')
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                    ->where('end_time', '>', $start);
            })
            ->exists();

        if ($exists) {
            return redirect()->back()->with(
                'error',
                '⛔ No se puede aprobar: ya existe otra reserva confirmada en este horario.'
            );
        }

        // Aprobar la reserva seleccionada
        $reservation->status = 'approved';
        $reservation->save();

        // Rechazar automáticamente las demás solicitudes
        // pendientes que se crucen con esta reserva.
        $conflictingReservations = RoomReservation::where(
                'meeting_room_id',
                $reservation->meeting_room_id
            )
            ->where('status', 'pending')
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                    ->where('end_time', '>', $start);
            })
            ->get();

        foreach ($conflictingReservations as $pending) {
            $pending->status = 'rejected';
            $pending->save();
        }

        // Notificar al usuario cuya reserva fue aprobada
        $reservation->user->notify(
            new ReservationConfirmed($reservation)
        );

        $reservation->load(['guests', 'meetingRoom', 'user']);

        // Enviar invitaciones a los invitados
        foreach ($reservation->guests as $guest) {
            (new AnonymousNotifiable)
                ->route('mail', $guest->email)
                ->notify(
                    new RoomGuestInvitationNotification(
                        $reservation,
                        $guest->name
                    )
                );
        }

        return redirect()->back()->with(
            'success',
            'Reserva aprobada con éxito. Las solicitudes que se solapaban fueron rechazadas automáticamente.'
        );
    }

/**
 * Rechaza una reserva pendiente.
 */
    public function reject($id)
    {
        $reservation = RoomReservation::findOrFail($id);
        $reservation->status = 'rejected';
        $reservation->save();

        return redirect()->back()->with('success', 'Reserva rechazada.');
    }

/**
 * Muestra las reservas del usuario autenticado(logado).
 */
    public function myReservations()
    {
        $reservations = RoomReservation::where('user_id', Auth::id())
            ->with('meetingRoom') 
            ->orderBy('start_time', 'desc') 
            ->get();

        return view('reservations.my_reservations', compact('reservations'));
    }

   /**
 * Cancela una reserva del usuario autenticado.
 */
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
        $selectedRoomId = $request->input('room_id');

        $selectedRoom = null;

        if ($selectedRoomId) {
        $selectedRoom = MeetingRoom::find($selectedRoomId);
        }

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $reservationsRaw = RoomReservation::with(['user','meetingRoom'])
            ->where('status', 'approved')
            ->when($selectedRoomId, function ($query) use ($selectedRoomId) {
            $query->where('meeting_room_id', $selectedRoomId);
            })
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
            $query->where('start_time', '<=', $endOfMonth)
                  ->where('end_time', '>=', $startOfMonth);
            })
            ->orderBy('start_time', 'asc')
            ->get();

        $reservations = collect();

        foreach ($reservationsRaw as $reservation) {
            $start = Carbon::parse($reservation->start_time)->startOfDay();
            $end = Carbon::parse($reservation->end_time)->startOfDay();

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                if ($date->between($startOfMonth, $endOfMonth)) {

                    $key = $date->format('Y-m-d');

                    if (!$reservations->has($key)) {
                    $reservations->put($key, collect());
                    }

                    $reservations[$key]->push($reservation);
                }
            }
        }

        return view('rooms.agenda', compact(
            'reservations',
            'month',
            'year',
            'selectedRoom',
            'selectedRoomId'
        ));
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

    /**
 * Genera reporte PDF de ocupación por rango.
 */
    public function downloadMonthlyReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
    
        $reservations = RoomReservation::with(['user', 'meetingRoom'])
            ->where('status', 'approved')
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.monthly_occupancy', [
            'reservations' => $reservations,
            'month' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
            'year' => '',
        ]);

        return $pdf->download(
            'informe_ocupacion_' . $startDate->format('d_m_Y') . '_al_' . $endDate->format('d_m_Y') . '.pdf'
        );
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
            'cellphone' => [
                'required',
                'string',
                'size:9',
                'regex:/^9\d{8}$/',
            ],
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

        if ($start->lt($now->copy()->subMinute())) {
            return back()->with('error_modal', '⚠️ Error: No puedes agendar en el pasado. La hora seleccionada ya pasó.');
        }

        if ($end->lte($start)) {
            return back()->with('error_modal', '⚠️ Error Lógico: La hora de término debe ser DESPUÉS de la hora de inicio.');
        }
        
        $exists = $this->hasRoomConflict(
            $request->meeting_room_id,
            $start,
            $end
        );      

        if ($exists) {
            return back()
                ->withErrors(['error' => '⚠️ La sala ya tiene una reserva aprobada. Por favor elige otro horario o sala.'])
                ->withInput();
        }

        
        $finalPurpose = "EXTERNO: " . $request->external_name . " - " . $request->purpose;

        RoomReservation::create([
            'user_id'         => Auth::id(), 
            'meeting_room_id' => $request->meeting_room_id,
            'start_time'      => $start,
            'end_time'        => $end,
            'purpose'         => $finalPurpose, 
            'attendees'       => $request->attendees,
            'cellphone'       => $request->cellphone,
            'resources'       => $request->resources,
            'status'          => 'approved' 
        ]);

        return redirect()->route('rooms.agenda')->with('success', 'Reserva externa agendada correctamente.');
    }
    
    private function hasRoomConflict($meetingRoomId, $start, $end, $excludeId = null){
        return  RoomReservation::where('meeting_room_id', $meetingRoomId)
            ->where('status', 'approved')
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
            })
            ->exists();
    }

}