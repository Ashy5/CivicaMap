@extends('layouts.app')
@section('title', 'CivicaMap | Inicio')
@section('content')
<section class="hero py-5 py-lg-6">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-light text-primary mb-3 px-3 py-2">Participación ciudadana · ODS 16</span>
                <h1 class="display-4 fw-bold">Reporta, consulta y participa en tu comunidad.</h1>
                <p class="lead mt-3 mb-4">CivicaMap centraliza reportes ciudadanos y los presenta de forma clara para facilitar la comunicación y la toma de decisiones.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('denuncias') }}" class="btn btn-danger btn-lg"><i class="bi bi-megaphone me-2"></i>Realizar denuncia</a>
                    <a href="{{ route('mapa') }}" class="btn btn-light btn-lg"><i class="bi bi-map me-2"></i>Explorar mapa</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-card p-4 shadow-lg">
                    <div class="d-flex align-items-center mb-3"><i class="bi bi-shield-check fs-1 me-3"></i><div><h4 class="mb-0">Tu reporte importa</h4><small>Información organizada para la comunidad</small></div></div>
                    <hr class="border-light opacity-50">
                    <div class="row g-3 text-center">
                        <div class="col-4"><strong class="fs-3 d-block">24/7</strong><small>Acceso</small></div>
                        <div class="col-4"><strong class="fs-3 d-block">100%</strong><small>Enfoque ciudadano</small></div>
                        <div class="col-4"><strong class="fs-3 d-block"><i class="bi bi-eye-slash"></i></strong><small>Privacidad</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5"><h2 class="section-title">Todo en un solo lugar</h2><p class="text-secondary">Una interfaz sencilla para consultar información y participar.</p></div>
        <div class="row g-4">
            <div class="col-md-4"><div class="card shadow-sm h-100 p-4"><div class="feature-icon bg-primary-subtle text-primary mb-3"><i class="bi bi-map"></i></div><h4>Mapa ciudadano</h4><p class="text-secondary">Visualiza reportes por zona y conoce qué situaciones se han registrado.</p><a href="{{ route('mapa') }}" class="btn btn-outline-primary mt-auto">Ver mapa</a></div></div>
            <div class="col-md-4"><div class="card shadow-sm h-100 p-4"><div class="feature-icon bg-danger-subtle text-danger mb-3"><i class="bi bi-megaphone"></i></div><h4>Denuncias</h4><p class="text-secondary">Envía información sobre situaciones de interés comunitario de manera sencilla.</p><a href="{{ route('denuncias') }}" class="btn btn-outline-danger mt-auto">Reportar</a></div></div>
            <div class="col-md-4"><div class="card shadow-sm h-100 p-4"><div class="feature-icon bg-success-subtle text-success mb-3"><i class="bi bi-buildings"></i></div><h4>Servicios</h4><p class="text-secondary">Consulta los servicios disponibles y la información relacionada con la comunidad.</p><a href="{{ route('servicios') }}" class="btn btn-outline-success mt-auto">Consultar</a></div></div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container"><div class="row align-items-center g-5"><div class="col-lg-6"><span class="badge text-bg-primary mb-2">¿Por qué CivicaMap?</span><h2 class="section-title">Tecnología para fortalecer la participación</h2><p class="text-secondary">La plataforma propone un espacio digital para registrar, organizar y consultar reportes de la comunidad, manteniendo una experiencia simple y accesible.</p><ul class="list-group list-group-flush"><li class="list-group-item px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Interfaz clara y adaptable a celulares</li><li class="list-group-item px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Información organizada por categorías</li><li class="list-group-item px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Enfoque en privacidad y participación</li></ul></div><div class="col-lg-6"><div class="card bg-dark text-white shadow p-4"><h4><i class="bi bi-bullseye me-2"></i>Objetivo</h4><p class="mb-0 text-white-50">Contribuir al ODS 16: Paz, justicia e instituciones sólidas, mediante herramientas digitales que faciliten la participación ciudadana.</p></div></div></div></div>
</section>
@endsection
