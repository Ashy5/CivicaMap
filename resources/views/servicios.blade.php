@extends('layouts.app')
@section('title', 'CivicaMap | Servicios')
@section('content')
<div class="container py-5">
    <div class="text-center mb-5"><span class="badge text-bg-success mb-2">Servicios</span><h1 class="fw-bold">Servicios comunitarios</h1><p class="text-secondary">Espacio preparado para mostrar servicios registrados en la plataforma.</p></div>
    <div class="row g-4">
        @foreach([
            ['bi-hospital','Atención médica','Salud','Consulta información de centros y servicios de atención.','bg-danger-subtle text-danger'],
            ['bi-building','Servicios municipales','Gobierno','Información sobre trámites y servicios comunitarios.','bg-primary-subtle text-primary'],
            ['bi-people','Mediación comunitaria','Comunidad','Orientación para la resolución pacífica de conflictos.','bg-success-subtle text-success'],
            ['bi-shield-check','Orientación ciudadana','Seguridad','Información y canales institucionales de apoyo.','bg-warning-subtle text-warning-emphasis'],
            ['bi-tree','Espacios públicos','Comunidad','Consulta información sobre parques y espacios de convivencia.','bg-success-subtle text-success'],
            ['bi-question-circle','Ayuda','Soporte','Guía rápida para utilizar las funciones de CivicaMap.','bg-secondary-subtle text-secondary']
        ] as $servicio)
        <div class="col-md-6 col-xl-4"><div class="card shadow-sm h-100 p-4"><div class="feature-icon {{ $servicio[4] }} mb-3"><i class="bi {{ $servicio[0] }}"></i></div><span class="small text-uppercase text-secondary fw-semibold">{{ $servicio[2] }}</span><h4 class="mt-1">{{ $servicio[1] }}</h4><p class="text-secondary">{{ $servicio[3] }}</p><button class="btn btn-outline-dark mt-auto">Consultar información</button></div></div>
        @endforeach
    </div>
</div>
@endsection
