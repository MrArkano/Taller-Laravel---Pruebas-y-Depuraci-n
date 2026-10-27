@extends('layouts.medicitas')

@section('title', 'Agendar cita')

@section('content')
    <section class="card">
        <div class="head">
            <div>
                <h1>Agendar nueva cita</h1>
                <p style="color: var(--muted); margin: 4px 0 0; font-size: 0.88rem;">Complete el formulario de Medícate o Muérete</p>
            </div>
            <a href="{{ route('citas.index') }}" class="btn btn-ghost">← Volver</a>
        </div>

        <form method="POST" action="{{ route('citas.store') }}" id="form-cita" novalidate>
            @csrf

            <label>Paciente</label>
            <div class="readonly" id="paciente-actual">{{ auth()->user()->name }}</div>

            <div class="grid">
                <div>
                    <label for="doctor_id">Médico</label>
                    <select name="doctor_id" id="doctor_id" required>
                        <option value="">— Seleccione un médico —</option>
                        @foreach ($medicos as $medico)
                            <option value="{{ $medico->id }}" @selected((string) old('doctor_id') === (string) $medico->id)>{{ $medico->name }}</option>
                        @endforeach
                    </select>
                    @error('doctor_id') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="especialidad">Especialidad</label>
                    <input type="text" name="especialidad" id="especialidad" list="lista-especialidades"
                           value="{{ old('especialidad') }}" maxlength="100" required>
                    <datalist id="lista-especialidades">
                        <option value="Cardiología"><option value="Pediatría">
                        <option value="Dermatología"><option value="Medicina General">
                    </datalist>
                    @error('especialidad') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <label for="fecha_hora">Fecha y hora</label>
            <input type="datetime-local" name="fecha_hora" id="fecha_hora" value="{{ old('fecha_hora') }}" required>
            @error('fecha_hora') <div class="field-error">{{ $message }}</div> @enderror

            <label for="motivo">Motivo de la consulta</label>
            <textarea name="motivo" id="motivo" rows="4" maxlength="1000" required>{{ old('motivo') }}</textarea>
            @error('motivo') <div class="field-error">{{ $message }}</div> @enderror

            <p style="margin-top:22px">
                <button type="submit" class="btn" id="btn-guardar-cita">Registrar cita</button>
            </p>
        </form>
    </section>
@endsection
