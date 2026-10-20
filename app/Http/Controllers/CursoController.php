<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use Illuminate\Support\Facades\Gate;

class CursoController extends Controller
{
    public function index() {
        return response()->json(Curso::with('profesor:id,name')->get());
    }

    public function update(Request $request, Curso $curso) {
        // Laravel busca la CursoPolicy y ejecuta 'update(user, curso)'
        Gate::authorize('update', $curso);

        $request->validate([
            'titulo' => 'required|string',
            'descripcion' => 'required|string'
        ]);
        $curso->update($request->only(['titulo', 'descripcion']));

        return response()->json([
            'message' => 'Curso actualizado con éxito',
            'curso' => $curso
        ]);
    }
}

