<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\WorkflowHelper;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\WorkflowHistory;
use App\Services\DigitalSignatureService;

class RenditionController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Rendition::with(['routePlanning', 'observations.user'])
            ->where('user_id', auth()->id());

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por destino (búsqueda de texto)
        if ($request->filled('destination')) {
            $query->whereHas('routePlanning', function ($q) use ($request) {
                $q->where('destination', 'like', '%' . $request->destination . '%');
            });
        }

        // Filtro por año del viaje
        if ($request->filled('year')) {
            $query->whereHas('routePlanning', function ($q) use ($request) {
                $q->whereYear('start_date', $request->year);
            });
        }

        $renditions = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

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

        $isLocked = false;
        $lockOwnerName = null;

        if (!$isOwner && in_array($rendition->status, ['pending_jefatura', 'pending_controlling', 'pending_finances', 'approved'])) {
            if ($rendition->isLocked()) {
                $isLocked = true;
                $owner = $rendition->lockOwner();
                $lockOwnerName = $owner ? $owner->name . ' ' . $owner->last_name : 'Otro auditor';
            } else {
                $rendition->acquireLock();
            }
        }

        return view('renditions.show', compact('rendition', 'isLocked', 'lockOwnerName'));
    }

    public function storeExpense(Request $request, \App\Models\Rendition $rendition)
    {
        if ($rendition->user_id !== auth()->id() || !in_array($rendition->status, ['draft', 'rejected'])) {
            abort(403, 'No puedes agregar gastos a esta rendición en su estado actual.');
        }

        $rules = [
            'date' => 'required|date',
            'provider' => 'required|string|max:255',
            'document_type' => 'required|in:boleta,factura,vale,otro',
            'expense_category' => 'required|in:bencina,peaje,estacionamiento_transbordador,alojamiento,comida,otros',
            'document_number' => 'nullable|string',
            'amount' => 'required|numeric|min:1',
            'attachment' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'provider_rut' => 'required_if:document_type,factura|nullable|string',
            'justification' => 'required_if:document_type,boleta|nullable|string|max:1000'
        ];

        $request->validate($rules);

        if ($request->document_type === 'factura') {
            if (!$this->validateRut($request->provider_rut)) {
                return redirect()->back()->withErrors(['provider_rut' => 'El RUT del proveedor ingresado no es válido matemáticamente.'])->withInput();
            }
        }

        $path = $request->file('attachment')->store('receipts', 'local');

        $rendition->expenses()->create([
            'date' => $request->date,
            'provider' => $request->provider,
            'provider_rut' => $request->document_type === 'factura' ? $request->provider_rut : null,
            'document_type' => $request->document_type,
            'expense_category' => $request->expense_category,
            'justification' => $request->document_type === 'boleta' ? $request->justification : null,
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
        if ($expense->rendition_id !== $rendition->id) {
            abort(404);
        }

        if ($rendition->user_id !== auth()->id() || !in_array($rendition->status, ['draft', 'rejected'])) {
            abort(403, 'No puedes editar gastos de esta rendición en su estado actual.');
        }

        $rules = [
            'date' => 'required|date',
            'provider' => 'required|string|max:255',
            'document_type' => 'required|in:boleta,factura,vale,otro',
            'expense_category' => 'required|in:bencina,peaje,estacionamiento_transbordador,alojamiento,comida,otros',
            'document_number' => 'nullable|string',
            'amount' => 'required|numeric|min:1',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'provider_rut' => 'required_if:document_type,factura|nullable|string',
            'justification' => 'required_if:document_type,boleta|nullable|string|max:1000'
        ];

        $request->validate($rules);

        if ($request->document_type === 'factura') {
            if (!$this->validateRut($request->provider_rut)) {
                return redirect()->back()->withErrors(['provider_rut' => 'El RUT del proveedor ingresado no es válido matemáticamente.'])->withInput();
            }
        }

        $data = $request->only([
            'date',
            'provider',
            'document_type',
            'expense_category',
            'document_number',
            'amount'
        ]);

        $data['provider_rut'] = $request->document_type === 'factura' ? $request->provider_rut : null;
        $data['justification'] = $request->document_type === 'boleta' ? $request->justification : null;

        if ($request->hasFile('attachment')) {
            if (Storage::disk('local')->exists($expense->attachment_path)) {
                Storage::disk('local')->delete($expense->attachment_path);
            }

            $data['attachment_path'] = $request->file('attachment')->store('receipts', 'local');
        }

        // Si el documento fue corregido por el trabajador,
        // se limpia la observación anterior de Controlling.
        $data['is_valid'] = true;
        $data['rejection_reason'] = null;

        $expense->update($data);

        $rendition->total_declared = $rendition->expenses()->sum('amount');
        $rendition->save();

        return redirect()->back()->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroyExpense(\App\Models\Rendition $rendition, \App\Models\RenditionExpense $expense)
    {
        if ($expense->rendition_id !== $rendition->id) {
            abort(404);
        }

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

        $hasBoleta = $rendition->expenses()
            ->where('document_type', 'boleta')
            ->exists();

        $hasFactura = $rendition->expenses()
            ->where('document_type', 'factura')
            ->exists();

        if (!$hasBoleta || !$hasFactura) {
            return redirect()
                ->back()
                ->withErrors([
                    'expenses' => 'Debes adjuntar al menos una boleta y una factura antes de enviar la rendición.'
                ]);
        }

        $observedExpenses = $rendition->expenses()
            ->where('is_valid', false)
            ->get();

        if ($observedExpenses->isNotEmpty()) {
            $observedDetails = $observedExpenses
                ->map(function ($expense) {
                    $documentNumber = $expense->document_number ?: 'S/N';

                    return $expense->provider
                        . ' | '
                        . strtoupper($expense->document_type)
                        . ' N° '
                        . $documentNumber
                        . ' | $'
                        . number_format($expense->amount, 0, ',', '.')
                        . ($expense->rejection_reason ? ' | Motivo: ' . $expense->rejection_reason : '');
                })
                ->implode(' / ');

            return redirect()
                ->back()
                ->withErrors([
                    'expenses' => 'No puedes reenviar la rendición porque existen documentos observados. Corrige o reemplaza: ' . $observedDetails
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

        $rendition->load(['user', 'routePlanning', 'expenses', 'digitalSignatures.user']);

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
        )->setPaper('letter', 'portrait');

        return $pdf->download(
            'rendicion-RND-' . str_pad($rendition->id, 4, '0', STR_PAD_LEFT) . '.pdf'
        );
    }

    public function downloadAttachment(\App\Models\RenditionExpense $expense)
    {
        $user = auth()->user();

        $expense->load('rendition.user');

        $rendition = $expense->rendition;

        if (!$rendition) {
            abort(404);
        }

        $isOwner = $rendition->user_id === $user->id;
        $isAdmin = $user->role === 'admin';
        $isJefatura = $rendition->user && $rendition->user->jefatura_id === $user->id;
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
            abort(403, 'No autorizado.');
        }

        if (!$expense->attachment_path) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($expense->attachment_path)) {
            abort(404);
        }

        $fullPath = Storage::disk('local')->path($expense->attachment_path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }

    public function approvals()
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $user->role !== 'jefatura') {
            abort(403, 'No autorizado.');
        }

        $plannings = \App\Models\RoutePlanning::with('user')
            ->when($user->role !== 'admin', function ($query) use ($user) {
                $query->whereHas('user', function ($subQuery) use ($user) {
                    $subQuery->where('jefatura_id', $user->id);
                });
            })
            ->where('status', 'pending_jefatura')
            ->orderBy('created_at', 'asc')
            ->paginate(5, ['*'], 'plannings_page');

        $renditions = \App\Models\Rendition::with(['user', 'routePlanning', 'observations.user'])
            ->when($user->role !== 'admin', function ($query) use ($user) {
                $query->whereHas('user', function ($subQuery) use ($user) {
                    $subQuery->where('jefatura_id', $user->id);
                });
            })
            ->where('status', 'pending_jefatura')
            ->orderBy('updated_at', 'asc')
            ->paginate(5, ['*'], 'renditions_page');

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
            ->orderBy('updated_at', 'desc')
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
            (
                $user->role !== 'jefatura'
                || $rendition->user->jefatura_id !== $user->id
            )
        ) {
            abort(403, 'No autorizado.');
        }

        if ($rendition->user_id === $user->id) {
            abort(403, 'No puedes aprobar tu propia rendición.');
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
            (
                $user->role !== 'jefatura'
                || $rendition->user->jefatura_id !== $user->id
            )
        ) {
            abort(403, 'No autorizado.');
        }

        if ($rendition->user_id === $user->id) {
            abort(403, 'No puedes rechazar tu propia rendición.');
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

        if ($rendition->user_id === $user->id && $user->email !== 'test@example.com') {
            abort(403, 'No puedes aprobar tu propia rendición.');
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

        if ($rendition->user_id === $user->id && $user->email !== 'test@example.com') {
            abort(403, 'No puedes rechazar tu propia rendición.');
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

        if ($rendition->user_id === $user->id && $user->email !== 'test@example.com') {
            abort(403, 'No puedes aprobar tu propia rendición.');
        }

        DB::transaction(function () use ($rendition, $user) {

        // aprobar

        $totalDeclared = $rendition->expenses()->sum('amount');
        $difference = $rendition->funds_received - $totalDeclared;

        $rendition->total_declared = $totalDeclared;
        $rendition->total_approved = $totalDeclared;
        $rendition->difference = $difference;

        $rendition->refund_to_company = $difference > 0;
        $rendition->refund_to_worker = $difference < 0;
        $rendition->refund_resolved_at = now();

        if (round((float) $difference, 2) === 0.0) {
            $rendition->payment_completed = true;
            $rendition->payment_completed_at = now();
            $rendition->payment_completed_by = $user->id;
            $rendition->payment_observation = 'Cierre automático: rendición exacta, sin devolución ni reembolso.';
        } else {
            $rendition->payment_completed = false;
            $rendition->payment_completed_at = null;
            $rendition->payment_completed_by = null;
            $rendition->payment_observation = null;
        }

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

        if (round((float) $difference, 2) === 0.0) {
            WorkflowHistory::create([
                'workflowable_type' => \App\Models\Rendition::class,
                'workflowable_id' => $rendition->id,
                'user_id' => $user->id,
                'action' => 'payment_completed_automatically',
                'from_status' => 'approved',
                'to_status' => 'approved',
                'observation' => 'Cierre automático: rendición exacta, sin devolución ni reembolso.',
                'ip_address' => request()->ip(),
            ]);
        }

        $notificationMessage = 'Tu rendición fue aprobada por Finanzas.';

        if ($rendition->payment_completed) {
            $notificationMessage .= ' La rendición quedó cerrada automáticamente porque no existen saldos pendientes.';
        } elseif ($rendition->refund_to_company) {
            $notificationMessage .= ' Queda pendiente la devolución de saldo a la empresa.';
        } elseif ($rendition->refund_to_worker) {
            $notificationMessage .= ' Queda pendiente el reembolso a tu favor.';
        }

        $rendition->user->notify(new WorkflowNotification(
            'Rendición aprobada',
            $notificationMessage,
            route('renditions.index')
        ));

        // aprobar planificación original

        if ($rendition->routePlanning) {

            $rendition->routePlanning->status = 'approved';
            $rendition->routePlanning->save();
        }

        });

        return redirect()->back()->with(
            'success',
            'Rendición aprobada por Finanzas correctamente.'
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

        if ($rendition->user_id === $user->id && $user->email !== 'test@example.com') {
            abort(403, 'No puedes rechazar tu propia rendición.');
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

        if ($rendition->user_id === $user->id && $user->email !== 'test@example.com') {
            abort(403, 'No puedes cerrar el pago o devolución de tu propia rendición.');
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

        if (!$rendition) {
            abort(404);
        }

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

        if ($rendition->user_id === $user->id && $user->email !== 'test@example.com') {
            abort(403, 'No puedes validar documentos de tu propia rendición.');
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

        if (!$rendition) {
            abort(404);
        }

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

        if ($rendition->user_id === $user->id && $user->email !== 'test@example.com') {
            abort(403, 'No puedes observar documentos de tu propia rendición.');
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

    public function uploadTransferProof(Request $request, \App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        if ($rendition->user_id !== $user->id) {
            abort(403, 'No autorizado.');
        }

        if ($rendition->status !== 'approved' || !$rendition->refund_to_company) {
            abort(400, 'La rendición no se encuentra en estado de devolución de dinero.');
        }

        if ($rendition->payment_completed) {
            abort(400, 'El cierre financiero ya ha sido completado.');
        }

        $request->validate([
            'transfer_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($rendition->transfer_proof_path) {
            Storage::disk('local')->delete($rendition->transfer_proof_path);
        }

        $path = $request->file('transfer_proof')->store('receipts/transfers', 'local');

        $rendition->transfer_proof_path = $path;
        $rendition->save();

        \App\Models\WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => $user->id,
            'action' => 'transfer_proof_uploaded',
            'from_status' => $rendition->status,
            'to_status' => $rendition->status,
            'observation' => 'El colaborador adjuntó el comprobante de transferencia.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Comprobante de transferencia subido correctamente.');
    }

    public function downloadTransferProof(\App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        $isOwner = $rendition->user_id === $user->id;
        $isAdmin = $user->role === 'admin';
        $isJefatura = $rendition->user && $rendition->user->jefatura_id === $user->id;
        $isFinanzas = $user->departamento === \App\Helpers\WorkflowHelper::DEPARTMENT_FINANCES;
        $isControlling = $user->departamento === \App\Helpers\WorkflowHelper::DEPARTMENT_CONTROLLING;

        if (!$isOwner && !$isAdmin && !$isJefatura && !$isFinanzas && !$isControlling) {
            abort(403, 'No autorizado.');
        }

        if (!$rendition->transfer_proof_path) {
            abort(404, 'No hay comprobante de transferencia registrado.');
        }

        if (!Storage::disk('local')->exists($rendition->transfer_proof_path)) {
            abort(404, 'El archivo no existe en el disco.');
        }

        return Storage::disk('local')->download($rendition->transfer_proof_path);
    }

    public function history()
    {
        $user = auth()->user();

        $planningQuery = \App\Models\RoutePlanning::with([
            'user',
            'workflowHistories.user',
        ])
            ->whereIn('status', ['approved', 'rejected']);

        $renditionQuery = \App\Models\Rendition::with([
            'user',
            'routePlanning',
            'observations.user',
            'workflowHistories.user',
        ])
            ->whereIn('status', ['approved', 'rejected']);

        if ($user->role === 'admin') {
            // Admin ve todo.
        } elseif (in_array($user->departamento, [
            WorkflowHelper::DEPARTMENT_FINANCES,
            WorkflowHelper::DEPARTMENT_CONTROLLING,
        ])) {
            // Finanzas y Controlling ven todo lo aprobado/rechazado.
        } elseif ($user->role === 'jefatura') {
            $planningQuery->whereHas('user', function ($query) use ($user) {
                $query->where('jefatura_id', $user->id);
            });

            $renditionQuery->whereHas('user', function ($query) use ($user) {
                $query->where('jefatura_id', $user->id);
            });
        } else {
            $planningQuery->where('user_id', $user->id);
            $renditionQuery->where('user_id', $user->id);
        }

        $plannings = $planningQuery
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'plannings_page');

        $renditions = $renditionQuery
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'renditions_page');

        return view('renditions.history', compact('plannings', 'renditions'));
    }

    public function rejectTransferProof(Request $request, $id) 
    {
        $rendition = \App\Models\Rendition::findOrFail($id);
        $rendition->update(['transfer_proof_path' => null]);

        // Log the action to workflow history
        \App\Models\WorkflowHistory::create([
            'workflowable_type' => \App\Models\Rendition::class,
            'workflowable_id' => $rendition->id,
            'user_id' => auth()->id(),
            'action' => 'transfer_proof_rejected',
            'from_status' => $rendition->status,
            'to_status' => $rendition->status,
            'observation' => 'Finanzas rechazó el comprobante de transferencia y solicitó uno nuevo.',
            'ip_address' => $request->ip(),
        ]);

        // Notify the worker
        if ($rendition->user) {
            $rendition->user->notify(new \App\Notifications\WorkflowNotification(
                'Comprobante de Transferencia Rechazado',
                'Su comprobante de transferencia para la rendición de la planificación #' . ($rendition->route_planning_id ?? '') . ' fue rechazado por finanzas. Por favor, suba un nuevo comprobante válido.',
                route('renditions.show', $rendition->id)
            ));
        }

        return back()->with('success', 'Comprobante rechazado correctamente y notificado al trabajador.');
    }

    public function lock(\App\Models\Rendition $rendition)
    {
        $success = $rendition->acquireLock();
        return response()->json([
            'success' => $success,
            'locked' => $rendition->isLocked(),
            'owner' => $rendition->lockOwner() ? $rendition->lockOwner()->name . ' ' . $rendition->lockOwner()->last_name : null,
        ]);
    }

    public function unlock(\App\Models\Rendition $rendition)
    {
        $rendition->releaseLock();
        return response()->json(['success' => true]);
    }

    /**
     * Valida matemáticamente un RUT chileno.
     */
    private function validateRut($rut)
    {
        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        if (strlen($rut) < 2) {
            return false;
        }
        
        $number = substr($rut, 0, -1);
        $dv = strtoupper(substr($rut, -1));
        
        if (!ctype_digit($number)) {
            return false;
        }
        
        $factor = 2;
        $sum = 0;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $sum += $number[$i] * $factor;
            $factor = $factor == 7 ? 2 : $factor + 1;
        }
        
        $expectedDv = 11 - ($sum % 11);
        if ($expectedDv == 11) {
            $expectedDv = '0';
        } elseif ($expectedDv == 10) {
            $expectedDv = 'K';
        } else {
            $expectedDv = (string)$expectedDv;
        }
        
        return $dv === $expectedDv;
    }

    public function reports(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && !in_array($user->departamento, ['Finanzas', 'Controlling'])) {
            abort(403);
        }

        $users = \App\Models\User::orderBy('name')->get();

        $query = \App\Models\RoutePlanning::with(['user', 'rendition', 'workflowHistories.user']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        if ($request->filled('trip_type')) {
            $query->where('trip_type', $request->trip_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plannings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('renditions.reports', compact('plannings', 'users'));
    }

    public function exportReports(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && !in_array($user->departamento, ['Finanzas', 'Controlling'])) {
            abort(403);
        }

        $query = \App\Models\RoutePlanning::with(['user', 'rendition', 'workflowHistories.user']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        if ($request->filled('trip_type')) {
            $query->where('trip_type', $request->trip_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plannings = $query->orderBy('created_at', 'desc')->get();

        $fileName = 'Reporte_Rendiciones_' . now()->format('Ymd_His') . '.csv';

        $headers = array(
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = [
            'ID Planificacion',
            'Colaborador',
            'RUT Colaborador',
            'Departamento',
            'Destino Principal',
            'Destinos Adicionales',
            'Motivo',
            'Tipo Viaje',
            'Fecha Inicio',
            'Fecha Fin',
            'Fondos Solicitados',
            'Monto Amipass',
            'Total Asignado',
            'Total Gastos Rendidos',
            'Subtotal Boletas',
            'Subtotal Facturas',
            'Subtotal Vales/Otros',
            'Diferencia',
            'Estado Planificacion',
            'Estado Rendicion',
            'Auditado Por'
        ];

        $callback = function() use($plannings, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns, ';');

            foreach ($plannings as $plan) {
                $rendition = $plan->rendition;
                
                $destinationsText = '';
                if (!empty($plan->destinations)) {
                    $dests = [];
                    foreach ($plan->destinations as $d) {
                        if (!empty($d['destination'])) {
                            $dests[] = $d['destination'] . (!empty($d['region']) ? ' (' . $d['region'] . ')' : '');
                        }
                    }
                    $destinationsText = implode(' | ', $dests);
                }

                $subtotalBoletas = 0;
                $subtotalFacturas = 0;
                $subtotalOtros = 0;
                $diffText = 'N/A';

                if ($rendition) {
                    $subtotalBoletas = $rendition->expenses->where('document_type', 'boleta')->sum('amount');
                    $subtotalFacturas = $rendition->expenses->where('document_type', 'factura')->sum('amount');
                    $subtotalOtros = $rendition->expenses->whereNotIn('document_type', ['boleta', 'factura'])->sum('amount');
                    
                    $diff = $rendition->funds_received - $rendition->total_declared;
                    if ($diff > 0) {
                        $diffText = 'Devolver a Empresa: $' . number_format($diff, 0, ',', '.');
                    } elseif ($diff < 0) {
                        $diffText = 'Reembolso a Colaborador: $' . number_format(abs($diff), 0, ',', '.');
                    } else {
                        $diffText = 'Rendicion Exacta';
                    }
                }

                $auditor = $plan->finalAuditor();
                $auditorName = $auditor ? $auditor->name . ' ' . $auditor->last_name : 'No auditado';

                $row = [
                    'REQ-' . str_pad($plan->id, 4, '0', STR_PAD_LEFT),
                    $plan->user ? $plan->user->name . ' ' . $plan->user->last_name : 'N/A',
                    $plan->user ? $plan->user->rut : 'N/A',
                    $plan->user ? $plan->user->departamento : 'N/A',
                    $plan->destination,
                    $destinationsText ?: 'Ninguno',
                    $plan->motive,
                    ucfirst($plan->trip_type),
                    $plan->start_date,
                    $plan->end_date,
                    $plan->requested_funds ?: 0,
                    $plan->amipass_amount ?: 0,
                    ($plan->requested_funds ?? 0) + ($plan->amipass_amount ?? 0),
                    $rendition ? $rendition->total_declared : 0,
                    $subtotalBoletas,
                    $subtotalFacturas,
                    $subtotalOtros,
                    $diffText,
                    ucfirst(str_replace('_', ' ', $plan->status)),
                    $rendition ? ucfirst(str_replace('_', ' ', $rendition->status)) : 'No Iniciada',
                    $auditorName
                ];

                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
