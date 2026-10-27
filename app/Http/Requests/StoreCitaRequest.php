<?php

namespace App\Http\Requests;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Cita::class) ?? false;
    }

    /**
     * patient_id y estado NO se aceptan del cliente: el paciente es el usuario
     * autenticado y el estado inicial lo fija el backend.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'doctor_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('rol', User::ROL_MEDICO),
            ],
            'especialidad' => ['required', 'string', 'max:100'],
            'fecha_hora' => ['required', 'date', 'after:now'],
            'motivo' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.exists' => 'El médico seleccionado no existe.',
            'fecha_hora.after' => 'La fecha y hora deben ser posteriores al momento actual.',
        ];
    }
}
