<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use Illuminate\Http\Request;

class ConductorController extends Controller
{
    public function index()
    {
        //todos los conductores de la base de datos
        $conductores = Conductor::all();


        return view('conductores.index', compact('conductores'));
    }

    public function create()
    {
        return view('conductores.create');
    }

    public function store(Request $request)
    {
        // validar datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rut' => 'nullable|string|max:12|unique:conductores,rut',
            'cargo' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'fecha_licencia' => 'required|date',
            'fotografia' => 'nullable|image|mimes:jpg,png,jpeg|max:10240', // Máximo 10MB
        ]);
        $conductor = new Conductor($request->except('fotografia'));

        // guarda foto en caso de ser subida
        if ($request->hasFile('fotografia')) {
            $rutaFoto = $request->file('fotografia')->store('conductores', 'public');
            $conductor->fotografia = $rutaFoto;
        }

        // guardar datos 
        $conductor->save();
        return redirect()->route('conductores.index')->with('success', 'Conductor creado correctamente.');
    }

    // Muestra el formulario con los datos cargados
    public function edit(Conductor $conductor)
    {
        return view('conductores.edit', compact('conductor'));
    }

    // Guarda los cambios 
    public function update(Request $request, Conductor $conductor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rut' => 'nullable|string|max:12|unique:conductores,rut,' . $conductor->id,
            'cargo' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'fecha_licencia' => 'required|date',
            'fotografia' => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
        ]);

        $data = $request->all();

        if ($request->hasFile('fotografia')) {
            $data['fotografia'] = $request->file('fotografia')->store('conductores', 'public');
        }

        $conductor->update($data);
        return redirect()->route('conductores.index')->with('success', 'Conductor actualizado.');
    }

    // Elimina al conductor
    public function destroy(Conductor $conductor)
    {
        $conductor->delete();
        return redirect()->route('conductores.index')->with('success', 'Conductor eliminado.');
    }

    public function trash()
    {
        $conductoresEliminados = Conductor::onlyTrashed()->get(); // Trae solo los borrados
        return view('conductores.trash', compact('conductoresEliminados'));
    }

    public function restore($id)
    {

        $conductor = Conductor::withTrashed()->findOrFail($id);
        $conductor->restore();

        return redirect()->route('conductores.index')->with('success', 'Conductor restaurado correctamente.');
    }
    public function forceDelete($id)
    {
        $conductor = Conductor::withTrashed()->findOrFail($id);
        $conductor->forceDelete(); // Borrado físico de la base de datos

        return redirect()->route('conductores.trash')->with('success', 'Conductor eliminado permanentemente.');
    }


}