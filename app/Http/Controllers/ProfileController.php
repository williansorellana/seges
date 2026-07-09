<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Muestra el formulario de perfil del usuario.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualiza la información de perfil del usuario.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->validate([
            'license_photo' => [
                $request->user()->license_photo_path ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:10240',
            ],
            'license_expires_at' => 'required|date|after_or_equal:today',
        ], [
            'license_photo.required' => 'Debe subir una foto de su licencia de conducir.',
            'license_photo.image' => 'El archivo de licencia debe ser una imagen válida.',
            'license_photo.mimes' => 'La licencia debe estar en formato JPG, JPEG o PNG.',
            'license_expires_at.required' => 'Debe ingresar la fecha de vencimiento de su licencia.',
            'license_expires_at.after_or_equal' => 'La fecha de vencimiento de la licencia no puede estar vencida.',
        ]);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('photo')) {
            // Eliminar foto antigua si existe
            if ($request->user()->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($request->user()->profile_photo_path);
            }

            $path = $request->file('photo')->store('profile-photos', 'public');
            $request->user()->profile_photo_path = $path;
        }

        if ($request->input('delete_license_photo') === '1') {
            if ($request->user()->license_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($request->user()->license_photo_path);
                $request->user()->license_photo_path = null;
            }
        } elseif ($request->hasFile('license_photo')) {
            // Eliminar foto antigua de licencia si existe
            if ($request->user()->license_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($request->user()->license_photo_path);
            }

            $path = $request->file('license_photo')->store('license-photos', 'public');
            $request->user()->license_photo_path = $path;
        }

        if ($request->filled('license_expires_at')) {
            $request->user()->license_expires_at = $request->input('license_expires_at');
        }

        $request->user()->save();

        $redirectTo = session('url.intended', route('requests.create'));

        return Redirect::to($redirectTo)->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Elimina la cuenta del usuario.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
