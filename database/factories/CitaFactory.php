<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cita>
 */
class CitaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => User::factory()->paciente(),
            'doctor_id' => User::factory()->medico(),
            'especialidad' => fake()->randomElement(['Cardiología', 'Pediatría', 'Dermatología', 'Medicina General']),
            'fecha_hora' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'estado' => Cita::ESTADO_PENDIENTE,
            'motivo' => fake()->sentence(8),
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn () => ['estado' => Cita::ESTADO_PENDIENTE]);
    }

    public function confirmada(): static
    {
        return $this->state(fn () => ['estado' => Cita::ESTADO_CONFIRMADA]);
    }

    public function cancelada(): static
    {
        return $this->state(fn () => ['estado' => Cita::ESTADO_CANCELADA]);
    }
}
