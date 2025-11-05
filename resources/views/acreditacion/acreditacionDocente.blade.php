@extends('template')
@section('title', 'Acreditacion Pendiente')
@section('subtitle', 'Gestión de Documentos Obligatorios')

@section('content')

<!-- Estilos personalizados para el área de carga (Bootstrap no tiene border-dashed por defecto) -->
<style>
    .file-drop-area {
        border: 2px dashed #ced4da; /* Simula border-dashed */
        padding: 40px 20px;
        transition: border-color 0.15s ease-in-out;
    }
    .file-drop-area:hover {
        border-color: #0d6efd;
        cursor: pointer;
    }
    .card-hover-scale {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .card-hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(0,0,0,.15);
    }
</style>

<!-- Contenedor Principal (Bootstrap) -->
<div class="container">

    <!-- Encabezado de la Sección -->
    <div class="">
        <div class="p-3">
            <h1 class="fs-3 fw-bolder text-dark mb-2 d-flex align-items-center">
                <svg class="bi me-3 text-primary" width="32" height="32" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                    <path d="M4 8a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3A.5.5 0 0 1 4 8m5.5 0a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m-5.5 4a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m5.5 0a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
                </svg>
                Requisitos de Habilitación de Acceso
            </h1>
            <p class="text-secondary">
                Debe subir la documentación obligatoria correspondiente a su rol para habilitar la navegación completa del sistema.
            </p>
        </div>
    </div>

    <!-- Contenedor de las Tarjetas (Responsive Grid con Bootstrap Row/Col) -->
    <div class="row g-4">
        @php
            $archivosPorTipo = $acreditacion->archivos->groupBy('tipo');

            $getLatest = function ($tipo) use ($archivosPorTipo) {
                $history = $archivosPorTipo->get($tipo);
                // Si existe, lo ordenamos y tomamos el primero (el más nuevo)
                return $history ? $history->sortByDesc('created_at')->first() : null; 
            };

            $latestCL = $getLatest('carga lectiva');
            $estadoCL = $latestCL ? $latestCL->estado_archivo : 'Falta';
            $msjCL = ($estadoCL === 'Corregir') ? $latestCL->comentario : null;


            $latestHorario = $getLatest('horario');
            $estadoHorario = $latestHorario ? $latestHorario->estado_archivo : 'Falta';
            $msjHorario = ($estadoHorario === 'Corregir') ? $latestHorario->comentario : null;


            $latestResolucion = $getLatest('resolucion');
            $estadoResolucion = $latestResolucion ? $latestResolucion->estado_archivo : 'Falta';
            $msjResolucion = ($estadoResolucion === 'Corregir') ? $latestResolucion->comentario : null;

            $colorResolucion = match($estadoResolucion) {
                'Aprobado' => 'success',
                'Enviado' => 'warning',
                'Corregir' => 'danger',
                default => 'secondary',
            };
        @endphp
        <!-- TARJETA 1: CARGA LECTIVA -->
        <div class="col-lg-4">
            <div class="card shadow-lg rounded-3 h-100 card-hover-scale">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-start mb-4">
                        <div class="text-primary bg-primary bg-opacity-10 p-3 rounded-circle me-4">
                            <!-- Icono de Carga Lectiva (Gráfico) -->
                            <svg class="bi" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm6-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1zm6-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark">Carga Lectiva (C.L.)</h2>
                            <p class="text-muted mb-0">Documento oficial de distribución de horas.</p>
                        </div>
                    </div>

                    <p class="text-secondary mb-4 border-start border-4 border-info ps-3 py-1">
                        Debe estar firmado y sellado. (Formato PDF).
                    </p>
                    @if($estadoCL === 'Aprobado')
                        <div class="alert alert-success border-start border-4 border-success rounded-3 mt-4" role="alert">
                            <p class="mb-0 fw-bold">¡Documento Aprobado! ✅</p>
                            @if($latestCL->ruta ?? null)
                                <a href="{{ asset($latestHorario->ruta) }}" target="_blank" class="btn btn-sm btn-outline-success mt-2">Ver Versión Aprobada</a>
                            @endif
                        </div>
                    @elseif($estadoCL === 'Enviado')
                        <div class="mt-4 p-3 alert alert-info border-start border-4 border-info rounded-3" role="alert">
                            <p class="mb-0">Su constancia de Carga Lectiva está siendo revisada. Por favor, espere la confirmación.</p>
                        </div>
                    @elseif($estadoCL === 'Corregir')
                        <div class="alert alert-danger border-start border-4 border-danger rounded-3 mt-4" role="alert">
                            <p class="mb-1 fw-bold">⚠️ Debe Corregir la Resolución.</p>
                            <p class="mb-0">Mensaje del Revisor: <strong>{{ $msjCL }}</strong></p>
                            {{-- Si hay una ruta para ver la versión que fue corregida --}}
                            @if($latestCL->ruta ?? null)
                                <a href="{{ asset($latestHorario->ruta) }}" target="_blank" class="btn btn-sm btn-outline-danger mt-2">Ver Versión Anterior</a>
                            @endif
                            {{-- mensaje que vuelva enviar --}}
                            <p class="mb-0">Vuelve enviar</p>
                        </div>
                    @endif
                    @if($estadoCL != 'Enviado' && $estadoCL != 'Aprobado')
                        <form action="{{ route('subir.clectiva') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="ap_id" value="{{ $ap->id }}">
                        <!-- Área de Subida de Archivos Estilizada con input oculto -->
                        <div class="mb-4">
                            <label for="carga_lectiva" class="form-label fw-bold">Seleccionar Archivo (PDF Máx. 5MB)</label>
                            <div class="file-drop-area text-center bg-light rounded-3" onclick="document.getElementById('carga_lectiva').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-secondary" width="48" height="48" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v12m0 0V20a4 4 0 00-4-4h-4m4 4v12m-4-12v12m-4-12v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 32h40" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div class="d-flex justify-content-center text-sm text-muted">
                                        <label for="carga_lectiva" class="position-relative text-primary fw-bold" style="cursor: pointer;">
                                            Buscar Archivo
                                        </label>
                                        <p class="ps-1 mb-0">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs text-secondary mb-0" id="carga_lectiva_nombre">PDF, máximo 5MB</p>
                                </div>
                                <!-- Input File Real (Oculto visualmente, pero activado por el div) -->
                                <input id="carga_lectiva" name="carga_lectiva" type="file" class="d-none" onchange="document.getElementById('carga_lectiva_nombre').textContent = this.files[0] ? this.files[0].name : 'PDF, máximo 5MB';" accept=".pdf" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm mt-3">
                            <i class="bi bi-cloud-upload me-2"></i>
                            Confirmar y Subir Carga Lectiva
                        </button>
                    </form>
                    
                    @endif
                    
                </div>
            </div>
        </div>
        
        <!-- TARJETA 2: HORARIO DE CLASES -->
        <div class="col-lg-4">
            <div class="card shadow-lg rounded-3 h-100 card-hover-scale">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-start mb-4">
                        <div class="text-success bg-success bg-opacity-10 p-3 rounded-circle me-4">
                            <!-- Icono de Horario (Calendario) -->
                            <svg class="bi" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M11 6.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1h2.5V7a.5.5 0 0 1 .5-.5z"/>
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark">Horario de Clases</h2>
                            <p class="text-muted mb-0">Distribución semanal de la actividad docente.</p>
                        </div>
                    </div>

                    <p class="text-secondary mb-4 border-start border-4 border-success ps-3 py-1">
                        Asegúrese de que coincida con la Carga Lectiva. (Formato PDF).
                    </p>
                    @if($estadoHorario === 'Aprobado')
                        <div class="alert alert-success border-start border-4 border-success rounded-3 mt-4" role="alert">
                            <p class="mb-0 fw-bold">¡Documento Aprobado! ✅</p>
                            @if($latestHorario->ruta ?? null)
                                <a href="{{ asset($latestHorario->ruta) }}" target="_blank" class="btn btn-sm btn-outline-success mt-2">Ver Versión Aprobada</a>
                            @endif
                        </div>
                    @elseif($estadoHorario === 'Enviado')
                        <div class="mt-4 p-3 alert alert-success border-start border-4 border-success rounded-3" role="alert">
                            <p class="mb-0">Su constancia de Horario de Clases está siendo revisada. Por favor, espere la confirmación.</p>
                        </div>
                    @elseif($estadoHorario === 'Corregir')
                        <div class="alert alert-danger border-start border-4 border-danger rounded-3 mt-4" role="alert">
                            <p class="mb-1 fw-bold">⚠️ Debe Corregir la Resolución.</p>
                            <p class="mb-0">Mensaje del Revisor: <strong>{{ $msjHorario }}</strong></p>
                            {{-- Si hay una ruta para ver la versión que fue corregida --}}
                            @if($latestHorario->ruta_archivo ?? null)
                                <a href="{{ asset($latestHorario->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-danger mt-2">Ver Versión Anterior</a>
                            @endif
                            {{-- mensaje que vuelva enviar --}}
                            <p class="mb-0">Vuelve enviar</p>
                        </div>
                    @endif
                    @if($estadoHorario != 'Enviado' && $estadoHorario != 'Aprobado')
                    <form action="{{ route('subir.horario') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="ap_id" value="{{ $ap->id }}">
                        <!-- Área de Subida de Archivos Estilizada con input oculto -->
                        <div class="mb-4">
                            <label for="horario" class="form-label fw-bold">Seleccionar Archivo (PDF Máx. 5MB)</label>
                            <div class="file-drop-area text-center bg-light rounded-3" onclick="document.getElementById('horario').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-secondary" width="48" height="48" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v12m0 0V20a4 4 0 00-4-4h-4m4 4v12m-4-12v12m-4-12v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 32h40" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div class="d-flex justify-content-center text-sm text-muted">
                                        <label for="carga_lectiva" class="position-relative text-success fw-bold" style="cursor: pointer;">
                                            Buscar Archivo
                                        </label>
                                        <p class="ps-1 mb-0">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs text-secondary mb-0" id="horario_clases_nombre">PDF, máximo 5MB</p>
                                </div>
                                <!-- Input File Real (Oculto visualmente, pero activado por el div) -->
                                <input id="horario" name="horario" type="file" class="d-none" onchange="document.getElementById('horario_clases_nombre').textContent = this.files[0] ? this.files[0].name : 'PDF, máximo 5MB';" accept=".pdf" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm mt-3">
                            <i class="bi bi-cloud-upload me-2"></i>
                            Confirmar y Subir Horario
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- TARJETA 3: RESOLUCION DE DESINGACION DE SUPERVISOR -->
        <div class="col-lg-4">
            <div class="card shadow-lg rounded-3 h-100 card-hover-scale">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-start mb-4">
                        <div class="text-success bg-success bg-opacity-10 p-3 rounded-circle me-4">
                            <!-- Icono de Documento (Documento) -->
                            <svg class="bi" width="24" height="24" fill="#ffc107" viewBox="0 0 16 16">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
                                <path d="M8 5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1 0-1h4V5.5a.5.5 0 0 1-.5-.5zM8 8.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1 0-1h4V9a.5.5 0 0 1-.5-.5zM12 11.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1 0-1h4V12a.5.5 0 0 1-.5-.5z"/>
                            </svg>                
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark">Resolución</h2>
                            <p class="text-muted mb-0">Resolución de designacion de docente supervisor.</p>
                        </div>
                    </div>

                    <p class="text-secondary mb-4 border-start border-4 border-warning ps-3 py-1">
                        Asegúrese de que coincida con la Carga Lectiva. (Formato PDF).
                    </p>
                    @if($estadoResolucion === 'Aprobado')
                        <div class="alert alert-success border-start border-4 border-success rounded-3 mt-4" role="alert">
                            <p class="mb-0 fw-bold">¡Documento Aprobado! ✅</p>
                            @if($latestResolucion->ruta ?? null)
                                <a href="{{ asset($latestResolucion->ruta) }}" target="_blank" class="btn btn-sm btn-outline-success mt-2">Ver Versión Aprobada</a>
                            @endif
                        </div>
                    @elseif($estadoResolucion === 'Enviado')
                        <div class="mt-4 p-3 alert alert-success border-start border-4 border-success rounded-3" role="alert">
                            <p class="mb-0">Su constancia de Horario de Clases está siendo revisada. Por favor, espere la confirmación.</p>
                        </div>
                    @elseif($estadoResolucion === 'Corregir')
                        <div class="alert alert-danger border-start border-4 border-danger rounded-3 mt-4" role="alert">
                            <p class="mb-1 fw-bold">⚠️ Debe Corregir la Resolución.</p>
                            <p class="mb-0">Mensaje del Revisor: <strong>{{ $msjResolucion }}</strong></p>
                            {{-- Si hay una ruta para ver la versión que fue corregida --}}
                            @if($latestResolucion->ruta_archivo ?? null)
                                <a href="{{ asset($latestResolucion->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-outline-danger mt-2">Ver Versión Anterior</a>
                            @endif
                        </div>
                    @endif
                    @if($estadoResolucion != 'Enviado' && $estadoResolucion != 'Aprobado')
                    <form action="{{ route('subir.resolucion') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="ap_id" value="{{ $ap->id }}">
                        <!-- Área de Subida de Archivos Estilizada con input oculto -->
                        <div class="mb-4">
                            <label for="resolucion" class="form-label fw-bold">Seleccionar Archivo (PDF Máx. 5MB)</label>
                            <div class="file-drop-area text-center bg-light rounded-3" onclick="document.getElementById('resolucion').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-secondary" width="48" height="48" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v12m0 0V20a4 4 0 00-4-4h-4m4 4v12m-4-12v12m-4-12v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 32h40" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <div class="d-flex justify-content-center text-sm text-muted">
                                        <label for="carga_lectiva" class="position-relative text-warning fw-bold" style="cursor: pointer;">
                                            Buscar Archivo
                                        </label>
                                        <p class="ps-1 mb-0">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs text-secondary mb-0" id="resolucion_nombre">PDF, máximo 5MB</p>
                                </div>
                                <!-- Input File Real (Oculto visualmente, pero activado por el div) -->
                                <input id="resolucion" name="resolucion" type="file" class="d-none" onchange="document.getElementById('resolucion_nombre').textContent = this.files[0] ? this.files[0].name : 'PDF, máximo 5MB';" accept=".pdf" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg w-100 shadow-sm mt-3">
                            <i class="bi bi-cloud-upload me-2"></i>
                            Confirmar Resolución
                        </button>
                    </form>
                    @endif
                    <!-- Listado de enviados, o sea corregidos -->
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de Estado (Si aplica) -->
    @if(session('status'))
        <div class="mt-6 p-4 alert alert-success border-start border-4 border-success rounded-3" role="alert">
            <p class="mb-0">{{ session('status') }}</p>
        </div>
    @endif
</div>

@endsection
