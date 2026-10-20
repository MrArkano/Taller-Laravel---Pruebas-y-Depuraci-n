<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Usuario no encontrado o contraseña incorrecta'
                ], 401);
        }

        // El método createToken() está disponible gracias a HasApiTokens en el Modelo User
        $token = $user->createToken('token-edulearn')->plainTextToken;
        
        return response()->json([
            'token' => $token
        ]);
    }
}
