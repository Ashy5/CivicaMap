@extends('layouts.app')
@section('title', 'CivicaMap | Mapa')
@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><span class="badge text-bg-primary mb-2">CivicaMap</span><h1 class="fw-bold mb-1">Mapa ciudadano</h1><p class="text-secondary mb-0">Consulta de forma visual los reportes registrados.</p></div><a href="{{ route('denuncias') }}" class="btn btn-danger"><i class="bi bi-plus-lg me-1"></i>Nuevo reporte</a></div>
    <div class="row g-4">
        <div class="col-lg-9"><div class="map-placeholder shadow-sm"><div class="position-absolute top-0 start-0 p-3 z-3"><span class="badge text-bg-light shadow-sm"><i class="bi bi-info-circle me-1"></i>Vista demostrativa</span></div><span class="map-pin" style="top:27%;left:30%">📍</span><span class="map-pin" style="top:53%;left:57%">📍</span><span class="map-pin" style="top:67%;left:38%">📍</span><span class="map-pin" style="top:38%;left:74%">📍</span><div class="position-absolute bottom-0 start-0 end-0 p-3 bg-white bg-opacity-75 z-3"><small class="text-secondary">En una versión posterior, este panel puede conectarse con Leaflet/Google Maps y los registros de la base de datos.</small></div></div></div>
        <div class="col-lg-3"><div class="card shadow-sm p-4"><h5 class="fw-bold">Categorías</h5><div class="d-grid gap-2 mt-3"><button class="btn btn-outline-danger text-start"><i class="bi bi-exclamation-triangle me-2"></i>Seguridad</button><button class="btn btn-outline-warning text-start"><i class="bi bi-cone-striped me-2"></i>Infraestructura</button><button class="btn btn-outline-primary text-start"><i class="bi bi-people me-2"></i>Convivencia</button><button class="btn btn-outline-success text-start"><i class="bi bi-tree me-2"></i>Espacios públicos</button></div><hr><div class="small text-secondary"><i class="bi bi-shield-lock me-1"></i>Los reportes deben manejarse con criterios de privacidad y sin exponer datos personales innecesarios.</div></div></div>
    </div>
</div>
@endsection
