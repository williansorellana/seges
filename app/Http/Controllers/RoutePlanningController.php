<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DigitalSignatureService;
use App\Models\WorkflowHistory;
use App\Helpers\WorkflowHelper;

class RoutePlanningController extends Controller
{
    public function index()
    {
        $query = \App\Models\RoutePlanning::query();

        /*
        |--------------------------------------------------------------------------
        | Trabajador normal
        |--------------------------------------------------------------------------
        */

        if (auth()->user()->role === WorkflowHelper::ROLE_WORKER) {

            $query->where('user_id', auth()->id());
        }

        /*
        |--------------------------------------------------------------------------
        | Jefatura
        |--------------------------------------------------------------------------
        */

        elseif (auth()->user()->role === WorkflowHelper::ROLE_JEFATURA) {

            $query->where('status', 'pending_jefatura')
                ->whereHas('user', function ($q) {
                    $q->where('jefatura_id', auth()->id());
                });
        }

        /*
        |--------------------------------------------------------------------------
        | Controlling
        |--------------------------------------------------------------------------
        */

        elseif (
            auth()->user()->departamento === WorkflowHelper::DEPARTMENT_CONTROLLING
            || auth()->user()->role === WorkflowHelper::ROLE_ADMIN
        ) {

            $query->where('status', 'pending_controlling');
        }

        /*
        |--------------------------------------------------------------------------
        | Finanzas
        |--------------------------------------------------------------------------
        */

        elseif (
            auth()->user()->departamento === WorkflowHelper::DEPARTMENT_FINANCES
        ) {

            $query->where('status', 'pending_finances');
        }

        $plannings = $query
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
        /*
        |--------------------------------------------------------------------------
        | Validación autorización
        |--------------------------------------------------------------------------
        */

        if (
            $planning->user->jefatura_id !== auth()->id()
            && auth()->user()->role !== WorkflowHelper::ROLE_ADMIN
        ) {
            abort(403, 'No autorizado.');
        }

        /*
        |--------------------------------------------------------------------------
        | Firma digital jefatura
        |--------------------------------------------------------------------------
        */

        $signatureService = new DigitalSignatureService();

        $signatureService->sign(

            model: $planning,

            user: auth()->user(),

            snapshot: [

                'planning_id' => $planning->id,

                'worker' => $planning->user->name,

                'destination' => $planning->destination,

                'trip_type' => $planning->trip_type,

                'approved_by' => auth()->user()->name,

                'approved_at' => now()->toDateTimeString(),
            ],

            type: 'jefatura_approval'
        );

        /*
        |--------------------------------------------------------------------------
        | Workflow según tipo viaje
        |--------------------------------------------------------------------------
        */

        if ($planning->trip_type === 'reunion') {

            $financeExists = \App\Models\User::where('departamento', WorkflowHelper::DEPARTMENT_FINANCES)
                ->exists();

            if (!$financeExists) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'No existe ningún usuario perteneciente al departamento Finanzas.'
                    );
            }

            $planning->status = WorkflowHelper::STATUS_PENDING_FINANCES;

        } else {

            $controllingExists = \App\Models\User::where('departamento', WorkflowHelper::DEPARTMENT_CONTROLLING)
                ->exists();

            if (!$controllingExists) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'No existe ningún usuario perteneciente al departamento Controlling.'
                    );
            }

            $planning->status = WorkflowHelper::STATUS_PENDING_CONTROLLING;
        }

        $planning->save();

        /*
        |--------------------------------------------------------------------------
        | Historial workflow
        |--------------------------------------------------------------------------
        */

        WorkflowHistory::create([

            'workflowable_type' => \App\Models\RoutePlanning::class,

            'workflowable_id' => $planning->id,

            'user_id' => auth()->id(),

            'action' => 'approved_by_jefatura',

            'from_status' => 'pending_jefatura',

            'to_status' => $planning->status,

            'observation' => 'Solicitud aprobada por jefatura.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Respuesta
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->back()
            ->with(
                'success',
                'Solicitud aprobada por jefatura correctamente.'
            );
    }

    public function approveByControlling(\App\Models\RoutePlanning $planning)
    {
        if (
            auth()->user()->role !== WorkflowHelper::ROLE_ADMIN
            &&
            auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING
        ) {
            abort(403, 'No autorizado.');
        }

        $planning->status = WorkflowHelper::STATUS_PENDING_FINANCES;

        $planning->save();

        return redirect()
            ->back()
            ->with(
                'success',
                'Solicitud validada por Controlling y enviada a Finanzas.'
            );
    }

    public function rejectByControlling(Request $request, \App\Models\RoutePlanning $planning)
    {
        $request->validate(['observation' => 'required|string|max:500']);

        if (auth()->user()->role !== WorkflowHelper::ROLE_ADMIN && auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING) {
            abort(403, 'No autorizado.');
        }

        $planning->status = WorkflowHelper::STATUS_REJECTED;
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
        if (auth()->user()->role !== WorkflowHelper::ROLE_ADMIN && auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_FINANCES) {
            abort(403, 'No autorizado.');
        }

        $planning->status = WorkflowHelper::STATUS_APPROVED;
        // Firma Digital: Hash SHA-256 para asegurar integridad
        $planning->digital_signature = hash('sha256', $planning->id . $planning->user_id . now());
        $planning->signed_at = now();
        $planning->save();

        // Crear automáticamente el borrador de Rendición asociada
        \App\Models\Rendition::create([
            'route_planning_id' => $planning->id,
            'user_id' => $planning->user_id,
            'funds_received' => $planning->requested_funds ?? 0,
            'status' => WorkflowHelper::STATUS_DRAFT
        ]);

        return redirect()->back()->with('success', 'Fondos liberados. Solicitud aprobada y firmada digitalmente.');
    }

    public function rejectByFinances(Request $request, \App\Models\RoutePlanning $planning)
    {
        $request->validate(['observation' => 'required|string|max:500']);

        if (auth()->user()->role !== WorkflowHelper::ROLE_ADMIN && auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_FINANCES) {
            abort(403, 'No autorizado.');
        }

        $planning->status = WorkflowHelper::STATUS_REJECTED;
        $planning->save();

        $planning->observations()->create([
            'user_id' => auth()->id(),
            'observation' => $request->observation,
            'action' => 'rejected'
        ]);

        return redirect()->back()->with('success', 'Solicitud rechazada por Finanzas. Se ha notificado al colaborador.');
    }
}
