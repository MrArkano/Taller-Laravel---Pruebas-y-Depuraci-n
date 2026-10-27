<?php

namespace App\Http\Requests;

use App\Models\Cita;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCitaEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización por cita se verifica con CitaPolicy en el controlador.
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'estado' => ['required', 'string', Rule::in(Cita::ESTADOS)],
        ];
    }
}
