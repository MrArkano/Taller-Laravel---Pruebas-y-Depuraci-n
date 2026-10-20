<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Curso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear Administrador
        User::create([
            'name' => 'Admin Global',
            'email' => 'admin@edulearn.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Crear Profesor 1
        $profesor = User::create([
            'name' => 'Feisar Moreno',
            'email' => 'feisar@edulearn.com',
            'password' => bcrypt('password'),
            'role' => 'profesor',
        ]);

        // Crear Profesor 2
        User::create([
            'name' => 'Ana Diaz',
            'email' => 'ana@edulearn.com',
            'password' => bcrypt('password'),
            'role' => 'profesor',
        ]);

        // Crear un curso asignado al Profesor Feisar (ID: 1)
        Curso::create([
            'user_id' => $profesor->id,
            'titulo' => 'Desarrollo y Arquitectura Backend',
            'descripcion' => 'Backend con Laravel.',
        ]);
    }
}