<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePerfilRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $usuario = $this->user();
        $esAdmin = $usuario?->rol === 'administrador';

        // El admin no tiene finanzas personales (decisión #20).
        // Si alguien manipula el HTML y envía ingreso_mensual, lo borramos del
        // request para que no pase a validated().
        if ($esAdmin) {
            $this->request->remove('ingreso_mensual');
        }

        $ingreso = $this->input('ingreso_mensual');

        if ($ingreso === null || trim((string) $ingreso) === '') {
            $this->merge(['ingreso_mensual' => null]);
        } elseif (
            is_string($ingreso)
            && preg_match('/^(?:\d+|\d{1,3}(?:[.\s]\d{3})+)$/', trim($ingreso))
        ) {
            $this->merge([
                'ingreso_mensual' => str_replace(['.', ' '], '', trim($ingreso)),
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->user();

        return [
            'nombre' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($usuario->id),
                Rule::unique('users', 'pending_email')->ignore($usuario->id),
            ],

            // Solo los no-admin tienen ingreso mensual.
            'ingreso_mensual' => [
                Rule::prohibitedIf($usuario->rol === 'administrador'),
                'nullable',
                'integer',
                'min:0',
                'max:999999999999',
            ],

            'contrasena_actual' => [
                'nullable',
                Rule::requiredIf(function () use ($usuario) {
                    $cambiaPassword = !empty($this->input('nueva_contrasena'));
                    $cambiaEmail = strtolower(trim((string) $this->input('email')))
                        !== $usuario->email;
                    return $cambiaPassword || $cambiaEmail;
                }),
                'current_password',
            ],

            'nueva_contrasena' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'foto_perfil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2 MB
            ],

            'eliminar_foto' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'ingreso_mensual.prohibited' => 'El administrador no tiene ingreso mensual.',
            'ingreso_mensual.integer' => 'El ingreso mensual debe ser un número entero.',
            'ingreso_mensual.min' => 'El ingreso mensual no puede ser negativo.',

            'contrasena_actual.required' => 'Ingresa tu contraseña actual para cambiar el correo o la contraseña.',
            'contrasena_actual.current_password' => 'La contraseña actual no es correcta.',

            'nueva_contrasena.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'nueva_contrasena.confirmed' => 'La confirmación de la nueva contraseña no coincide.',

            'foto_perfil.image' => 'La foto debe ser una imagen válida.',
            'foto_perfil.mimes' => 'La foto debe ser JPG, PNG o WebP.',
            'foto_perfil.max'   => 'La foto no puede superar los 2 MB.',
        ];
    }
}
