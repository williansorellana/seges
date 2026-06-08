<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DigitalSignatureService;
use App\Models\WorkflowHistory;
use App\Helpers\WorkflowHelper;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Services\AmipassCalculatorService;

class RoutePlanningController extends Controller
{
    public function index()
    {
        $query = \App\Models\RoutePlanning::with([
            'workflowHistories.user',
            'rendition',
        ]);

        // Trabajador normal

        if (auth()->user()->role === WorkflowHelper::ROLE_WORKER) {

            $query->where('user_id', auth()->id());
        }

        // jefatura

        elseif (auth()->user()->role === WorkflowHelper::ROLE_JEFATURA) {

            $query->where('status', 'pending_jefatura')
                ->whereHas('user', function ($q) {
                    $q->where('jefatura_id', auth()->id());
                });
        }

        // controlling

        elseif (
            auth()->user()->departamento === WorkflowHelper::DEPARTMENT_CONTROLLING
            || auth()->user()->role === WorkflowHelper::ROLE_ADMIN
        ) {

            $query->where('status', 'pending_controlling');
        }

        // finanzas

        elseif (
            auth()->user()->departamento === WorkflowHelper::DEPARTMENT_FINANCES
        ) {

            $query->where('status', 'pending_finances');
        }

        $plannings = $query
            ->orderBy('updated_at', 'desc')
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
            'region' => 'nullable|string|max:255',
            'companions' => 'nullable|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'requires_funds' => 'nullable|boolean',
            'requested_funds' => 'nullable|numeric|min:1|required_if:requires_funds,1',

            'requires_amipass' => 'nullable|boolean',
            'amipass_start_time' => 'nullable|required_if:requires_amipass,1|date_format:H:i',
            'amipass_end_time' => 'nullable|required_if:requires_amipass,1|date_format:H:i',
        ]);

        $planning = new \App\Models\RoutePlanning();

        $planning->user_id = auth()->id();
        $planning->trip_type = $validated['trip_type'];
        $planning->motive = $validated['motive'];
        $planning->destination = $validated['destination'];
        $planning->region = $validated['region'] ?? null;
        $planning->companions = $validated['companions'] ?? null;
        $planning->start_date = $validated['start_date'];
        $planning->end_date = $validated['end_date'];

        $planning->requires_funds = $request->has('requires_funds');
        $planning->requested_funds = $request->has('requires_funds')
            ? $validated['requested_funds']
            : null;

        $planning->requires_amipass = $request->has('requires_amipass');

        if ($request->has('requires_amipass')) {
            $amipassCalculator = new AmipassCalculatorService();

            $amipassResult = $amipassCalculator->calculate(
                $validated['start_date'],
                $validated['end_date'],
                $validated['amipass_start_time'],
                $validated['amipass_end_time']
            );

            $planning->amipass_days = $amipassResult['business_days'];
            $planning->amipass_business_days = $amipassResult['business_days'];
            $planning->amipass_amount = $amipassResult['amount'];
            $planning->amipass_start_time = $validated['amipass_start_time'];
            $planning->amipass_end_time = $validated['amipass_end_time'];
            $planning->usual_zone = null;
            $planning->extraordinary_zone = $validated['destination'];
        } else {
            $planning->amipass_days = null;
            $planning->amipass_business_days = 0;
            $planning->amipass_amount = 0;
            $planning->amipass_start_time = null;
            $planning->amipass_end_time = null;
            $planning->usual_zone = null;
            $planning->extraordinary_zone = null;
        }

        if (auth()->user()->jefatura_id) {
            $planning->status = 'pending_jefatura';
        } else {
            $planning->status = $planning->trip_type === 'reunion'
                ? 'pending_finances'
                : 'pending_controlling';
        }

        $planning->save();

        $signatureService = new DigitalSignatureService();

        $signatureService->sign(
            model: $planning,
            user: auth()->user(),
            snapshot: [
                'planning_id' => $planning->id,
                'worker_name' => auth()->user()->name,
                'worker_rut' => auth()->user()->rut ?? null,
                'trip_type' => $planning->trip_type,
                'motive' => $planning->motive,
                'destination' => $planning->destination,
                'start_date' => $planning->start_date,
                'end_date' => $planning->end_date,
                'requires_funds' => $planning->requires_funds,
                'requested_funds' => $planning->requested_funds,
                'requires_amipass' => $planning->requires_amipass,
                'amipass_days' => $planning->amipass_days,
                'amipass_business_days' => $planning->amipass_business_days,
                'amipass_amount' => $planning->amipass_amount,
                'amipass_start_time' => $planning->amipass_start_time,
                'amipass_end_time' => $planning->amipass_end_time,
                'usual_zone' => $planning->usual_zone,
                'extraordinary_zone' => $planning->extraordinary_zone,
                'signed_at' => now()->toDateTimeString(),
            ],
            type: 'planning_worker_signature'
        );

        if ($planning->user->jefatura) {
            $planning->user->jefatura->notify(new WorkflowNotification(
                'Nueva planificación pendiente',
                'El trabajador ' . $planning->user->name . ' creó una planificación de ruta.',
                route('renditions.approvals')
            ));
        } else {
            $targetDepartment = $planning->trip_type === 'reunion'
                ? WorkflowHelper::DEPARTMENT_FINANCES
                : WorkflowHelper::DEPARTMENT_CONTROLLING;

            $users = User::where('departamento', $targetDepartment)->get();

            Notification::send($users, new WorkflowNotification(
                'Nueva planificación pendiente',
                'El trabajador ' . $planning->user->name . ' creó una planificación sin jefatura asignada.',
                $planning->trip_type === 'reunion'
                    ? route('renditions.finances')
                    : route('renditions.controlling')
            ));
        }

        return redirect()
            ->route('route-plannings.index')
            ->with('success', 'Planificación creada y enviada a revisión con éxito.');
    }

    public function approveByJefatura(\App\Models\RoutePlanning $planning)
    {
        // Validación autorización

        if (
            $planning->user->jefatura_id !== auth()->id()
            && auth()->user()->role !== WorkflowHelper::ROLE_ADMIN
        ) {
            abort(403, 'No autorizado.');
        }

        if ($planning->user_id === auth()->id()) {
            abort(403, 'No puedes aprobar tu propia planificación.');
        }

        if ($planning->status !== WorkflowHelper::STATUS_PENDING_JEFATURA) {
            abort(403, 'La planificación no está pendiente de Jefatura.');
        }

        // Firma digital jefatura

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

        // Workflow segun tipo viaje

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

        if ($planning->trip_type === 'reunion') {
            $financeUsers = User::where('departamento', WorkflowHelper::DEPARTMENT_FINANCES)->get();

            Notification::send($financeUsers, new WorkflowNotification(
                'Planificación pendiente en Finanzas',
                'Una planificación por reunión fue aprobada por jefatura.',
                route('renditions.finances')
            ));
        } else {
            $controllingUsers = User::where('departamento', WorkflowHelper::DEPARTMENT_CONTROLLING)->get();

            Notification::send($controllingUsers, new WorkflowNotification(
                'Planificación pendiente en Controlling',
                'Una planificación de terreno fue aprobada por jefatura.',
                route('renditions.controlling')
            ));
        }


        // Historial workflow

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\RoutePlanning::class,
            'workflowable_id' => $planning->id,
            'user_id' => auth()->id(),
            'action' => 'approved_by_jefatura',
            'from_status' => WorkflowHelper::STATUS_PENDING_JEFATURA,
            'to_status' => $planning->status,
            'observation' => 'Solicitud aprobada por jefatura.',
            'ip_address' => request()->ip(),
        ]);

        // respuesta

        return redirect()
            ->back()
            ->with(
                'success',
                'Solicitud aprobada por jefatura correctamente.'
            );
    }

    public function rejectByJefatura(Request $request, \App\Models\RoutePlanning $planning)
    {
        // validar observación

        $request->validate([
            'observation' => 'required|string|max:500'
        ]);

        // Validación autorización

        if (
            $planning->user->jefatura_id !== auth()->id()
            &&
            auth()->user()->role !== WorkflowHelper::ROLE_ADMIN
        ) {
            abort(403, 'No autorizado.');
        }

        if ($planning->user_id === auth()->id()) {
            abort(403, 'No puedes rechazar tu propia planificación.');
        }

        // Estado válido

        if ($planning->status !== WorkflowHelper::STATUS_PENDING_JEFATURA) {
            abort(403, 'La planificación no está pendiente de Jefatura.');
        }

        // Rechazar planificación
        $planning->status = WorkflowHelper::STATUS_REJECTED;
        $planning->save();

        // Registrar observación

        $planning->observations()->create([
            'user_id' => auth()->id(),
            'observation' => $request->observation,
            'action' => 'rejected'
        ]);

        // Historial workflow

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\RoutePlanning::class,
            'workflowable_id' => $planning->id,
            'user_id' => auth()->id(),
            'action' => 'rejected_by_jefatura',
            'from_status' => WorkflowHelper::STATUS_PENDING_JEFATURA,
            'to_status' => WorkflowHelper::STATUS_REJECTED,
            'observation' => $request->observation,
            'ip_address' => request()->ip(),
        ]);

        // Notificar al trabajador

        $planning->user->notify(new WorkflowNotification(
            'Planificación rechazada',
            'Tu planificación fue rechazada por Jefatura. Revisa las observaciones.',
            route('route-plannings.index')
        ));

        return redirect()
            ->back()
            ->with('success', 'Solicitud rechazada por Jefatura. Se ha notificado al colaborador.');
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

        if ($planning->status !== WorkflowHelper::STATUS_PENDING_CONTROLLING) {
            abort(403, 'La planificación no está pendiente de Controlling.');
        }

        if ($planning->user_id === auth()->id()) {
            abort(403, 'No puedes aprobar tu propia planificación.');
        }

        $planning->status = WorkflowHelper::STATUS_PENDING_FINANCES;
        $planning->save();

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\RoutePlanning::class,
            'workflowable_id' => $planning->id,
            'user_id' => auth()->id(),
            'action' => 'approved_by_controlling',
            'from_status' => WorkflowHelper::STATUS_PENDING_CONTROLLING,
            'to_status' => WorkflowHelper::STATUS_PENDING_FINANCES,
            'observation' => 'Solicitud aprobada por Controlling.',
            'ip_address' => request()->ip(),
        ]);

        $financeUsers = User::where('departamento', WorkflowHelper::DEPARTMENT_FINANCES)->get();

        Notification::send($financeUsers, new WorkflowNotification(
            'Planificación pendiente en Finanzas',
            'Una planificación fue validada por Controlling y requiere revisión de Finanzas.',
            route('renditions.finances')
        ));

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

        if (
            auth()->user()->role !== WorkflowHelper::ROLE_ADMIN
            &&
            auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING
        ) {
            abort(403, 'No autorizado.');
        }

        if ($planning->status !== WorkflowHelper::STATUS_PENDING_CONTROLLING) {
            abort(403, 'La planificación no está pendiente de Controlling.');
        }

        if ($planning->user_id === auth()->id()) {
            abort(403, 'No puedes rechazar tu propia planificación.');
        }

        $planning->status = WorkflowHelper::STATUS_REJECTED;
        $planning->save();

        $planning->observations()->create([
            'user_id' => auth()->id(),
            'observation' => $request->observation,
            'action' => 'rejected'
        ]);

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\RoutePlanning::class,
            'workflowable_id' => $planning->id,
            'user_id' => auth()->id(),
            'action' => 'rejected_by_controlling',
            'from_status' => WorkflowHelper::STATUS_PENDING_CONTROLLING,
            'to_status' => WorkflowHelper::STATUS_REJECTED,
            'observation' => $request->observation,
            'ip_address' => request()->ip(),
        ]);

        $planning->user->notify(new WorkflowNotification(
            'Planificación rechazada',
            'Tu planificación fue rechazada por Controlling. Revisa las observaciones.',
            route('route-plannings.index')
        ));

        return redirect()
            ->back()
            ->with('success', 'Solicitud rechazada por Controlling. Se ha notificado al colaborador.');
    }

    public function approveByFinances(\App\Models\RoutePlanning $planning)
    {
        if (
            auth()->user()->role !== WorkflowHelper::ROLE_ADMIN
            &&
            auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_FINANCES
        ) {
            abort(403, 'No autorizado.');
        }

        if ($planning->status !== WorkflowHelper::STATUS_PENDING_FINANCES) {
            return redirect()
                ->back()
                ->with('error', 'Esta planificación no se encuentra pendiente de Finanzas.');
        }

        if ($planning->user_id === auth()->id()) {
            abort(403, 'No puedes aprobar tu propia planificación.');
        }

        DB::transaction(function () use ($planning) {
            $signatureService = new DigitalSignatureService();

            $signature = $signatureService->sign(
                model: $planning,
                user: auth()->user(),
                snapshot: [
                    'planning_id' => $planning->id,
                    'worker_name' => $planning->user->name,
                    'worker_rut' => $planning->user->rut ?? null,
                    'destination' => $planning->destination,
                    'region' => $planning->region,
                    'trip_type' => $planning->trip_type,
                    'start_date' => $planning->start_date,
                    'end_date' => $planning->end_date,
                    'requested_funds' => $planning->requested_funds,
                    'requires_amipass' => $planning->requires_amipass,
                    'amipass_days' => $planning->amipass_days,
                    'amipass_business_days' => $planning->amipass_business_days,
                    'amipass_amount' => $planning->amipass_amount,
                    'approved_by' => auth()->user()->name,
                    'approved_at' => now()->toDateTimeString(),
                ],
                type: 'planning_finances_approval'
            );

            $planning->status = WorkflowHelper::STATUS_APPROVED;
            $planning->digital_signature = $signature->hash;
            $planning->signed_at = now();
            $planning->save();

            WorkflowHistory::create([
                'workflowable_type' => \App\Models\RoutePlanning::class,
                'workflowable_id' => $planning->id,
                'user_id' => auth()->id(),
                'action' => 'approved_by_finances',
                'from_status' => WorkflowHelper::STATUS_PENDING_FINANCES,
                'to_status' => WorkflowHelper::STATUS_APPROVED,
                'observation' => 'Solicitud aprobada por Finanzas.',
                'ip_address' => request()->ip(),
            ]);

            $fundsReceived = ($planning->requested_funds ?? 0) + ($planning->amipass_amount ?? 0);

            $rendition = \App\Models\Rendition::where('route_planning_id', $planning->id)
                ->where('user_id', $planning->user_id)
                ->first();

            if (!$rendition) {
                \App\Models\Rendition::create([
                    'route_planning_id' => $planning->id,
                    'user_id' => $planning->user_id,
                    'funds_received' => $fundsReceived,
                    'status' => WorkflowHelper::STATUS_DRAFT,
                ]);
            } elseif ($rendition->status === WorkflowHelper::STATUS_DRAFT) {
                $rendition->funds_received = $fundsReceived;
                $rendition->save();
            }
        });

        $planning->user->notify(new WorkflowNotification(
            'Planificación aprobada',
            'Tu planificación fue aprobada por Finanzas y ya puedes realizar la rendición.',
            route('renditions.index')
        ));

        return redirect()->back()->with('success', 'Fondos liberados. Solicitud aprobada y firmada digitalmente.');
    }

    public function rejectByFinances(Request $request, \App\Models\RoutePlanning $planning)
    {
        $request->validate(['observation' => 'required|string|max:500']);

        if (
            auth()->user()->role !== WorkflowHelper::ROLE_ADMIN
            &&
            auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_FINANCES
        ) {
            abort(403, 'No autorizado.');
        }

        if ($planning->status !== WorkflowHelper::STATUS_PENDING_FINANCES) {
            abort(403, 'La planificación no está pendiente de Finanzas.');
        }

        if ($planning->user_id === auth()->id()) {
            abort(403, 'No puedes rechazar tu propia planificación.');
        }

        $planning->status = WorkflowHelper::STATUS_REJECTED;
        $planning->save();

        $planning->observations()->create([
            'user_id' => auth()->id(),
            'observation' => $request->observation,
            'action' => 'rejected'
        ]);

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\RoutePlanning::class,
            'workflowable_id' => $planning->id,
            'user_id' => auth()->id(),
            'action' => 'rejected_by_finances',
            'from_status' => WorkflowHelper::STATUS_PENDING_FINANCES,
            'to_status' => WorkflowHelper::STATUS_REJECTED,
            'observation' => $request->observation,
            'ip_address' => request()->ip(),
        ]);

        $planning->user->notify(new WorkflowNotification(
            'Planificación rechazada',
            'Tu planificación fue rechazada por Finanzas. Revisa las observaciones.',
            route('route-plannings.index')
        ));

        return redirect()
            ->back()
            ->with('success', 'Solicitud rechazada por Finanzas. Se ha notificado al colaborador.');
    }

    public function downloadPdf(\App\Models\RoutePlanning $planning)
    {
        $user = auth()->user();

        if (
            $user->role !== WorkflowHelper::ROLE_ADMIN
            && $planning->user_id !== $user->id
            && $planning->user->jefatura_id !== $user->id
            && !in_array($user->departamento, [
                WorkflowHelper::DEPARTMENT_CONTROLLING,
                WorkflowHelper::DEPARTMENT_FINANCES,
            ])
        ) {
            abort(403, 'No autorizado.');
        }

        $planning->load(['user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('route-plannings.pdf', [
            'planning' => $planning,
        ])->setPaper('letter', 'portrait');

        return $pdf->download('Planificacion_REQ-' . str_pad($planning->id, 4, '0', STR_PAD_LEFT) . '.pdf');
    }
}
