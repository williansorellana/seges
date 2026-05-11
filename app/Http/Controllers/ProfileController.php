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
        $request->user()->fill($request->validated());

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

        if ($request->hasFile('license_photo')) {
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
