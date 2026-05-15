<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForceChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.force-change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
            'rut' => ['required', 'string', 'max:12', 'unique:users,rut,' . $request->user()->id],
            'phone' => ['required', 'regex:/^9\d{8}$/'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
            'rut' => $request->rut,
            'phone' => $request->phone,
            'address' => $request->address,
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('status', 'Perfil completado correctamente.');
    }
}
