@extends('layouts.app')
@section('title', 'CivicaMap | Editar reporte')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="text-center mb-4">
                <span class="badge text-bg-primary mb-2">Editar reporte</span>
                <h1 class="fw-bold">Actualizar denuncia</h1>
                <p class="text-secondary">Modifica la información del caso y actualiza la evidencia si es necesario.</p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong>Hay datos inválidos o faltantes:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow-sm p-4 p-lg-5">
                <form action="{{ route('reportes.update', $reporte->id_reporte) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_usuario" value="{{ old('id_usuario', $reporte->id_usuario) }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="id_categoria" class="form-label fw-semibold">Categoría</label>
                            <select id="id_categoria" name="id_categoria" class="form-select @error('id_categoria') is-invalid @enderror" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach ($categorias ?? [] as $categoria)
                                    <option value="{{ $categoria->id_categoria }}" {{ old('id_categoria', $reporte->id_categoria) == $categoria->id_categoria ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_categoria')
                                <div class="invalid-feedback d-block">La categoría es obligatoria.</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="estado" class="form-label fw-semibold">Estado del reporte</label>
                            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                <option value="Recibido" {{ old('estado', $reporte->estado) == 'Recibido' ? 'selected' : '' }}>Recibido</option>
                                <option value="En revisión" {{ old('estado', $reporte->estado) == 'En revisión' ? 'selected' : '' }}>En revisión</option>
                                <option value="Atendido" {{ old('estado', $reporte->estado) == 'Atendido' ? 'selected' : '' }}>Atendido</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback d-block">El estado es obligatorio.</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                            <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="5" minlength="10" maxlength="2000" placeholder="Describe la situación de manera clara y objetiva..." required>{{ old('descripcion', $reporte->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback d-block">La descripción es obligatoria y debe tener al menos 10 caracteres.</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="latitud_aprox" class="form-label fw-semibold">Latitud aproximada</label>
                            <input id="latitud_aprox" name="latitud_aprox" type="number" step="any" min="-90" max="90" class="form-control @error('latitud_aprox') is-invalid @enderror" value="{{ old('latitud_aprox', $reporte->latitud_aprox) }}" placeholder="Ej. 20.6597" required>
                            @error('latitud_aprox')
                                <div class="invalid-feedback d-block">La latitud es obligatoria y debe estar entre -90 y 90.</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="longitud_aprox" class="form-label fw-semibold">Longitud aproximada</label>
                            <input id="longitud_aprox" name="longitud_aprox" type="number" step="any" min="-180" max="180" class="form-control @error('longitud_aprox') is-invalid @enderror" value="{{ old('longitud_aprox', $reporte->longitud_aprox) }}" placeholder="Ej. -103.3496" required>
                            @error('longitud_aprox')
                                <div class="invalid-feedback d-block">La longitud es obligatoria y debe estar entre -180 y 180.</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="fecha_reporte" class="form-label fw-semibold">Fecha y hora del reporte</label>
                            <input id="fecha_reporte" name="fecha_reporte" type="datetime-local" class="form-control @error('fecha_reporte') is-invalid @enderror" value="{{ old('fecha_reporte', $reporte->fecha_reporte?->format('Y-m-d\TH:i')) }}" required>
                            @error('fecha_reporte')
                                <div class="invalid-feedback d-block">La fecha del reporte es obligatoria.</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="anonimizado" name="anonimizado" value="1" {{ old('anonimizado', $reporte->anonimizado) ? 'checked' : '' }}>
                                <label class="form-check-label" for="anonimizado">
                                    <strong>Tratar el reporte como anónimo</strong><br>
                                    <small class="text-secondary">La interfaz no mostrará públicamente la identidad de quien reporta.</small>
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="evidencia" class="form-label fw-semibold">Evidencia (opcional)</label>
                            <input id="evidencia" name="evidencia" class="form-control @error('evidencia') is-invalid @enderror" type="file" accept="image/*">
                            @error('evidencia')
                                <div class="invalid-feedback d-block">La evidencia debe ser una imagen válida (jpg, jpeg o png).</div>
                            @enderror
                            <div class="form-text">Si no seleccionas una nueva imagen, se conserva la actual.</div>

                            @php
                                $evidenciaActual = $reporte->evidencias()->latest('fecha_subida')->first();
                                $rutaActual = $evidenciaActual && $evidenciaActual->ruta_archivo ? asset('storage/' . ltrim($evidenciaActual->ruta_archivo, '/')) : null;
                            @endphp

                            @if ($rutaActual)
                                <div class="mt-3">
                                    <p class="small text-secondary mb-2">Imagen actual:</p>
                                    <img src="{{ $rutaActual }}" alt="Evidencia actual" class="img-thumbnail shadow-sm" style="max-width:220px; max-height:220px; object-fit:cover;">
                                </div>
                            @endif
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('reportes.index') }}" class="btn btn-light">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
