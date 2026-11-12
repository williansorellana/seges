<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use App\Models\RutCostCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CostCenterController extends Controller
{
    public function index()
    {
        return view('cost-centers.index', ['centers' => CostCenter::orderBy('area')->get(),
            'people' => User::orderBy('name')->get(),
            'assignments' => RutCostCenter::with('user', 'center')->orderBy('rut')->get()]);
    }

    public function save(Request $request)
    {
        $request->merge(['code' => strtoupper(trim((string) $request->code))]);
        $data = $request->validate(['id' => 'nullable|exists:cost_centers,id',
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('cost_centers', 'code')->ignore($request->id)],
            'area' => 'required|string|max:255', 'is_active' => 'required|boolean']);
        DB::transaction(function () use ($data) {
            $center = CostCenter::updateOrCreate(['id' => $data['id'] ?? null], collect($data)->except('id')->all());
            RutCostCenter::where('cost_center_id', $center->id)->update(['cost_center' => $center->code, 'description' => $center->area]);
        });

        return back()->with('success', 'Centro guardado. Las planificaciones históricas conservan su código original.');
    }

    public function assign(Request $request)
    {
        $data = $request->validate(['user_id' => 'required|exists:users,id',
            'cost_center_id' => 'required|exists:cost_centers,id', 'is_active' => 'required|boolean']);
        DB::transaction(function () use ($data) {
            $user = User::whereKey($data['user_id'])->lockForUpdate()->firstOrFail();
            $rut = preg_replace('/[^0-9K]/', '', strtoupper((string) $user->rut));
            if (! preg_match('/^[0-9]{7,8}[0-9K]$/', $rut)) {
                throw ValidationException::withMessages(['user_id' => 'La persona necesita un RUT completo en su perfil antes de asignar centro.']);
            }
            $center = CostCenter::findOrFail($data['cost_center_id']);
            if ($data['is_active'] && ! $center->is_active) {
                throw ValidationException::withMessages(['cost_center_id' => 'No se pueden activar asignaciones a un centro inactivo.']);
            }
            $mapping = RutCostCenter::where('user_id', $user->id)->first();
            $rutMapping = RutCostCenter::all()->first(fn ($row) => preg_replace('/[^0-9K]/', '', strtoupper($row->rut)) === $rut);
            if ($rutMapping && $rutMapping->user_id && $rutMapping->user_id !== $user->id) {
                throw ValidationException::withMessages(['user_id' => 'Este RUT ya pertenece a otra asignación. Revisa los datos del usuario.']);
            }
            if ($mapping && $rutMapping && $mapping->id !== $rutMapping->id) {
                throw ValidationException::withMessages(['user_id' => 'Existen asignaciones antiguas duplicadas para este RUT; deben revisarse antes de continuar.']);
            }
            ($mapping ?? $rutMapping ?? new RutCostCenter)->fill(['user_id' => $user->id, 'rut' => $rut,
                'cost_center_id' => $center->id, 'cost_center' => $center->code,
                'description' => $center->area, 'is_active' => $data['is_active']])->save();
        });

        return back()->with('success', 'Asignación guardada. Se aplicará a nuevas planificaciones; no modifica el historial.');
    }
}
