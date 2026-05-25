<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\WorkflowHelper;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\WorkflowHistory;
use App\Services\DigitalSignatureService;

class RenditionController extends Controller
{
    public function index()
    {
        $renditions = \App\Models\Rendition::with(['routePlanning', 'observations.user'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('renditions.index', compact('renditions'));
    }

    public function show(\App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        $isOwner = $rendition->user_id === $user->id;

        $isAdmin = $user->role === 'admin';

        $isJefatura = $rendition->user->jefatura_id === $user->id;

        $isFinanzas = $user->departamento === WorkflowHelper::DEPARTMENT_FINANCES;

        $isControlling = $user->departamento === WorkflowHelper::DEPARTMENT_CONTROLLING;

        if (
            !$isOwner
            &&
            !$isAdmin
            &&
            !$isJefatura
            &&
            !$isFinanzas
            &&
            !$isControlling
        ) {
            abort(403);
        }

        $rendition->load('routePlanning','expenses','observations.user','workflowHistories.user', 'digitalSignatures.user');

        return view('renditions.show', compact('rendition'));
    }

    public function storeExpense(Request $request, \App\Models\Rendition $rendition)
    {
        if ($rendition->user_id !== auth()->id() || !in_array($rendition->status, ['draft', 'rejected'])) {
            abort(403, 'No puedes agregar gastos a esta rendición en su estado actual.');
        }

        $request->validate([
            'date' => 'required|date',
            'provider' => 'required|string|max:255',
            'document_type' => 'required|in:boleta,factura,vale,otro',
            'document_number' => 'nullable|string',
            'amount' => 'required|numeric|min:1',
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $path = $request->file('attachment')->store('receipts', 'local');

        $rendition->expenses()->create([
            'date' => $request->date,
            'provider' => $request->provider,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'amount' => $request->amount,
            'attachment_path' => $path
        ]);

        $rendition->total_declared = $rendition->expenses()->sum('amount');
        $rendition->save();

        return redirect()->back()->with('success', 'Documento subido y monto recalculado correctamente.');
    }

    public function updateExpense(Request $request, \App\Models\Rendition $rendition, \App\Models\RenditionExpense $expense)
    {
        if ($rendition->user_id !== auth()->id() || !in_array($rendition->status, ['draft', 'rejected'])) {
            abort(403, 'No puedes editar gastos de esta rendición en su estado actual.');
        }

        $request->validate([
            'date' => 'required|date',
            'provider' => 'required|string|max:255',
            'document_type' => 'required|in:boleta,factura,vale,otro',
            'document_number' => 'nullable|string',
            'amount' => 'required|numeric|min:1',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $data = $request->only(['date', 'provider', 'document_type', 'document_number', 'amount']);

        if ($request->hasFile('attachment')) {
            if (Storage::disk('local')->exists($expense->attachment_path)) {
                Storage::disk('local')->delete($expense->attachment_path);
            }

            $data['attachment_path'] = $request->file('attachment')->store('receipts', 'local');
        }

        $expense->update($data);

        $rendition->total_declared = $rendition->expenses()->sum('amount');
        $rendition->save();

        return redirect()->back()->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroyExpense(\App\Models\Rendition $rendition, \App\Models\RenditionExpense $expense)
    {
        if ($rendition->user_id !== auth()->id() || !in_array($rendition->status, ['draft', 'rejected'])) {
            abort(403, 'No puedes eliminar gastos de esta rendición en su estado actual.');
        }

        if (Storage::disk('local')->exists($expense->attachment_path)) {
            Storage::disk('local')->delete($expense->attachment_path);
        }

        $expense->delete();

        $rendition->total_declared = $rendition->expenses()->sum('amount');
        $rendition->save();

        return redirect()->back()->with('success', 'Gasto eliminado y monto recalculado.');
    }

    public function submitRendition(Request $request, \App\Models\Rendition $rendition)
    {
        if ($rendition->user_id !== auth()->id() || !in_array($rendition->status, ['draft', 'rejected'])) {
            abort(403, 'No autorizado.');
        }

        $request->validate([
            'user_observation' => 'nullable|string|max:1000',
        ]);

        if ($rendition->expenses()->count() === 0) {
            return redirect()
                ->back()
                ->withErrors([
                    'expenses' => 'Debes subir al menos un documento antes de enviar la rendición.'
                ]);
        }

        $signatureService = new DigitalSignatureService();

        $signatureService->sign(
            model: $rendition,
            user: auth()->user(),
            snapshot: [
                'rendition_id' => $rendition->id,
                'route_planning_id' => $rendition->route_planning_id,
                'worker_name' => auth()->user()->name,
                'worker_rut' => auth()->user()->rut ?? null,
                'funds_received' => $rendition->funds_received,
                'total_declared' => $rendition->expenses()->sum('amount'),
                'expenses_count' => $rendition->expenses()->count(),
                'signed_at' => now()->toDateTimeString(),
                'user_observation' => $request->user_observation,
            ],
            type: 'rendition_worker_signature'
        );

        // Envía a jefatura si tiene, si no, directo a controlling.
        $fromStatus = $rendition->status;

        $rendition->status = auth()->user()->jefatura_id ? 'pending_jefatura' : 'pending_controlling';
        $rendition->save();

        \App\Models\WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => auth()->id(),
            'action' => 'submitted_by_worker',
            'from_status' => $fromStatus,
            'to_status' => $rendition->status,
            'observation' => $request->user_observation,
            'ip_address' => $request->ip(),
        ]);
        if ($rendition->user->jefatura) {
            $rendition->user->jefatura->notify(new WorkflowNotification(
                'Nueva rendición pendiente',
                'El trabajador ' . $rendition->user->name . ' envió una rendición para revisión.',
                route('renditions.approvals')
            ));
        } else {
            $controllingUsers = User::where('departamento', WorkflowHelper::DEPARTMENT_CONTROLLING)->get();

            Notification::send($controllingUsers, new WorkflowNotification(
                'Nueva rendición pendiente',
                'El trabajador ' . $rendition->user->name . ' envió una rendición directamente a Controlling.',
                route('renditions.controlling')
            ));
        }

        return redirect()->route('renditions.index')->with('success', 'Rendición finalizada y enviada a revisión con éxito.');
    }

    public function downloadPdf(\App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        $isOwner = $rendition->user_id === $user->id;

        $isAdmin = $user->role === 'admin';

        $isJefatura = $rendition->user->jefatura_id === $user->id;

        $isFinanzas = $user->departamento === WorkflowHelper::DEPARTMENT_FINANCES;

        $isControlling = $user->departamento === WorkflowHelper::DEPARTMENT_CONTROLLING;

        if (
            !$isOwner
            &&
            !$isAdmin
            &&
            !$isJefatura
            &&
            !$isFinanzas
            &&
            !$isControlling
        ) {
            abort(403);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'renditions.pdf',
            compact('rendition')
        );

        return $pdf->download(
            'rendicion-RND-' . str_pad($rendition->id, 4, '0', STR_PAD_LEFT) . '.pdf'
        );
    }

    public function downloadAttachment(\App\Models\RenditionExpense $expense)
    {
        $user = auth()->user();

        // Cargar rendición asociada

        $rendition = $expense->rendition;

        // Permisos

        $isOwner = $rendition->user_id === $user->id;

        $isAdmin = $user->role === 'admin';

        $isJefatura = $rendition->user->jefatura_id === $user->id;

        $isFinanzas = $user->departamento === WorkflowHelper::DEPARTMENT_FINANCES;

        $isControlling = $user->departamento === WorkflowHelper::DEPARTMENT_CONTROLLING;

        if (
            !$isOwner
            &&
            !$isAdmin
            &&
            !$isJefatura
            &&
            !$isFinanzas
            &&
            !$isControlling
        ) {
            abort(403);
        }

        // Validar
        if (!Storage::disk('local')->exists($expense->attachment_path)) {
            abort(404);
        }

        // Descargar archivo

        $fullPath = Storage::disk('local')->path($expense->attachment_path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }

    public function approvals()
    {
        $plannings = \App\Models\RoutePlanning::with('user')
            ->whereHas('user', function ($query) { $query->where('jefatura_id', auth()->id()); })
            ->where('status', 'pending_jefatura')->orderBy('created_at', 'asc')->paginate(5, ['*'], 'plannings_page');

        $renditions = \App\Models\Rendition::with(['user','routePlanning','observations.user'])
            ->whereHas('user', function ($query) { $query->where('jefatura_id', auth()->id()); })
            ->where('status', 'pending_jefatura')->orderBy('updated_at', 'asc')->paginate(5, ['*'], 'renditions_page');
            
        return view('renditions.approvals', compact('plannings', 'renditions'));
    }

    public function finances()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_FINANCES) abort(403);

        $plannings = \App\Models\RoutePlanning::with('user')->where('status', 'pending_finances')->orderBy('created_at', 'asc')->paginate(5, ['*'], 'plannings_page');
        $renditions = \App\Models\Rendition::with(['user','routePlanning','observations.user'])
            ->where(function ($query) {
                $query->where('status', 'pending_finances')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('status', 'approved')
                            ->where('payment_completed', false);
                    });
            })
            ->orderBy('updated_at', 'asc')
            ->paginate(5, ['*'], 'renditions_page');
            
        return view('renditions.finances', compact('plannings', 'renditions'));
    }

    public function controlling()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING) abort(403);

        $plannings = \App\Models\RoutePlanning::with('user')->where('status', 'pending_controlling')->orderBy('created_at', 'asc')->paginate(5, ['*'], 'plannings_page');
        $renditions = \App\Models\Rendition::with(['user','routePlanning','observations.user','expenses'])
    ->withCount([
        'expenses as observed_expenses_count' => function ($query) {
            $query->where('is_valid', false);
        },
        'expenses as valid_expenses_count' => function ($query) {
            $query->where('is_valid', true);
        },
        'expenses as total_expenses_count',
    ])
    ->where('status', 'pending_controlling')
    ->orderBy('updated_at', 'asc')
    ->paginate(5, ['*'], 'renditions_page');

        return view('renditions.controlling', compact('plannings', 'renditions'));
    }

    public function approveByJefatura(\App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        // Permisos

        if (
            $user->role !== 'admin'
            &&
            $user->role !== 'jefatura'
        ) {
            abort(403);
        }

        // Estado válido

        if ($rendition->status !== 'pending_jefatura') {
            abort(403, 'La rendición no está pendiente de Jefatura.');
        }

        //Aprobar
    
        $signatureService = new DigitalSignatureService();

        $signatureService->sign(
            model: $rendition,
            user: $user,
            snapshot: [
                'rendition_id' => $rendition->id,
                'route_planning_id' => $rendition->route_planning_id,
                'worker_name' => $rendition->user->name,
                'worker_rut' => $rendition->user->rut ?? null,
                'approved_by' => $user->name,
                'approver_role' => $user->role,
                'funds_received' => $rendition->funds_received,
                'total_declared' => $rendition->total_declared,
                'expenses_count' => $rendition->expenses()->count(),
                'approved_at' => now()->toDateTimeString(),
            ],
            type: 'rendition_jefatura_signature'
        );

        $rendition->status = 'pending_controlling';
        $rendition->save();

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'approved_by_jefatura',
            'from_status' => 'pending_jefatura',
            'to_status' => 'pending_controlling',
            'observation' => 'Rendición aprobada por jefatura.',
            'ip_address' => request()->ip(),
        ]);

        $controllingUsers = User::where('departamento', WorkflowHelper::DEPARTMENT_CONTROLLING)->get();

        Notification::send($controllingUsers, new WorkflowNotification(
            'Rendición pendiente en Controlling',
            'Una rendición fue aprobada por jefatura y requiere revisión de Controlling.',
            route('renditions.controlling')
        ));

        return redirect()->back()->with(
            'success',
            'Rendición validada por jefatura.'
        );
    }

    public function rejectByJefatura(Request $request, \App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        // Permisos

        if (
            $user->role !== 'admin'
            &&
            $user->role !== 'jefatura'
        ) {
            abort(403);
        }

        // estado válido

        if ($rendition->status !== 'pending_jefatura') {
            abort(403, 'La rendición no está pendiente de Jefatura.');
        }

        // validar observación

        $request->validate([
            'observation' => 'required|string|max:1000'
        ]);

        // rechazar

        $rendition->status = 'rejected';
        $rendition->save();

        // registrar observación

        $rendition->observations()->create([
            'user_id' => $user->id,
            'observation' => $request->observation,
            'action' => 'returned'
        ]);

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'rejected_by_jefatura',
            'from_status' => 'pending_jefatura',
            'to_status' => 'rejected',
            'observation' => $request->observation,
            'ip_address' => request()->ip(),
        ]);

        $rendition->user->notify(new WorkflowNotification(
            'Rendición observada',
            'Tu rendición fue devuelta con observaciones. Revisa y corrige la información.',
            route('renditions.show', $rendition->id)
        ));

        return redirect()->back()->with(
            'success',
            'Rendición devuelta al trabajador.'
        );
    }
    
    public function approveByControlling(\App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        // permisos

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING
        ) {
            abort(403);
        }

        // estado válido
        if ($rendition->status !== 'pending_controlling') {
            abort(403, 'La rendición no está pendiente de Controlling.');
        }

        if ($rendition->expenses()->where('is_valid', false)->exists()) {
            return redirect()
                ->back()
                ->withErrors([
                    'expenses' => 'No puedes aprobar esta rendición porque existen documentos observados.'
                ]);
        }

        // aprobar

        $rendition->status = 'pending_finances';
        $rendition->save();

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'approved_by_controlling',
            'from_status' => 'pending_controlling',
            'to_status' => 'pending_finances',
            'observation' => 'Rendición aprobada por Controlling.',
            'ip_address' => request()->ip(),
        ]);

        $financeUsers = User::where('departamento', WorkflowHelper::DEPARTMENT_FINANCES)->get();

        Notification::send($financeUsers, new WorkflowNotification(
            'Rendición pendiente en Finanzas',
            'Una rendición fue aprobada por Controlling y requiere revisión final de Finanzas.',
            route('renditions.finances')
        ));

        return redirect()->back()->with(
            'success',
            'Rendición auditada y escalada a Finanzas.'
        );
    }

    public function rejectByControlling(Request $request, \App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        // Permisos

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING
        ) {
            abort(403);
        }

        // Estado válido

        if ($rendition->status !== 'pending_controlling') {
            abort(403, 'La rendición no está pendiente de Controlling.');
        }

        // Validar observación

        $request->validate([
            'observation' => 'required|string|max:1000'
        ]);

        // rechazar

        $rendition->status = 'rejected';
        $rendition->save();

        // registrar observación

        $rendition->observations()->create([
            'user_id' => $user->id,
            'observation' => $request->observation,
            'action' => 'returned'
        ]);

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'rejected_by_controlling',
            'from_status' => 'pending_controlling',
            'to_status' => 'rejected',
            'observation' => $request->observation,
            'ip_address' => request()->ip(),
        ]);

        $rendition->user->notify(new WorkflowNotification(
            'Rendición observada',
            'Tu rendición fue devuelta con observaciones. Revisa y corrige la información.',
            route('renditions.show', $rendition->id)
        ));

        return redirect()->back()->with(
            'success',
            'Rendición devuelta por Controlling.'
        );
    }
    
    public function approveByFinances(\App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        // permisos

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_FINANCES
        ) {
            abort(403);
        }

        // estado válido

        if ($rendition->status !== 'pending_finances') {
            abort(403, 'La rendición no está pendiente de Finanzas.');
        }

        // aprobar

        $totalDeclared = $rendition->expenses()->sum('amount');
        $difference = $rendition->funds_received - $totalDeclared;

        $rendition->total_declared = $totalDeclared;
        $rendition->total_approved = $totalDeclared;
        $rendition->difference = $difference;

        $rendition->refund_to_company = $difference > 0;
        $rendition->refund_to_worker = $difference < 0;
        $rendition->refund_resolved_at = now();

        $rendition->status = 'approved';
        $rendition->save();

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'approved_by_finances',
            'from_status' => 'pending_finances',
            'to_status' => 'approved',
            'observation' => $difference > 0
                ? 'Rendición aprobada por Finanzas. Queda saldo a devolver a la empresa.'
                : ($difference < 0
                    ? 'Rendición aprobada por Finanzas. Queda saldo a favor del trabajador.'
                    : 'Rendición aprobada por Finanzas. Rendición exacta.'),
            'ip_address' => request()->ip(),
        ]);

        $rendition->user->notify(new WorkflowNotification(
            'Rendición aprobada',
            'Tu rendición fue aprobada por Finanzas. El proceso finalizó correctamente.',
            route('renditions.index')
        ));

        // aprobar planificación original

        if ($rendition->routePlanning) {

            $rendition->routePlanning->status = 'approved';
            $rendition->routePlanning->save();
        }

        return redirect()->back()->with(
            'success',
            'Rendición aprobada. Proceso finalizado.'
        );
    }

    public function rejectByFinances(Request $request, \App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        // permisos

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_FINANCES
        ) {
            abort(403);
        }

        // estado válido

        if ($rendition->status !== 'pending_finances') {
            abort(403, 'La rendición no está pendiente de Finanzas.');
        }

        // validar observación

        $request->validate([
            'observation' => 'required|string|max:1000'
        ]);

        // rechazar


        $rendition->status = 'rejected';
        $rendition->save();

        // registrar observación

        $rendition->observations()->create([
            'user_id' => $user->id,
            'observation' => $request->observation,
            'action' => 'returned'
        ]);

        WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'rejected_by_finances',
            'from_status' => 'pending_finances',
            'to_status' => 'rejected',
            'observation' => $request->observation,
            'ip_address' => request()->ip(),
        ]);

        $rendition->user->notify(new WorkflowNotification(
            'Rendición observada',
            'Tu rendición fue devuelta con observaciones. Revisa y corrige la información.',
            route('renditions.show', $rendition->id)
        ));

        return redirect()->back()->with(
            'success',
            'Rendición devuelta por Finanzas.'
        );
    }

    public function markPaymentCompleted(Request $request, \App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_FINANCES
        ) {
            abort(403);
        }

        if ($rendition->status !== 'approved') {
            abort(403, 'La rendición debe estar aprobada antes de cerrar el pago o devolución.');
        }

        if ($rendition->payment_completed) {
            return redirect()
                ->back()
                ->with('success', 'Esta rendición ya tenía el pago/devolución marcado como realizado.');
        }

        $request->validate([
            'payment_observation' => 'nullable|string|max:1000',
        ]);

        $rendition->payment_completed = true;
        $rendition->payment_completed_at = now();
        $rendition->payment_completed_by = $user->id;
        $rendition->payment_observation = $request->payment_observation;
        $rendition->save();

        \App\Models\WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'payment_completed_by_finances',
            'from_status' => $rendition->status,
            'to_status' => $rendition->status,
            'observation' => $request->payment_observation ?: 'Finanzas marcó el pago/devolución como realizado.',
            'ip_address' => $request->ip(),
        ]);

        $rendition->user->notify(new WorkflowNotification(
            'Pago/devolución finalizado',
            'Finanzas marcó como realizado el cierre financiero de tu rendición.',
            route('renditions.show', $rendition->id)
        ));

        return redirect()
            ->back()
            ->with('success', 'Pago/devolución marcado como realizado correctamente.');
    }

    public function validateExpense(\App\Models\RenditionExpense $expense)
    {
        $user = auth()->user();

        $rendition = $expense->rendition;

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING
        ) {
            abort(403);
        }

        if ($rendition->status !== 'pending_controlling') {
            abort(403, 'Solo puedes validar documentos cuando la rendición está pendiente de Controlling.');
        }

        $expense->is_valid = true;
        $expense->rejection_reason = null;
        $expense->save();

        \App\Models\WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'expense_validated_by_controlling',
            'from_status' => $rendition->status,
            'to_status' => $rendition->status,
            'observation' => 'Documento validado por Controlling: ' . $expense->provider,
            'ip_address' => request()->ip(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Documento marcado como válido.');
    }

    public function invalidateExpense(Request $request, \App\Models\RenditionExpense $expense)
    {
        $user = auth()->user();

        $rendition = $expense->rendition;

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING
        ) {
            abort(403);
        }

        if ($rendition->status !== 'pending_controlling') {
            abort(403, 'Solo puedes observar documentos cuando la rendición está pendiente de Controlling.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $expense->is_valid = false;
        $expense->rejection_reason = $request->rejection_reason;
        $expense->save();

        \App\Models\WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'expense_observed_by_controlling',
            'from_status' => $rendition->status,
            'to_status' => $rendition->status,
            'observation' => 'Documento observado por Controlling: ' . $request->rejection_reason,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Documento observado correctamente.');
    }

    public function history()
    {
        $user = auth()->user();

        // Admin puede ver todo el historial

        if ($user->role === 'admin') {

            $plannings = \App\Models\RoutePlanning::with('user')
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'plannings_page');

            $renditions = \App\Models\Rendition::with(['user', 'routePlanning', 'observations.user'])
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'renditions_page');

            return view('renditions.history', compact('plannings', 'renditions'));
        }

        // Finanzas y Controlling pueden ver todo lo aprobado/rechazado

        if (in_array($user->departamento, [WorkflowHelper::DEPARTMENT_FINANCES, WorkflowHelper::DEPARTMENT_CONTROLLING])) {

            $plannings = \App\Models\RoutePlanning::with('user')
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'plannings_page');

            $renditions = \App\Models\Rendition::with(['user','routePlanning','observations.user'])
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'renditions_page');

            return view('renditions.history', compact('plannings', 'renditions'));
        }

        // Jefatura puede ver el historial de sus trabajadores

        if ($user->role === 'jefatura') {

            $plannings = \App\Models\RoutePlanning::with('user')
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('jefatura_id', $user->id);
                })
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'plannings_page');

            $renditions = \App\Models\Rendition::with(['user','routePlanning','observations.user'])
                ->whereHas('user', function ($query) use ($user) {
                    $query->where('jefatura_id', $user->id);
                })
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'renditions_page');

            return view('renditions.history', compact('plannings', 'renditions'));
        }

        // Trabajadores pueden ver su propio historial

        $plannings = \App\Models\RoutePlanning::with('user')
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'plannings_page');

        $renditions = \App\Models\Rendition::with(['user','routePlanning','observations.user'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'renditions_page');

        return view('renditions.history', compact('plannings', 'renditions'));
    }
}
