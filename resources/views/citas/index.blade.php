@extends('layouts.medicitas')

@section('title', 'Citas médicas')

@section('content')
    <section class="card">
        <div class="head">
            <h1>Citas médicas</h1>
            @can('create', App\Models\Cita::class)
                <a href="{{ route('citas.create') }}" class="btn" id="btn-nueva-cita">+ Nueva cita</a>
            @endcan
        </div>

        <div class="table-wrap">
            <table id="tabla-citas">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Especialidad</th>
                        <th>Fecha y hora</th>
                        <th>Estado</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($citas as $cita)
                        <tr>
                            <td>{{ $cita->id }}</td>
                            <td>{{ $cita->patient->name }}</td>
                            <td>{{ $cita->doctor->name }}</td>
                            <td>{{ $cita->especialidad }}</td>
                            <td>{{ $cita->fecha_hora->format('d/m/Y H:i') }}</td>
                            <td><span class="badge badge-{{ $cita->estado }}">{{ $cita->estado }}</span></td>
                            <td>{{ $cita->motivo }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">No hay citas registradas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
