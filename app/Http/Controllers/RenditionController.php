<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\WorkflowHelper;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use Illuminate\Support\Facades\Notification;

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

        $rendition->load('routePlanning','expenses','observations.user');

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

    public function submitRendition(\App\Models\Rendition $rendition)
    {
        if ($rendition->user_id !== auth()->id() || !in_array($rendition->status, ['draft', 'rejected'])) {
            abort(403, 'No autorizado.');
        }

        // Envía a jefatura si tiene, si no, directo a controlling.
        $rendition->status = auth()->user()->jefatura_id ? 'pending_jefatura' : 'pending_controlling';
        $rendition->save();
        if ($rendition->user->jefatura) {
            $rendition->user->jefatura->notify(new WorkflowNotification(
            'Nueva rendición pendiente',
            'El trabajador ' . $rendition->user->name . ' envió una rendición para revisión.',
            route('renditions.approvals')
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

        /*
        |--------------------------------------------------------------------------
        | Cargar rendición asociada
        |--------------------------------------------------------------------------
        */

        $rendition = $expense->rendition;

        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Validar existencia archivo
        |--------------------------------------------------------------------------
        */

        if (!Storage::disk('local')->exists($expense->attachment_path)) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | Descargar
        |--------------------------------------------------------------------------
        */

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
        $renditions = \App\Models\Rendition::with(['user','routePlanning','observations.user'])->where('status', 'pending_finances')->orderBy('updated_at', 'asc')->paginate(5, ['*'], 'renditions_page');

        return view('renditions.finances', compact('plannings', 'renditions'));
    }

    public function controlling()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING) abort(403);

        $plannings = \App\Models\RoutePlanning::with('user')->where('status', 'pending_controlling')->orderBy('created_at', 'asc')->paginate(5, ['*'], 'plannings_page');
        $renditions = \App\Models\Rendition::with(['user','routePlanning','observations.user'])->where('status', 'pending_controlling')->orderBy('updated_at', 'asc')->paginate(5, ['*'], 'renditions_page');

        return view('renditions.controlling', compact('plannings', 'renditions'));
    }

    public function approveByJefatura(\App\Models\Rendition $rendition)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

        if (
            $user->role !== 'admin'
            &&
            $user->role !== 'jefatura'
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Estado válido
        |--------------------------------------------------------------------------
        */

        if ($rendition->status !== 'pending_jefatura') {
            abort(403, 'La rendición no está pendiente de Jefatura.');
        }

        /*
        |--------------------------------------------------------------------------
        | Aprobar
        |--------------------------------------------------------------------------
        */

        $rendition->status = 'pending_controlling';
        $rendition->save();

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

        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

        if (
            $user->role !== 'admin'
            &&
            $user->role !== 'jefatura'
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Estado válido
        |--------------------------------------------------------------------------
        */

        if ($rendition->status !== 'pending_jefatura') {
            abort(403, 'La rendición no está pendiente de Jefatura.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validar observación
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'observation' => 'required|string|max:1000'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Rechazar
        |--------------------------------------------------------------------------
        */

        $rendition->status = 'rejected';
        $rendition->save();

        /*
        |--------------------------------------------------------------------------
        | Registrar observación
        |--------------------------------------------------------------------------
        */

        $rendition->observations()->create([
            'user_id' => $user->id,
            'observation' => $request->observation,
            'action' => 'returned'
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

        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Estado válido
        |--------------------------------------------------------------------------
        */

        if ($rendition->status !== 'pending_controlling') {
            abort(403, 'La rendición no está pendiente de Controlling.');
        }

        /*
        |--------------------------------------------------------------------------
        | Aprobar
        |--------------------------------------------------------------------------
        */

        $rendition->status = 'pending_finances';
        $rendition->save();

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

        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_CONTROLLING
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Estado válido
        |--------------------------------------------------------------------------
        */

        if ($rendition->status !== 'pending_controlling') {
            abort(403, 'La rendición no está pendiente de Controlling.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validar observación
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'observation' => 'required|string|max:1000'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Rechazar
        |--------------------------------------------------------------------------
        */

        $rendition->status = 'rejected';
        $rendition->save();

        /*
        |--------------------------------------------------------------------------
        | Registrar observación
        |--------------------------------------------------------------------------
        */

        $rendition->observations()->create([
            'user_id' => $user->id,
            'observation' => $request->observation,
            'action' => 'returned'
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

        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_FINANCES
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Estado válido
        |--------------------------------------------------------------------------
        */

        if ($rendition->status !== 'pending_finances') {
            abort(403, 'La rendición no está pendiente de Finanzas.');
        }

        /*
        |--------------------------------------------------------------------------
        | Aprobar
        |--------------------------------------------------------------------------
        */

        $rendition->status = 'approved';
        $rendition->save();

        $rendition->user->notify(new WorkflowNotification(
            'Rendición aprobada',
            'Tu rendición fue aprobada por Finanzas. El proceso finalizó correctamente.',
            route('renditions.index')
        ));

        /*
        |--------------------------------------------------------------------------
        | Aprobar planificación original
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

        if (
            $user->role !== 'admin'
            &&
            $user->departamento !== WorkflowHelper::DEPARTMENT_FINANCES
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Estado válido
        |--------------------------------------------------------------------------
        */

        if ($rendition->status !== 'pending_finances') {
            abort(403, 'La rendición no está pendiente de Finanzas.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validar observación
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'observation' => 'required|string|max:1000'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Rechazar
        |--------------------------------------------------------------------------
        */

        $rendition->status = 'rejected';
        $rendition->save();

        /*
        |--------------------------------------------------------------------------
        | Registrar observación
        |--------------------------------------------------------------------------
        */

        $rendition->observations()->create([
            'user_id' => $user->id,
            'observation' => $request->observation,
            'action' => 'returned'
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

    public function history()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Finanzas / Controlling
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Jefatura
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Trabajador / Visualizador
        |--------------------------------------------------------------------------
        */

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
