<?php

namespace App\Http\Controllers;

use App\Mail\NewCommentNotification;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:3'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        // Enviar notificación al autor del post
        Mail::to($post->user->email)->send(new NewCommentNotification($comment));

        return response()->json($comment, 201);
    }
}
