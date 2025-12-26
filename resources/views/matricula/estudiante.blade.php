@extends('estudiante')
@section('title', 'Matricula')
@section('subtitle', 'Matricula')
@section('content')
    <div class="container">
        <div class="">
            <div class="p-3">
                <h1 class="fs-3 fw-bolder text-dark mb-2 d-flex align-items-center">
                    <svg class="bi me-3 text-primary" width="32" height="32" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                        <path d="M4 8a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3A.5.5 0 0 1 4 8m5.5 0a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m-5.5 4a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m5.5 0a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
                    </svg>
                    Requisitos para llevar las prácticas
                </h1>
                <p class="text-secondary">
                    Debe subir la documentación obligatoria correspondiente a su rol para habilitar la navegación completa del sistema.
                </p>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($matricula && $matricula->estado_matricula == 'Completo')
        <div class="alert alert-info text-center">
            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
            <h5 class="alert-heading mt-2">Matrícula Completada</h5>
            <p>La matrícula ha sido completada correctamente. El docente ya la revisó.</p>
        </div>
        @endif

        <div class="row g-4">   
            {{-- BLOQUE 1: FICHA DE MATRÍCULA --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="text-primary bg-primary bg-opacity-10 p-3 rounded-circle me-4">
                                <i class="bi bi-file-text-fill" style="font-size: 1.5rem;"></i>
                            </div>
                            <h2 class="h4 fw-bold text-dark mb-0">Ficha de matrícula</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($ficha)
                            {{-- ESTADO: APROBADO --}}
                            @if($ficha->estado_archivo == 'Aprobado')
                                <div class="bg-success bg-opacity-10 border border-success rounded p-3 text-center mb-3">
                                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                                    <h5 class="mt-2 text-success fw-bold">Aprobado</h5>
                                    <p class="mb-0 text-muted small">El documento ha sido revisado y aprobado.</p>
                                </div>
                                <div class="alert alert-light border d-flex justify-content-between align-items-center p-2">
                                    <span class="text-truncate"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Ficha Enviada</span>
                                    <a href="{{ route('documentos.show', ['path' => str_replace('storage/', '', $ficha->ruta)]) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </div>

                            {{-- ESTADO: ENVIADO (PENDIENTE) --}}
                            @elseif($ficha->estado_archivo == 'Enviado')
                                <div class="bg-info bg-opacity-10 border border-info rounded p-3 text-center mb-3">
                                    <i class="bi bi-clock-history text-info fs-1"></i>
                                    <h5 class="mt-2 text-info fw-bold">Enviado</h5>
                                    <p class="mb-0 text-muted small">Archivo enviado correctamente. Esperando revisión del docente.</p>
                                </div>
                                <div class="alert alert-light border d-flex justify-content-between align-items-center p-2">
                                    <span class="text-truncate"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Ficha Enviada</span>
                                    <a href="{{ route('documentos.show', ['path' => str_replace('storage/', '', $ficha->ruta)]) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </div>

                            {{-- ESTADO: CORREGIR --}}
                            @elseif($ficha->estado_archivo == 'Corregir')
                                <div class="bg-warning bg-opacity-10 border border-warning rounded p-3 text-center mb-3">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1"></i>
                                    <h5 class="mt-2 text-warning fw-bold">Requiere Corrección</h5>
                                    <p class="mb-0 text-muted small">{{ $ficha->comentario ?? 'El docente ha solicitado corregir este archivo.' }}</p>
                                </div>
                            @endif
                        @else
                            {{-- SI NO EXISTE ARCHIVO: Mostrar mensaje default --}}
                            <p class="text-muted mb-4">Sube tu ficha de matrícula para iniciar el proceso.</p>
                        @endif

                        {{-- FORMULARIO (Se muestra si no existe o si se debe corregir) --}}
                        @if(!$ficha || $ficha->estado_archivo == 'Corregir')
                        <form action="{{ route('subir.ficha') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="ap_id" value="{{ auth()->user()->persona->asignacion_persona->id }}">
                            <div class="mb-3">
                                <label for="ficha" class="form-label fw-bold small text-uppercase text-secondary">Seleccionar Archivo (PDF Máx. 20MB)</label>
                                <input class="form-control" type="file" id="ficha" name="ficha" accept=".pdf" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="bi bi-cloud-arrow-up me-2"></i>
                                {{ isset($ficha) ? 'Subir Corrección' : 'Subir Ficha' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- BLOQUE 2: RECORD DE NOTAS --}}
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="text-success bg-success bg-opacity-10 p-3 rounded-circle me-4">
                                <i class="bi bi-journal-bookmark-fill" style="font-size: 1.5rem;"></i>
                            </div>
                            <h2 class="h4 fw-bold text-dark mb-0">Record de notas</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($record)
                            {{-- ESTADO: APROBADO --}}
                            @if($record->estado_archivo == 'Aprobado')
                                <div class="bg-success bg-opacity-10 border border-success rounded p-3 text-center mb-3">
                                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                                    <h5 class="mt-2 text-success fw-bold">Aprobado</h5>
                                    <p class="mb-0 text-muted small">El documento ha sido revisado y aprobado.</p>
                                </div>
                                <div class="alert alert-light border d-flex justify-content-between align-items-center p-2">
                                    <span class="text-truncate"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Record Enviado</span>
                                    <a href="{{ route('documentos.show', ['path' => str_replace('storage/', '', $record->ruta)]) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </div>

                            {{-- ESTADO: ENVIADO (PENDIENTE) --}}
                            @elseif($record->estado_archivo == 'Enviado')
                                <div class="bg-info bg-opacity-10 border border-info rounded p-3 text-center mb-3">
                                    <i class="bi bi-clock-history text-info fs-1"></i>
                                    <h5 class="mt-2 text-info fw-bold">Enviado</h5>
                                    <p class="mb-0 text-muted small">Archivo enviado correctamente. Esperando revisión del docente.</p>
                                </div>
                                <div class="alert alert-light border d-flex justify-content-between align-items-center p-2">
                                    <span class="text-truncate"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Record Enviado</span>
                                    <a href="{{ route('documentos.show', ['path' => str_replace('storage/', '', $record->ruta)]) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </div>

                            {{-- ESTADO: CORREGIR --}}
                            @elseif($record->estado_archivo == 'Corregir')
                                <div class="bg-warning bg-opacity-10 border border-warning rounded p-3 text-center mb-3">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1"></i>
                                    <h5 class="mt-2 text-warning fw-bold">Requiere Corrección</h5>
                                    <p class="mb-0 text-muted small">{{ $record->comentario ?? 'El docente ha solicitado corregir este archivo.' }}</p>
                                </div>
                            @endif
                        @else
                            {{-- SI NO EXISTE ARCHIVO: Mostrar mensaje default --}}
                            <p class="text-muted mb-4">Sube tu record de notas para continuar.</p>
                        @endif

                        {{-- FORMULARIO (Se muestra si no existe o si se debe corregir) --}}
                        @if(!$record || $record->estado_archivo == 'Corregir')
                        <form action="{{ route('subir.record') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="ap_id" value="{{ auth()->user()->persona->asignacion_persona->id }}">
                            <div class="mb-3">
                                <label for="record" class="form-label fw-bold small text-uppercase text-secondary">Seleccionar Archivo (PDF Máx. 20MB)</label>
                                <input class="form-control" type="file" id="record" name="record" accept=".pdf" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="bi bi-cloud-arrow-up me-2"></i>
                                {{ isset($record) ? 'Subir Corrección' : 'Subir Record' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection