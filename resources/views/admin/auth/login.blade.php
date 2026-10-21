@extends('layouts.app')

@section('title', 'CivicaMap | Acceso administrativo')

@section('content')
<div class="container py-5">
    <div class="auth-card">
        <div class="card shadow-sm p-4 p-lg-5">
            <div class="text-center mb-4">
                <div class="feature-icon bg-primary-subtle text-primary mx-auto mb-3">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h1 class="h2 fw-bold">Acceso administrativo</h1>
                <p class="text-secondary">Inicia sesión para acceder al panel.</p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold">Nombre de usuario</label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        class="form-control form-control-lg"
                        value="{{ old('username') }}"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Contraseña</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="form-control form-control-lg"
                        autocomplete="current-password"
                        required
                    >
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Iniciar sesión</button>
            </form>
        </div>
    </div>
</div>
@endsection
