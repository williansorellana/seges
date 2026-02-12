<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;
use App\Models\AssetAssignment;

class UserController extends Controller
{
    /**
     * Muestra un listado del recurso.
     */
    public function index(Request $request)
    {
        if ($request->view === 'trash') {
            $users = User::onlyTrashed()->get();
        } else {
            $users = User::all();
        }
        return view('users.index', compact('users'));
    }

    /**
     * Almacena un recurso recién creado en el almacenamiento.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,supervisor,worker,viewer'],
            'authorized_modules' => ['nullable', 'array'],
        ]);

        $authorizedModules = $request->authorized_modules ?? ['all'];

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'must_change_password' => true,
            'is_active' => true,
            'authorized_modules' => $authorizedModules,
        ]);

        event(new Registered($user));

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['sometimes', 'required', 'in:admin,supervisor,worker,viewer'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'authorized_modules' => ['nullable', 'array'],
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario movido a la papelera.');
    }

    /**
     * Restaura el recurso especificado del almacenamiento.
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('users.index', ['view' => 'trash'])->with('success', 'Usuario restaurado correctamente.');
    }

    /**
     * Elimina permanentemente el recurso especificado del almacenamiento.
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->route('users.index', ['view' => 'trash'])->with('success', 'Usuario eliminado permanentemente.');
    }

    public function assetHistory(Request $request, $id)
    {

        $recipient = User::findOrFail($id);


        $query = AssetAssignment::with(['asset', 'photos', 'creator'])
            ->where('user_id', $recipient->id);


        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $assignments = $query->orderBy('created_at', 'desc')->get();


        return view('assets.user-history', compact('recipient', 'assignments'));
    }

    public function usersHistoryIndex()
    {
        $users = \App\Models\User::all();
        $workers = \App\Models\Worker::all();

        return view('assets.users-index', compact('users', 'workers'));
    }


}
