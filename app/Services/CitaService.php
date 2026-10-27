<?php

namespace App\Services;

use App\Mail\CitaAgendadaMail;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CitaService
{
    /**
     * Registra una cita en nombre del paciente y notifica al médico.
     *
     * Estrategia ante fallos de correo: la cita se persiste PRIMERO; si el envío
     * falla se registra el error (report) y la cita se conserva, de modo que
     * un reintento del cliente no genere citas duplicadas.
     *
     * @param  array{doctor_id:int, especialidad:string, fecha_hora:string, motivo:string}  $datos
     */
    public function agendar(User $paciente, array $datos): Cita
    {
        $cita = Cita::create([
            'patient_id' => $paciente->id, // siempre el usuario autenticado
            'doctor_id' => $datos['doctor_id'],
            'especialidad' => $datos['especialidad'],
            'fecha_hora' => $datos['fecha_hora'],
            'estado' => Cita::ESTADO_PENDIENTE, // fijado por el backend
            'motivo' => $datos['motivo'],
        ]);

        $cita->load(['patient', 'doctor']);

        try {
            Mail::to($cita->doctor->email)->send(new CitaAgendadaMail($cita));
        } catch (Throwable $e) {
            report($e);
        }

        return $cita;
    }
}
