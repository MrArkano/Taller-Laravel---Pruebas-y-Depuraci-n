<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Nueva cita agendada</title></head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f1f5f9; padding:24px; color:#0f172a;">
    <div style="max-width:560px; margin:auto; background:#ffffff; border-radius:12px; padding:28px; border:1px solid #e2e8f0;">
        <h2 style="margin-top:0; color:#0e7490;">Nueva cita agendada</h2>
        <p>Hola <strong>{{ $cita->doctor->name }}</strong>, se ha registrado una nueva cita a su nombre:</p>
        <table cellpadding="8" style="width:100%; border-collapse:collapse;">
            <tr><td style="color:#64748b; width:140px;">Paciente</td><td><strong>{{ $cita->patient->name }}</strong></td></tr>
            <tr><td style="color:#64748b;">Médico</td><td>{{ $cita->doctor->name }}</td></tr>
            <tr><td style="color:#64748b;">Especialidad</td><td>{{ $cita->especialidad }}</td></tr>
            <tr><td style="color:#64748b;">Fecha y hora</td><td>{{ $cita->fecha_hora->format('d/m/Y H:i') }}</td></tr>
            <tr><td style="color:#64748b;">Motivo</td><td>{{ $cita->motivo }}</td></tr>
            <tr><td style="color:#64748b;">Estado inicial</td><td>{{ ucfirst($cita->estado) }}</td></tr>
        </table>
        <p style="color:#64748b; font-size:13px; margin-bottom:0;">MediCitas - Gestión de citas médicas.</p>
    </div>
</body>
</html>
