<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Expone únicamente campos seguros (nunca email, password ni tokens).
 *
 * @mixin \App\Models\Cita
 */
class CitaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient' => ['id' => $this->patient->id, 'name' => $this->patient->name],
            'doctor' => ['id' => $this->doctor->id, 'name' => $this->doctor->name],
            'especialidad' => $this->especialidad,
            'fecha_hora' => $this->fecha_hora->format('Y-m-d\TH:i:s'),
            'estado' => $this->estado,
            'motivo' => $this->motivo,
        ];
    }
}
