<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Nueva cita agendada · Medícate o Muérete</title></head>
<body style="font-family: Georgia, 'Times New Roman', serif; background:#f7f1e3; padding:24px; color:#3f2d22;">
    <div style="max-width:560px; margin:auto; background:#fdf9ef; border-radius:16px; padding:28px; border:1px solid #e0d2b4;">
        <p style="margin:0 0 4px; font-size:20px; font-weight:bold;">Medícate <em style="color:#6b7a4a; font-weight:normal;">o</em> Muérete</p>
        <p style="margin:0 0 18px; font-size:11px; letter-spacing:2px; text-transform:uppercase; color:#8b7762; font-family:Arial,Helvetica,sans-serif;">Citas médicas</p>
        <h2 style="margin-top:0; color:#6b7a4a;">Nueva cita agendada</h2>
        <p style="font-family:Arial,Helvetica,sans-serif;">Hola <strong>{{ $cita->doctor->name }}</strong>, se ha registrado una nueva cita a su nombre:</p>
        <table cellpadding="8" style="width:100%; border-collapse:collapse; font-family:Arial,Helvetica,sans-serif;">
            <tr><td style="color:#8b7762; width:140px;">Paciente</td><td><strong>{{ $cita->patient->name }}</strong></td></tr>
            <tr><td style="color:#8b7762;">Médico</td><td>{{ $cita->doctor->name }}</td></tr>
            <tr><td style="color:#8b7762;">Especialidad</td><td>{{ $cita->especialidad }}</td></tr>
            <tr><td style="color:#8b7762;">Fecha y hora</td><td>{{ $cita->fecha_hora->format('d/m/Y H:i') }}</td></tr>
            <tr><td style="color:#8b7762;">Motivo</td><td>{{ $cita->motivo }}</td></tr>
            <tr><td style="color:#8b7762;">Estado inicial</td><td>{{ ucfirst($cita->estado) }}</td></tr>
        </table>
        <p style="color:#8b7762; font-size:13px; margin-bottom:0; font-family:Arial,Helvetica,sans-serif;">Medícate o Muérete - Gestión de citas médicas.</p>
    </div>
</body>
</html>
