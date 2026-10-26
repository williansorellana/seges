<?php

namespace App\Services;

use App\Models\RoutePlanning;
use Illuminate\Http\Request;

class RenditionReportService
{
    public function query(Request $request)
    {
        $request->validate(['user_id' => 'nullable|integer', 'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2100', 'trip_type' => 'nullable|in:terreno,reunion',
            'status' => 'nullable|in:draft,pending_jefatura,pending_controlling,pending_finances,approved,rejected',
            'date_from' => 'nullable|date_format:Y-m-d', 'date_to' => array_filter(['nullable', 'date_format:Y-m-d', $request->filled('date_from') ? 'after_or_equal:date_from' : null]),
            'project' => 'nullable|string|max:255', 'section' => 'nullable|string|max:255', 'cost_center' => 'nullable|string|max:30']);
        $query = RoutePlanning::with(['user', 'rendition.expenses', 'renditions.expenses', 'renditions.user', 'workflowHistories.user']);
        foreach (['user_id', 'trip_type', 'status', 'cost_center'] as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }
        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }
        foreach (['project', 'section'] as $column) {
            if ($request->filled($column)) {
                $query->where($column, 'like', '%'.$request->input($column).'%');
            }
        }

        return $query;
    }

    public function data(Request $request): array
    {
        $plans = $this->query($request)->orderByDesc('created_at')->get();
        $rows = [];
        foreach ($plans as $plan) {
            $attempts = $plan->renditions->sortBy('attempt_number');
            $latest = $attempts->last();
            $rejections = $attempts->sum('rejection_count');
            foreach ($attempts->isEmpty() ? [null] : $attempts as $attempt) {
                $current = ! $attempt || $attempt->id === $latest->id;
                $funds = (float) ($attempt?->funds_received ?? (($plan->requested_funds ?? 0) + ($plan->amipass_amount ?? 0)));
                $countedExpense = $current && $attempt && $attempt->status !== 'rejected' ? (float) $attempt->total_declared : 0;
                $auditor = $attempt?->finalAuditor();
                $rows[] = [
                    'plan' => $plan->id, 'rendition' => $attempt?->id, 'attempt' => $attempt?->attempt_number,
                    'replaces' => $attempt?->replaces_rendition_id, 'name' => trim(($plan->user?->name ?? '').' '.($plan->user?->last_name ?? '')),
                    'rut' => $plan->user?->rut, 'department' => $plan->user?->departamento,
                    'project' => $plan->project, 'section' => $plan->section, 'cost_center' => $plan->cost_center,
                    'destination' => $plan->destination, 'motive' => $plan->motive, 'trip_type' => $plan->trip_type,
                    'start' => $plan->start_date?->toDateString(), 'end' => $plan->end_date?->toDateString(),
                    'planning_status' => \App\Helpers\WorkflowHelper::statusLabel($plan->status),
                    'status' => \App\Helpers\WorkflowHelper::statusLabel($attempt?->status ?? 'sin_rendicion'),
                    'current' => $current ? 'Sí' : 'No', 'reference_funds' => $funds,
                    'declared' => (float) ($attempt?->total_declared ?? 0),
                    'receipts' => (float) ($attempt?->expenses->where('document_type', 'boleta')->sum('amount') ?? 0),
                    'invoices' => (float) ($attempt?->expenses->where('document_type', 'factura')->sum('amount') ?? 0),
                    'other' => (float) ($attempt?->expenses->whereNotIn('document_type', ['boleta', 'factura'])->sum('amount') ?? 0),
                    'rejection_reason' => $attempt?->rejection_reason, 'closed' => $attempt?->closed_at?->format('Y-m-d H:i'),
                    'first_submission' => $attempt?->first_submitted_at?->format('Y-m-d H:i'),
                    'counted_funds' => $current && $attempt ? $funds : 0, 'counted_expenses' => $countedExpense,
                    'rejections' => $rejections,
                    'auditor' => trim(($auditor?->name ?? '').' '.($auditor?->last_name ?? '')),
                    'requested_funds' => (float) $plan->requested_funds, 'amipass' => (float) $plan->amipass_amount,
                    'additional_destinations' => collect($plan->destinations ?? [])->map(fn ($destination) => ($destination['destination'] ?? '').(! empty($destination['region']) ? ' ('.$destination['region'].')' : ''))->implode(' / '),
                    'difference' => $funds - (float) ($attempt?->total_declared ?? 0),
                ];
            }
        }

        return ['rows' => $rows, 'totalFunds' => array_sum(array_column($rows, 'counted_funds')),
            'totalExpenses' => array_sum(array_column($rows, 'counted_expenses')),
            'filters' => $request->only(['user_id', 'month', 'year', 'trip_type', 'status', 'date_from', 'date_to', 'project', 'section', 'cost_center'])];
    }

    public function columns(): array
    {
        return ['plan' => ['Planificación', 'number'], 'rendition' => ['Rendición', 'number'], 'attempt' => ['Intento', 'number'],
            'replaces' => ['Reemplaza a rendición', 'number'], 'name' => ['Colaborador', 'text'], 'rut' => ['RUT', 'text'],
            'department' => ['Departamento', 'text'], 'project' => ['Proyecto', 'text'], 'section' => ['Sección', 'text'],
            'cost_center' => ['Centro de costo', 'text'], 'destination' => ['Destino', 'text'], 'motive' => ['Motivo viaje', 'text'],
            'trip_type' => ['Tipo de viaje', 'text'], 'start' => ['Fecha inicio', 'date'], 'end' => ['Fecha término', 'date'],
            'planning_status' => ['Estado planificación', 'text'], 'status' => ['Estado rendición', 'text'],
            'current' => ['Último intento', 'text'], 'reference_funds' => ['Fondos de referencia (no sumar)', 'money'],
            'declared' => ['Declarado por intento (no sumar)', 'money'], 'receipts' => ['Boletas del intento (no sumar)', 'money'],
            'invoices' => ['Facturas del intento (no sumar)', 'money'], 'other' => ['Otros comprobantes (no sumar)', 'money'],
            'rejection_reason' => ['Motivo rechazo', 'text'], 'closed' => ['Cierre del intento', 'text'],
            'first_submission' => ['Primer envío del intento', 'text'],
            'counted_funds' => ['Fondos sin duplicar', 'money'], 'counted_expenses' => ['Gastos vigentes sin duplicar', 'money'],
            'rejections' => ['Rechazos acumulados del viaje (no sumar)', 'number'], 'auditor' => ['Auditado por', 'text'],
            'requested_funds' => ['Fondos solicitados (referencia)', 'money'], 'amipass' => ['Amipass (referencia)', 'money'],
            'additional_destinations' => ['Destinos adicionales', 'text'], 'difference' => ['Diferencia por intento (referencia)', 'money']];
    }
}
