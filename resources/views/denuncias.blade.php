@extends('layouts.app')
@section('title', 'CivicaMap | Denuncias')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-xl-9">
        <div class="text-center mb-4"><span class="badge text-bg-danger mb-2">Nuevo reporte</span><h1 class="fw-bold">Realizar una denuncia</h1><p class="text-secondary">Completa la información del caso. Evita incluir datos personales innecesarios.</p></div>
        <div class="card shadow-sm p-4 p-lg-5">
            <div class="alert alert-primary"><i class="bi bi-info-circle me-2"></i>Esta pantalla es la interfaz del proyecto. El envío real se conectará posteriormente con el controlador y la base de datos.</div>
            <form action="#" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-semibold">Categoría</label><select class="form-select" required><option value="">Selecciona una categoría</option><option>Seguridad</option><option>Acoso</option><option>Infraestructura</option><option>Convivencia comunitaria</option><option>Otro</option></select></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Estado del reporte</label><select class="form-select"><option>Recibido</option><option>En revisión</option></select></div>
                    <div class="col-12"><label class="form-label fw-semibold">Descripción</label><textarea class="form-control" rows="5" placeholder="Describe la situación de manera clara y objetiva..." required></textarea></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Latitud aproximada</label><input type="number" step="any" class="form-control" placeholder="Ej. 20.6597"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Longitud aproximada</label><input type="number" step="any" class="form-control" placeholder="Ej. -103.3496"></div>
                    <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="anonimo" checked><label class="form-check-label" for="anonimo"><strong>Tratar el reporte como anónimo</strong><br><small class="text-secondary">La interfaz no mostrará públicamente la identidad de quien reporta.</small></label></div></div>
                    <div class="col-12"><label class="form-label fw-semibold">Evidencia (opcional)</label><input class="form-control" type="file"><div class="form-text">Agrega únicamente archivos pertinentes al reporte.</div></div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-4"><a href="{{ route('home') }}" class="btn btn-light">Cancelar</a><button type="button" class="btn btn-danger" onclick="alert('Interfaz lista. El siguiente paso es conectar este formulario con el controlador Laravel.')"><i class="bi bi-send me-2"></i>Enviar reporte</button></div>
                </div>
            </form>
        </div>
    </div></div>
</div>
@endsection
