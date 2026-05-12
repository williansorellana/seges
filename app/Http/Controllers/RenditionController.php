<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RenditionController extends Controller
{
    public function index()
    {
        $renditions = \App\Models\Rendition::with(['routePlanning'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('renditions.index', compact('renditions'));
    }

    public function show(\App\Models\Rendition $rendition)
    {
        if ($rendition->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $rendition->load('routePlanning', 'expenses', 'observations.user');

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

        $path = $request->file('attachment')->store('receipts', 'public');

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

        return redirect()->route('renditions.index')->with('success', 'Rendición finalizada y enviada a revisión con éxito.');
    }

    public function downloadPdf(\App\Models\Rendition $rendition)
    {
        if ($rendition->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $rendition->load('user', 'routePlanning.user', 'expenses', 'observations.user');
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('renditions.pdf', compact('rendition'));
        return $pdf->download('Rendicion_RND-' . str_pad($rendition->id, 4, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function approvals()
    {
        $plannings = \App\Models\RoutePlanning::with('user')
            ->whereHas('user', function ($query) { $query->where('jefatura_id', auth()->id()); })
            ->where('status', 'pending_jefatura')->orderBy('created_at', 'asc')->paginate(5, ['*'], 'plannings_page');

        $renditions = \App\Models\Rendition::with(['user', 'routePlanning'])
            ->whereHas('user', function ($query) { $query->where('jefatura_id', auth()->id()); })
            ->where('status', 'pending_jefatura')->orderBy('updated_at', 'asc')->paginate(5, ['*'], 'renditions_page');
            
        return view('renditions.approvals', compact('plannings', 'renditions'));
    }

    public function finances()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== 'Finanzas') abort(403);

        $plannings = \App\Models\RoutePlanning::with('user')->where('status', 'pending_finances')->orderBy('created_at', 'asc')->paginate(5, ['*'], 'plannings_page');
        $renditions = \App\Models\Rendition::with(['user', 'routePlanning'])->where('status', 'pending_finances')->orderBy('updated_at', 'asc')->paginate(5, ['*'], 'renditions_page');

        return view('renditions.finances', compact('plannings', 'renditions'));
    }

    public function controlling()
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->departamento !== 'Controlling') abort(403);

        $plannings = \App\Models\RoutePlanning::with('user')->where('status', 'pending_controlling')->orderBy('created_at', 'asc')->paginate(5, ['*'], 'plannings_page');
        $renditions = \App\Models\Rendition::with(['user', 'routePlanning'])->where('status', 'pending_controlling')->orderBy('updated_at', 'asc')->paginate(5, ['*'], 'renditions_page');

        return view('renditions.controlling', compact('plannings', 'renditions'));
    }

    public function approveByJefatura(\App\Models\Rendition $rendition) {
        $rendition->status = 'pending_controlling'; $rendition->save();
        return redirect()->back()->with('success', 'Rendición validada por jefatura.');
    }
    public function rejectByJefatura(Request $request, \App\Models\Rendition $rendition) {
        $rendition->status = 'rejected'; $rendition->save();
        $rendition->observations()->create(['user_id' => auth()->id(), 'observation' => $request->observation, 'action' => 'returned']);
        return redirect()->back()->with('success', 'Rendición devuelta al trabajador.');
    }
    
    public function approveByControlling(\App\Models\Rendition $rendition) {
        $rendition->status = 'pending_finances'; $rendition->save();
        return redirect()->back()->with('success', 'Rendición auditada y escalada a Finanzas.');
    }
    public function rejectByControlling(Request $request, \App\Models\Rendition $rendition) {
        $rendition->status = 'rejected'; $rendition->save();
        $rendition->observations()->create(['user_id' => auth()->id(), 'observation' => $request->observation, 'action' => 'returned']);
        return redirect()->back()->with('success', 'Rendición devuelta por Controlling.');
    }
    
    public function approveByFinances(\App\Models\Rendition $rendition) {
        $rendition->status = 'approved'; $rendition->save();
        return redirect()->back()->with('success', 'Rendición aprobada. Proceso finalizado.');
    }
    public function rejectByFinances(Request $request, \App\Models\Rendition $rendition) {
        $rendition->status = 'rejected'; $rendition->save();
        $rendition->observations()->create(['user_id' => auth()->id(), 'observation' => $request->observation, 'action' => 'returned']);
        return redirect()->back()->with('success', 'Rendición devuelta por Finanzas.');
    }

    public function history()
    {
        $queryPlannings = \App\Models\RoutePlanning::with('user')->whereIn('status', ['approved', 'rejected']);
        $queryRenditions = \App\Models\Rendition::with(['user', 'routePlanning'])->whereIn('status', ['approved', 'rejected']);

        // Si no es admin ni finanzas/controlling, asume que es jefatura viendo el historial de su equipo
        if (auth()->user()->role !== 'admin' && !in_array(auth()->user()->departamento, ['Finanzas', 'Controlling'])) {
            $queryPlannings->whereHas('user', function($q) { $q->where('jefatura_id', auth()->id()); });
            $queryRenditions->whereHas('user', function($q) { $q->where('jefatura_id', auth()->id()); });
        }

        $plannings = $queryPlannings->orderBy('updated_at', 'desc')->paginate(10, ['*'], 'plannings_page');
        $renditions = $queryRenditions->orderBy('updated_at', 'desc')->paginate(10, ['*'], 'renditions_page');

        return view('renditions.history', compact('plannings', 'renditions'));
    }
}
