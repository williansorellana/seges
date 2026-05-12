<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoutePlanningController extends Controller
{
    public function index()
    {
        $plannings = \App\Models\RoutePlanning::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('route-plannings.index', compact('plannings'));
    }

    public function create()
    {
        return view('route-plannings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_type' => 'required|in:terreno,reunion',
            'motive' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'requires_funds' => 'nullable|boolean',
            'requested_funds' => 'nullable|numeric|min:0|required_if:requires_funds,1',
            'requires_amipass' => 'nullable|boolean',
            'amipass_days' => 'nullable|integer|min:1|required_if:requires_amipass,1',
        ]);

        $planning = new \App\Models\RoutePlanning();
        $planning->user_id = auth()->id();
        $planning->trip_type = $validated['trip_type'];
        $planning->motive = $validated['motive'];
        $planning->destination = $validated['destination'];
        $planning->start_date = $validated['start_date'];
        $planning->end_date = $validated['end_date'];
        $planning->requires_funds = $request->has('requires_funds');
        $planning->requested_funds = $request->has('requires_funds') ? $validated['requested_funds'] : null;
        $planning->requires_amipass = $request->has('requires_amipass');
        $planning->amipass_days = $request->has('requires_amipass') ? $validated['amipass_days'] : null;
        
        // If Jefatura is assigned, go to pending_jefatura, else pending_controlling
        $planning->status = auth()->user()->jefatura_id ? 'pending_jefatura' : 'pending_controlling';
        
        $planning->save();

        return redirect()->route('route-plannings.index')->with('success', 'Planificación creada y enviada a revisión con éxito.');
    }

    public function approveByJefatura(\App\Models\RoutePlanning $planning)
    {
        if ($planning->user->jefatura_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'No autorizado.');
        }

        $planning->status = 'pending_controlling';
        $planning->save();

        return redirect()->back()->with('success', 'Solicitud aprobada y escalada a Controlling.');
    }

    public function rejectByJefatura(Request $request, \App\Models\RoutePlanning $planning)
    {
        $request->validate(['observation' => 'required|string|max:500']);

        if ($planning->user->jefatura_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'No autorizado.');
        }

        $planning->status = 'rejected';
        $planning->save();

        $planning->observations()->create([
            'user_id' => auth()->id(),
            'observation' => $request->observation,
            'action' => 'rejected'
        ]);

        return redirect()->back()->with('success', 'Solicitud rechazada. Se ha notificado al colaborador.');
    }

    public function approveByControlling(\App\Models\RoutePlanning $planning)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== 'Controlling') {
            abort(403, 'No autorizado.');
        }

        if ($planning->requires_funds || $planning->requires_amipass) {
            $planning->status = 'pending_finances';
            $message = 'Solicitud validada por Controlling y escalada a Finanzas.';
        } else {
            $planning->status = 'approved';
            $planning->digital_signature = hash('sha256', $planning->id . $planning->user_id . now());
            $planning->signed_at = now();
            $message = 'Solicitud aprobada exitosamente (Sin requerimientos financieros).';
        }

        $planning->save();

        return redirect()->back()->with('success', $message);
    }

    public function rejectByControlling(Request $request, \App\Models\RoutePlanning $planning)
    {
        $request->validate(['observation' => 'required|string|max:500']);

        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== 'Controlling') {
            abort(403, 'No autorizado.');
        }

        $planning->status = 'rejected';
        $planning->save();

        $planning->observations()->create([
            'user_id' => auth()->id(),
            'observation' => $request->observation,
            'action' => 'rejected'
        ]);

        return redirect()->back()->with('success', 'Solicitud rechazada por Controlling. Se ha notificado al colaborador.');
    }

    public function approveByFinances(\App\Models\RoutePlanning $planning)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== 'Finanzas') {
            abort(403, 'No autorizado.');
        }

        $planning->status = 'approved';
        // Firma Digital: Hash SHA-256 para asegurar integridad
        $planning->digital_signature = hash('sha256', $planning->id . $planning->user_id . now());
        $planning->signed_at = now();
        $planning->save();

        // Crear automáticamente el borrador de Rendición asociada
        \App\Models\Rendition::create([
            'route_planning_id' => $planning->id,
            'user_id' => $planning->user_id,
            'funds_received' => $planning->requested_funds ?? 0,
            'status' => 'draft'
        ]);

        return redirect()->back()->with('success', 'Fondos liberados. Solicitud aprobada y firmada digitalmente.');
    }

    public function rejectByFinances(Request $request, \App\Models\RoutePlanning $planning)
    {
        $request->validate(['observation' => 'required|string|max:500']);

        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== 'Finanzas') {
            abort(403, 'No autorizado.');
        }

        $planning->status = 'rejected';
        $planning->save();

        $planning->observations()->create([
            'user_id' => auth()->id(),
            'observation' => $request->observation,
            'action' => 'rejected'
        ]);

        return redirect()->back()->with('success', 'Solicitud rechazada por Finanzas. Se ha notificado al colaborador.');
    }
}
