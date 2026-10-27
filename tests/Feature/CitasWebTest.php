<?php

namespace Tests\Feature;

use App\Mail\CitaAgendadaMail;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CitasWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/citas')->assertRedirect('/login');
        $this->get('/citas/crear')->assertRedirect('/login');
    }

    public function test_listado_muestra_solo_las_citas_del_usuario_y_escapa_html(): void
    {
        $paciente = User::factory()->paciente()->create();
        $medico = User::factory()->medico()->create(['name' => 'Dra. Visible']);
        Cita::factory()->create([
            'patient_id' => $paciente->id,
            'doctor_id' => $medico->id,
            'motivo' => '<script>alert(1)</script>',
        ]);
        $ajena = Cita::factory()->create(['motivo' => 'Motivo privado ajeno']);

        $this->actingAs($paciente)
            ->get('/citas')
            ->assertOk()
            ->assertSee('Dra. Visible')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('Motivo privado ajeno')
            ->assertSee('Nueva cita');
    }

    public function test_listado_vacio_muestra_mensaje(): void
    {
        $this->actingAs(User::factory()->paciente()->create())
            ->get('/citas')
            ->assertOk()
            ->assertSee('No hay citas registradas');
    }

    public function test_paciente_agenda_desde_el_formulario(): void
    {
        Mail::fake();
        $paciente = User::factory()->paciente()->create();
        $medico = User::factory()->medico()->create();

        $this->actingAs($paciente)->get('/citas/crear')->assertOk()->assertSee($medico->name);

        $this->actingAs($paciente)->post('/citas', [
            'doctor_id' => $medico->id,
            'especialidad' => 'Pediatría',
            'fecha_hora' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'motivo' => 'Control general',
            'patient_id' => 12345, // ignorado
        ])->assertRedirect('/citas')->assertSessionHas('success');

        $this->assertDatabaseHas('citas', [
            'patient_id' => $paciente->id,
            'doctor_id' => $medico->id,
            'estado' => 'pendiente',
        ]);
        Mail::assertSent(CitaAgendadaMail::class, fn ($m) => $m->hasTo($medico->email));
    }

    public function test_formulario_invalido_conserva_datos_y_muestra_errores(): void
    {
        $paciente = User::factory()->paciente()->create();

        $this->actingAs($paciente)
            ->from('/citas/crear')
            ->post('/citas', ['especialidad' => 'Cardiología', 'motivo' => 'x'])
            ->assertRedirect('/citas/crear')
            ->assertSessionHasErrors(['doctor_id', 'fecha_hora', 'motivo'])
            ->assertSessionHasInput('especialidad', 'Cardiología');
    }

    public function test_medico_no_puede_acceder_al_formulario(): void
    {
        $this->actingAs(User::factory()->medico()->create())
            ->get('/citas/crear')
            ->assertForbidden();
    }

    public function test_login_web(): void
    {
        $user = User::factory()->create(['email' => 'web@example.com']);

        $this->post('/login', ['email' => 'web@example.com', 'password' => 'password'])
            ->assertRedirect('/citas');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();

        $this->post('/login', ['email' => 'web@example.com', 'password' => 'mala'])
            ->assertSessionHasErrors('email');
    }
}
