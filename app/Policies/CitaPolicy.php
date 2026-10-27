<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;

class CitaPolicy
{
    /** Solo los pacientes agendan citas (siempre a su propio nombre). */
    public function create(User $user): bool
    {
        return $user->esPaciente();
    }

    /** Solo las partes involucradas (paciente o médico) ven la cita. */
    public function view(User $user, Cita $cita): bool
    {
        return $user->id === $cita->patient_id || $user->id === $cita->doctor_id;
    }

    /**
     * El médico asignado puede asignar cualquier estado;
     * el paciente solo puede cancelar su propia cita.
     */
    public function cambiarEstado(User $user, Cita $cita, string $estado): bool
    {
        if ($user->id === $cita->doctor_id) {
            return true;
        }

        return $user->id === $cita->patient_id && $estado === Cita::ESTADO_CANCELADA;
    }
}
