<?php

namespace Tests\Feature;

use App\Mail\CitaAgendadaMail;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CitasApiTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function datosCita(User $medico, array $override = []): array
    {
        return array_merge([
            'doctor_id' => $medico->id,
            'especialidad' => 'Cardiología',
            'fecha_hora' => now()->addDays(3)->setTime(10, 30)->format('Y-m-d H:i:s'),
            'motivo' => 'Consulta de seguimiento',
        ], $override);
    }

    /** Cuenta las consultas SQL ejecutadas SOLO durante $accion (excluye la preparación de datos). */
    private function contarConsultas(callable $accion): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $accion();
        $total = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $total;
    }

    // ------------------------------------------------------------------
    // Entorno de pruebas
    // ------------------------------------------------------------------

    public function test_pruebas_usan_sqlite_en_memoria_y_correo_array(): void
    {
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        $this->assertSame('array', config('mail.default'));
    }

    public function test_claves_foraneas_se_aplican_en_sqlite(): void
    {
        $this->expectException(QueryException::class);

        Cita::factory()->create(['doctor_id' => 999999]);
    }

    // ------------------------------------------------------------------
    // 10.1 Consulta y filtrado por estado
    // ------------------------------------------------------------------

    public function test_muestra_citas_confirmadas(): void
    {
        $paciente = User::factory()->paciente()->create();
        $medico1 = User::factory()->medico()->create();
        $medico2 = User::factory()->medico()->create();

        $confirmada1 = Cita::factory()->confirmada()->create(['patient_id' => $paciente->id, 'doctor_id' => $medico1->id]);
        $confirmada2 = Cita::factory()->confirmada()->create(['patient_id' => $paciente->id, 'doctor_id' => $medico2->id]);
        $pendiente = Cita::factory()->pendiente()->create(['patient_id' => $paciente->id, 'doctor_id' => $medico1->id]);
        $cancelada = Cita::factory()->cancelada()->create(['patient_id' => $paciente->id, 'doctor_id' => $medico2->id]);

        // Cita confirmada de OTRO paciente: no debe filtrarse hacia este usuario.
        $ajena = Cita::factory()->confirmada()->create(['doctor_id' => $medico1->id]);

        Sanctum::actingAs($paciente);

        $response = $this->getJson('/api/citas?estado=confirmada');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'patient' => ['id', 'name'],
                        'doctor' => ['id', 'name'],
                        'especialidad',
                        'fecha_hora',
                        'estado',
                        'motivo',
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data');

        $estados = collect($response->json('data'))->pluck('estado')->unique()->all();
        $this->assertSame(['confirmada'], $estados);

        $ids = collect($response->json('data'))->pluck('id')->all();
        $this->assertEqualsCanonicalizing([$confirmada1->id, $confirmada2->id], $ids);
        $this->assertNotContains($pendiente->id, $ids);
        $this->assertNotContains($cancelada->id, $ids);
        $this->assertNotContains($ajena->id, $ids);

        // Relaciones incluidas y sin campos sensibles.
        $response->assertJsonPath('data.0.patient.name', $paciente->name);
        $response->assertJsonMissingPath('data.0.patient.email');
        $response->assertJsonMissingPath('data.0.patient.password');
        $response->assertJsonMissingPath('data.0.doctor.email');
        $response->assertJsonMissingPath('data.0.doctor.password');
        $response->assertJsonMissing(['email' => $paciente->email]);
    }

    public function test_estado_invalido_devuelve_422(): void
    {
        Sanctum::actingAs(User::factory()->paciente()->create());

        $this->getJson('/api/citas?estado=inexistente')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['estado']);
    }

    public function test_listado_requiere_autenticacion(): void
    {
        $this->getJson('/api/citas')->assertUnauthorized();
    }

    public function test_medico_solo_ve_sus_propias_citas(): void
    {
        $medico = User::factory()->medico()->create();
        $propia = Cita::factory()->create(['doctor_id' => $medico->id]);
        Cita::factory()->count(2)->create(); // citas de otros médicos

        Sanctum::actingAs($medico);

        $this->getJson('/api/citas')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $propia->id);
    }

    // ------------------------------------------------------------------
    // 10.2 Agendamiento, persistencia y correo
    // ------------------------------------------------------------------

    public function test_agendar_cita_y_envio_correo(): void
    {
        Mail::fake();

        $paciente = User::factory()->paciente()->create(['email' => 'paciente.prueba@example.com']);
        $medico = User::factory()->medico()->create(['email' => 'medico.prueba@example.com']);

        Sanctum::actingAs($paciente);

        $payload = $this->datosCita($medico);

        $response = $this->postJson('/api/citas', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.estado', 'pendiente')
            ->assertJsonPath('data.patient.id', $paciente->id)
            ->assertJsonPath('data.doctor.id', $medico->id);

        $this->assertDatabaseHas('citas', [
            'patient_id' => $paciente->id,
            'doctor_id' => $medico->id,
            'especialidad' => 'Cardiología',
            'estado' => 'pendiente',
            'motivo' => 'Consulta de seguimiento',
        ]);

        Mail::assertSent(CitaAgendadaMail::class, 1);
        Mail::assertSent(CitaAgendadaMail::class, function (CitaAgendadaMail $mail) use ($medico, $paciente) {
            return $mail->hasTo('medico.prueba@example.com')
                && ! $mail->hasTo($paciente->email)
                && count($mail->to) === 1
                && Cita::whereKey($mail->cita->id)->exists() // ya estaba persistida al notificar
                && $mail->cita->doctor_id === $medico->id;
        });

        // Contenido de la notificación.
        $mail = Mail::sent(CitaAgendadaMail::class)->first();
        $mail->assertSeeInHtml($paciente->name);
        $mail->assertSeeInHtml($medico->name);
        $mail->assertSeeInHtml('Cardiología');
        $mail->assertSeeInHtml('Consulta de seguimiento');
        $mail->assertSeeInHtml('Pendiente');
    }

    public function test_crear_cita_sin_autenticacion_es_rechazado(): void
    {
        Mail::fake();
        $medico = User::factory()->medico()->create();

        $this->postJson('/api/citas', $this->datosCita($medico))->assertUnauthorized();

        $this->assertDatabaseCount('citas', 0);
        Mail::assertNothingSent();
    }

    public function test_datos_invalidos_devuelven_422(): void
    {
        Mail::fake();
        Sanctum::actingAs(User::factory()->paciente()->create());

        $this->postJson('/api/citas', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['doctor_id', 'especialidad', 'fecha_hora', 'motivo']);

        $medico = User::factory()->medico()->create();
        $this->postJson('/api/citas', $this->datosCita($medico, ['fecha_hora' => now()->subDay()->toDateTimeString()]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_hora']);

        $this->assertDatabaseCount('citas', 0);
        Mail::assertNothingSent();
    }

    public function test_paciente_no_puede_suplantar_a_otro_paciente(): void
    {
        Mail::fake();
        $paciente = User::factory()->paciente()->create();
        $victima = User::factory()->paciente()->create();
        $medico = User::factory()->medico()->create();

        Sanctum::actingAs($paciente);

        $this->postJson('/api/citas', $this->datosCita($medico, [
            'patient_id' => $victima->id,
            'estado' => 'confirmada', // el cliente tampoco puede fijar el estado
        ]))->assertStatus(201)
            ->assertJsonPath('data.patient.id', $paciente->id)
            ->assertJsonPath('data.estado', 'pendiente');

        $this->assertDatabaseHas('citas', ['patient_id' => $paciente->id, 'doctor_id' => $medico->id]);
        $this->assertDatabaseMissing('citas', ['patient_id' => $victima->id]);
    }

    public function test_medico_no_puede_agendar_a_nombre_de_un_paciente(): void
    {
        Mail::fake();
        $paciente = User::factory()->paciente()->create();
        $medico = User::factory()->medico()->create();

        Sanctum::actingAs($medico);

        $this->postJson('/api/citas', $this->datosCita($medico, ['patient_id' => $paciente->id]))
            ->assertForbidden();

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_medico_inexistente_es_rechazado(): void
    {
        Mail::fake();
        Sanctum::actingAs(User::factory()->paciente()->create());

        $this->postJson('/api/citas', $this->datosCita(User::factory()->medico()->make(), ['doctor_id' => 999999]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['doctor_id']);

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_doctor_id_debe_pertenecer_a_un_usuario_con_rol_medico(): void
    {
        Sanctum::actingAs($paciente = User::factory()->paciente()->create());
        $otroPaciente = User::factory()->paciente()->create();

        $this->postJson('/api/citas', $this->datosCita($otroPaciente))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['doctor_id']);
    }

    // ------------------------------------------------------------------
    // GET /api/citas/{id} y PATCH /api/citas/{id}
    // ------------------------------------------------------------------

    public function test_muestra_una_cita_a_sus_involucrados(): void
    {
        $cita = Cita::factory()->create();

        Sanctum::actingAs($cita->patient);
        $this->getJson("/api/citas/{$cita->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $cita->id)
            ->assertJsonPath('data.doctor.id', $cita->doctor_id);

        Sanctum::actingAs($cita->doctor);
        $this->getJson("/api/citas/{$cita->id}")->assertOk();
    }

    public function test_cita_inexistente_devuelve_404_y_ajena_devuelve_403(): void
    {
        $cita = Cita::factory()->create();

        Sanctum::actingAs(User::factory()->paciente()->create());

        $this->getJson('/api/citas/999999')->assertNotFound();
        $this->getJson("/api/citas/{$cita->id}")->assertForbidden();
    }

    public function test_medico_confirma_y_paciente_solo_puede_cancelar(): void
    {
        $cita = Cita::factory()->pendiente()->create();

        Sanctum::actingAs($cita->patient);
        $this->patchJson("/api/citas/{$cita->id}", ['estado' => 'confirmada'])->assertForbidden();
        $this->assertSame('pendiente', $cita->fresh()->estado);

        Sanctum::actingAs($cita->doctor);
        $this->patchJson("/api/citas/{$cita->id}", ['estado' => 'confirmada'])
            ->assertOk()
            ->assertJsonPath('data.estado', 'confirmada');

        Sanctum::actingAs($cita->patient);
        $this->patchJson("/api/citas/{$cita->id}", ['estado' => 'cancelada'])
            ->assertOk()
            ->assertJsonPath('data.estado', 'cancelada');
    }

    public function test_actualizar_con_estado_invalido_devuelve_422(): void
    {
        $cita = Cita::factory()->create();
        Sanctum::actingAs($cita->doctor);

        $this->patchJson("/api/citas/{$cita->id}", ['estado' => 'reprogramada'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['estado']);
    }

    // ------------------------------------------------------------------
    // Login por token (Sanctum)
    // ------------------------------------------------------------------

    public function test_login_api_entrega_token_con_credenciales_validas(): void
    {
        $user = User::factory()->create(['email' => 'login@example.com']);

        $this->postJson('/api/login', ['email' => 'login@example.com', 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['data' => ['token', 'token_type']]);

        $this->postJson('/api/login', ['email' => 'login@example.com', 'password' => 'incorrecta'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ------------------------------------------------------------------
    // 10.3 Detección del problema N+1
    // ------------------------------------------------------------------

    /**
     * Estrategia: se mide, con el log de consultas, SOLO la petición GET /api/citas
     * (la preparación de datos con factories queda fuera porque el log se limpia justo
     * antes de la petición; actingAs() con Sanctum no genera consultas). Se compara un
     * conjunto pequeño (3 citas) contra uno grande (15 citas, con médicos distintos):
     * con eager loading (with(['patient','doctor'])) el número de consultas es CONSTANTE
     * (citas + pacientes + médicos = 3). Como contraste, se demuestra que el criterio
     * detecta el problema: acceder a las relaciones de forma perezosa (lazy) sí crece
     * linealmente (1 + 2N consultas).
     */
    public function test_detecta_problema_n_mas_1(): void
    {
        $paciente = User::factory()->paciente()->create();
        Sanctum::actingAs($paciente);

        // ---- Conjunto pequeño ----
        Cita::factory()->count(3)->create(['patient_id' => $paciente->id]);

        $respuestaPequena = null;
        $consultasPocas = $this->contarConsultas(function () use (&$respuestaPequena) {
            $respuestaPequena = $this->getJson('/api/citas');
        });
        $lazyPocas = $this->contarConsultas(fn () => Cita::all()->each(fn ($c) => [$c->patient->name, $c->doctor->name]));

        // ---- Conjunto grande ----
        Cita::factory()->count(12)->create(['patient_id' => $paciente->id]);

        $respuestaGrande = null;
        $consultasMuchas = $this->contarConsultas(function () use (&$respuestaGrande) {
            $respuestaGrande = $this->getJson('/api/citas');
        });
        $lazyMuchas = $this->contarConsultas(fn () => Cita::all()->each(fn ($c) => [$c->patient->name, $c->doctor->name]));

        // Las relaciones están disponibles en la respuesta JSON.
        $respuestaPequena->assertOk()->assertJsonCount(3, 'data');
        $respuestaGrande->assertOk()->assertJsonCount(15, 'data');
        foreach ($respuestaGrande->json('data') as $item) {
            $this->assertNotEmpty($item['patient']['name']);
            $this->assertNotEmpty($item['doctor']['name']);
        }

        fwrite(STDERR, sprintf(
            "\n[N+1] eager: 3 citas=%d consultas | 15 citas=%d consultas  ||  lazy (contraste): 3 citas=%d | 15 citas=%d\n",
            $consultasPocas, $consultasMuchas, $lazyPocas, $lazyMuchas,
        ));

        // Con eager loading, las consultas NO dependen del número de citas.
        $this->assertSame($consultasPocas, $consultasMuchas, 'Las consultas crecen con el número de citas: posible N+1.');
        $this->assertLessThanOrEqual(3, $consultasMuchas);

        // El criterio es válido: el acceso perezoso sí crece (valida que la prueba detecta N+1).
        $this->assertGreaterThan($lazyPocas, $lazyMuchas);
        $this->assertGreaterThan($consultasMuchas, $lazyMuchas);
    }
}
