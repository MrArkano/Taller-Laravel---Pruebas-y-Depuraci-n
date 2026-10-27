@extends('layouts.medicitas')

@section('title', 'Iniciar sesión')

@section('content')
    <section class="card" style="max-width:440px;margin:40px auto;">
        <h1>Iniciar sesión</h1>
        <p class="login-intro">Bienvenido a Medícate o Muérete. Ingrese para gestionar sus citas.</p>
        <form method="POST" action="{{ route('login.attempt') }}" id="form-login">
            @csrf
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="field-error">{{ $message }}</div> @enderror

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
            @error('password') <div class="field-error">{{ $message }}</div> @enderror

            <p style="margin-top:22px"><button type="submit" class="btn" id="btn-login">Entrar</button></p>
        </form>
    </section>
@endsection
