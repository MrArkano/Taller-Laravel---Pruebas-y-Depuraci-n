<?php

namespace Tests\Feature;

use App\Mail\NewCommentNotification;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_muestra_solo_publicaciones_publicadas(): void
    {
        Post::factory()->create(['title' => 'Post Publicado 1', 'is_published' => true]);
        Post::factory()->create(['title' => 'Post Publicado 2', 'is_published' => true]);
        Post::factory()->create(['title' => 'Post Publicado 3', 'is_published' => true]);
        Post::factory()->draft()->create(['title' => 'Post Borrador 1']);
        Post::factory()->draft()->create(['title' => 'Post Borrador 2']);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonFragment(['title' => 'Post Publicado 1'])
            ->assertJsonFragment(['title' => 'Post Publicado 2'])
            ->assertJsonFragment(['title' => 'Post Publicado 3']);
    }

    public function test_usuario_autenticado_puede_comentar_y_se_envia_correo(): void
    {
        Mail::fake();

        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $response = $this->actingAs($commenter)
            ->postJson("/api/posts/{$post->id}/comments", [
                'body' => 'Excelente artículo.',
            ]);

        // Imprime el cuerpo de la respuesta y detiene la prueba
        dump("/api/posts/{$post->id}/comments Response Content:");
        dump($response->getContent());

        $response->assertStatus(201);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'body' => 'Excelente artículo.',
        ]);

        Mail::assertSent(NewCommentNotification::class, function ($mail) use ($author) {
            return $mail->hasTo($author->email);
        });
    }

    public function test_busca_el_problema_n_mas_1(): void
    {
        // Crear 10 publicaciones asociadas a un usuario
        $user = User::factory()->create();
        Post::factory()->count(10)->create([
            'user_id' => $user->id,
            'is_published' => true,
        ]);

        // Escuchar y contar cuántas consultas SQL se ejecutan
        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            $queryCount++;
        });

        // Hacer la petición a la API
        $this->getJson('/api/posts');

        // Imprimir la evidencia directamente en la terminal de pruebas
        dump("Total de consultas SQL ejecutadas: " . $queryCount);

        // Asegurar que solo se ejecuten máximo 2 consultas (1 para posts, 1 para usuarios)
        $this->assertLessThanOrEqual(2, $queryCount, "Se detectó un problema de N+1. Consultas ejecutadas: {$queryCount}");
    }
}
