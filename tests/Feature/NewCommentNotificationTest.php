<?php

namespace Tests\Feature;

use App\Mail\NewCommentNotification;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewCommentNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_correo_se_renderiza_con_el_cuerpo_del_comentario(): void
    {
        $comment = Comment::factory()->create(['body' => 'Gran publicación']);

        $mail = new NewCommentNotification($comment);

        $mail->assertSeeInHtml('Gran publicación');
        $mail->assertHasSubject('Nuevo comentario en tu publicación');
    }
}
