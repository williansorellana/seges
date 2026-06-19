<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'rut' => ['nullable', 'string', 'max:20', Rule::unique(User::class)->ignore($this->user()->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'cargo' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:1024'], // Máx 1MB
            'license_photo' => ['nullable', 'image', 'max:5120'], // Máx 5MB (Higher for OCR)
            'license_expires_at' => ['nullable', 'date'],
            'delete_license_photo' => ['nullable', 'in:0,1'],
        ];

        if ($this->user() && $this->user()->role === 'admin') {
            $rules['departamento'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }
}
