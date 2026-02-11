<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrequentExternalPersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $people = \App\Models\FrequentExternalPerson::orderBy('name')->paginate(10);
        return view('admin.external_people.index', compact('people'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:frequent_external_persons,name',
            'rut' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        \App\Models\FrequentExternalPerson::create($validated);

        return redirect()->back()->with('success', 'Persona externa agregada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $externalPerson = \App\Models\FrequentExternalPerson::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:frequent_external_persons,name,' . $externalPerson->id,
            'rut' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $externalPerson->update($validated);

        return redirect()->back()->with('success', 'Persona externa actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $externalPerson = \App\Models\FrequentExternalPerson::findOrFail($id);
        $externalPerson->delete();
        return redirect()->back()->with('success', 'Persona externa movida a la papelera.');
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trash()
    {
        $people = \App\Models\FrequentExternalPerson::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
        return view('admin.external_people.trash', compact('people'));
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $externalPerson = \App\Models\FrequentExternalPerson::onlyTrashed()->findOrFail($id);
        $externalPerson->restore();
        return redirect()->back()->with('success', 'Persona externa restaurada correctamente.');
    }

    /**
     * Permanently remove the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $externalPerson = \App\Models\FrequentExternalPerson::onlyTrashed()->findOrFail($id);
        $externalPerson->forceDelete();
        return redirect()->back()->with('success', 'Persona externa eliminada permanentemente.');
    }

    /**
     * Empty the trash.
     */
    public function emptyTrash()
    {
        \App\Models\FrequentExternalPerson::onlyTrashed()->forceDelete();
        return redirect()->back()->with('success', 'Papelera vaciada correctamente.');
    }
}
