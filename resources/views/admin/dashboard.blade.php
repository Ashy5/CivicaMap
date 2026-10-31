@extends('layouts.app')

@section('title', 'CivicaMap | Panel administrativo')

@section('content')
<main class="container py-5">
    <h1 class="fw-bold">Panel administrativo</h1>
    <p class="text-secondary">Bienvenido, {{ auth('admin')->user()->nombre_completo }}.</p>
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @can('ver-reportes')
        <a href="{{ route('reportes.index') }}" class="btn btn-primary">
            <i class="bi bi-file-text me-2"></i>
            Consultar reportes
        </a>
    @endcan
</main>
@endsection
