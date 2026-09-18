@extends('layouts.app')
@section('title', 'CivicaMap | Reportes')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge text-bg-primary mb-2">Reportes</span>
            <h1 class="fw-bold mb-0">Listado de reportes</h1>
        </div>
        <a href="{{ route('denuncias') }}" class="btn btn-danger"><i class="bi bi-plus-lg me-2"></i>Nuevo reporte</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning">
            <strong>Faltan o son inválidos los siguientes datos:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm p-4">
        @if ($reportes->isEmpty())
            <p class="text-secondary mb-0">Todavía no hay reportes guardados.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Imagen</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportes as $reporte)
                            @php
                                $evidencia = $reporte->evidencias()->latest('fecha_subida')->first();
                                $rutaImagen = $evidencia && $evidencia->ruta_archivo && Storage::disk('public')->exists($evidencia->ruta_archivo)
                                    ? Storage::disk('public')->url($evidencia->ruta_archivo)
                                    : null;
                            @endphp
                            <tr>
                                <td>{{ $reporte->id_reporte }}</td>
                                <td>{{ $reporte->usuario?->nombre ?? 'No disponible' }}</td>
                                <td>{{ $reporte->categoria?->nombre ?? 'Sin categoría' }}</td>
                                <td>{{ Str::limit($reporte->descripcion, 80) }}</td>
                                <td>
                                    @if ($rutaImagen)
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalImagen{{ $reporte->id_reporte }}">
                                            <img src="{{ $rutaImagen }}" alt="Evidencia del reporte" class="img-thumbnail shadow-sm" style="width:84px;height:84px;object-fit:cover;cursor:pointer;">
                                        </a>
                                    @else
                                        <span class="text-muted small">Sin imagen</span>
                                    @endif
                                </td>
                                <td>{{ $reporte->latitud_aprox }}, {{ $reporte->longitud_aprox }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $reporte->estado }}</span>
                                </td>
                                <td>{{ $reporte->fecha_reporte ? $reporte->fecha_reporte->format('d/m/Y H:i') : 'Sin fecha' }}</td>
                            </tr>

                            @if ($rutaImagen)
                                <div class="modal fade" id="modalImagen{{ $reporte->id_reporte }}" tabindex="-1" aria-labelledby="modalImagenLabel{{ $reporte->id_reporte }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalImagenLabel{{ $reporte->id_reporte }}">Evidencia del reporte #{{ $reporte->id_reporte }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body text-center p-3">
                                                <img src="{{ $rutaImagen }}" alt="Evidencia del reporte" class="img-fluid rounded shadow-sm" style="max-height:72vh;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
