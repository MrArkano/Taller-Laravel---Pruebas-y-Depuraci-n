<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    // Endpoint que antes demostraba el problema N+1 (ya corregido)
    public function index(): JsonResponse
    {
        // CORREGIDO: eager loading con .with('user') -> 2 consultas sin importar el número de posts
        $posts = Post::with('user')->where('is_published', true)->get();

        $data = $posts->map(fn ($post) => [
            'id' => $post->id,
            'title' => $post->title,
            'author' => $post->user->name, // Ya no dispara una consulta individual por cada post
        ]);

        return response()->json($data);
    }
}
