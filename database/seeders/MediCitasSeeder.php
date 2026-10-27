<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Datos de demostración (ficticios). Idempotente: usa updateOrCreate con
 * claves naturales (email / paciente+médico+fecha), por lo que puede
 * ejecutarse varias veces sin duplicar registros.
 */
class MediCitasSeeder extends Seeder
{
    public function run(): void
    {
        $paciente = $this->usuario('Carlos Paciente', 'paciente@medicitas.test', User::ROL_PACIENTE);
        $otroPaciente = $this->usuario('María Gómez', 'maria.gomez@medicitas.test', User::ROL_PACIENTE);
        $cardiologa = $this->usuario('Dra. Laura Mendoza', 'laura.mendoza@medicitas.test', User::ROL_MEDICO);
        $pediatra = $this->usuario('Dr. Andrés Ruiz', 'andres.ruiz@medicitas.test', User::ROL_MEDICO);

        $this->cita($paciente, $cardiologa, 'Cardiología', '2026-11-05 10:30:00', Cita::ESTADO_CONFIRMADA, 'Consulta de seguimiento');
        $this->cita($paciente, $pediatra, 'Pediatría', '2026-11-06 09:00:00', Cita::ESTADO_PENDIENTE, 'Control de crecimiento');
        $this->cita($paciente, $cardiologa, 'Cardiología', '2026-11-12 15:45:00', Cita::ESTADO_CANCELADA, 'Revisión de presión arterial');
        $this->cita($otroPaciente, $cardiologa, 'Cardiología', '2026-11-07 11:15:00', Cita::ESTADO_CONFIRMADA, 'Dolor torácico ocasional');
        $this->cita($otroPaciente, $pediatra, 'Pediatría', '2026-11-08 08:30:00', Cita::ESTADO_PENDIENTE, 'Vacunación de refuerzo');
    }

    private function usuario(string $nombre, string $email, string $rol): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            ['name' => $nombre, 'password' => 'password', 'rol' => $rol],
        );
    }

    private function cita(User $paciente, User $medico, string $especialidad, string $fecha, string $estado, string $motivo): Cita
    {
        return Cita::updateOrCreate(
            [
                'patient_id' => $paciente->id,
                'doctor_id' => $medico->id,
                'fecha_hora' => Carbon::parse($fecha),
            ],
            ['especialidad' => $especialidad, 'estado' => $estado, 'motivo' => $motivo],
        );
    }
}
