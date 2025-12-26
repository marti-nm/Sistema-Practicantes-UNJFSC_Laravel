@extends('template')
@section('title', 'Registro de Usuarios')
@section('subtitle', 'Agregar nuevos usuarios al sistema')

@push('css')
<style>
    :root {
        --primary-color: #1e3a8a;
        --primary-light: #3b82f6;
        --secondary-color: #64748b;
        --background-color: #f8fafc;
        --surface-color: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .registration-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 0;
    }

    .registration-card {
        background: var(--surface-color);
        border: 2px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        cursor: pointer;
        height: 320px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .registration-card:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .registration-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-color);
    }

    .registration-card:hover:before {
        transform: scaleX(1);
    }

    .registration-icon {
        font-size: 4rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .registration-card:hover .registration-icon {
        color: var(--primary-light);
        transform: scale(1.1);
    }

    .registration-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.025em;
    }

    .registration-subtitle {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-top: 0.5rem;
        margin-bottom: 0;
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        border-radius: 1rem 1rem 0 0;
        padding: 1.5rem 2rem;
        border-bottom: none;
    }

    .modal-title {
        font-size: 1.375rem;
        font-weight: 600;
        margin: 0;
    }

    .modal-header .close {
    background: transparent;
    border: none;
    font-size: 1.2rem;
    color: #ffffffcc;
    padding: 0.5rem 0.7rem;
    border-radius: 50%;
    transition: all 0.3s ease-in-out;
    position: absolute;
    top: 15px;
    right: 15px;
    }

    .modal-header .close:hover {
    background-color: rgba(255, 255, 255, 0.2);
    color: #fff;
    transform: rotate(90deg);
    box-shadow: 0 0 5px #ffffff88;
    }

    .modal-body {
        padding: 2rem;
        background: var(--surface-color);
    }

    .modal-footer {
        background: var(--background-color);
        border-top: 1px solid var(--border-color);
        border-radius: 0 0 1rem 1rem;
        padding: 1.5rem 2rem;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: block;
    }

    .form-control {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: var(--surface-color);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        outline: none;
    }

    .form-control:disabled {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: var(--text-secondary);
    }

    /* Button Styles */
    .btn {
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: white;
    }

    .btn-secondary {
        background: var(--secondary-color);
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
        color: white;
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background: #047857;
        color: white;
    }

    /* File Upload Styles */
    .file-upload-container {
        border: 2px dashed var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.2s ease;
        background: var(--background-color);
        position: relative;
    }

    .file-upload-container:hover {
        border-color: var(--primary-color);
        background: rgba(30, 58, 138, 0.02);
    }

    .file-upload-container::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 48px;
        height: 48px;
        opacity: 0.1;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23059669' viewBox='0 0 16 16'%3E%3Cpath d='M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z'/%3E%3Cpath d='M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        pointer-events: none;
    }

    .file-name {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-top: 0.5rem;
        font-style: italic;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .registration-container {
            padding: 1rem 0;
        }
        
        .registration-card {
            height: 280px;
            padding: 1.5rem;
        }
        
        .registration-icon {
            font-size: 3rem;
        }
        
        .registration-title {
            font-size: 1.25rem;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
    }

    /* Estilos adicionales para mejor integración */
    .modal-dialog.modal-lg {
        max-width: 900px;
    }

    /* Mejoras en formularios */
    .form-row {
        margin-bottom: 1rem;
    }

    /* Indicadores de campos obligatorios */
    .form-group label::after {
        content: '*';
        color: var(--danger-color);
        margin-left: 4px;
        font-weight: 600;
    }

    .form-group label[for="departamento"]::after,
    .form-group label[for="correo_inst"]::after {
        display: none;
    }

    /* Estados de validación */
    .form-control.is-valid {
        border-color: var(--success-color);
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .form-control.is-invalid {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    /* Mejoras en los select */
    .form-control option {
        padding: 0.5rem;
        font-weight: 500;
    }

    /* Loading states */
    .btn.loading {
        position: relative;
        color: transparent;
    }

    .btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 1rem;
        height: 1rem;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        color: white;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* ...existing styles... */
</style>
@endpush

@section('content')
<div class="registration-container">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-6 mb-4">
            <div class="registration-card" data-toggle="modal" data-target="#modalRegistro">
                <i class="bi bi-person-plus registration-icon"></i>
                <h3 class="registration-title">Añadir Usuario</h3>
                <p class="registration-subtitle">Registrar un nuevo usuario individualmente</p>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6 col-md-6 mb-4">
            <div class="registration-card" data-toggle="modal" data-target="#modalCargaMasiva">
                <i class="bi bi-people registration-icon"></i>
                <h3 class="registration-title">Carga Masiva</h3>
                <p class="registration-subtitle">Importar múltiples usuarios desde archivo CSV</p>
            </div>
        </div>
    </div>
</div>

@php
    $id_semestre = session('semestre_actual_id');
@endphp

<!--Carga_Masiva-->
<div class="modal fade" id="modalCargaMasiva" tabindex="-1" role="dialog" aria-labelledby="modalCargaMasivaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCargaMasivaLabel">
                    <i class="bi bi-people me-2"></i>
                    Carga Masiva de Usuarios
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formUsuarioMasivo" enctype="multipart/form-data" action="{{ route('usuarios.masivos.store') }}" method="POST">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    @csrf
                    @method('POST')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rol">Tipo de Usuario</label>
                                <select class="form-control" id="rolMasivo" name="rol" required>
                                    <option value="">Seleccione un tipo de usuario</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archivo" class="d-block mb-2">Archivo CSV</label>
                                <div class="file-upload-container" onclick="document.getElementById('archivo').click()">
                                    <i class="bi bi-cloud-upload me-2"></i>
                                    <span class="file-upload-text">Seleccionar Archivo</span>
                                    <div class="file-name" id="archivo-nombre">Ningún archivo seleccionado</div>
                                    <input type="file" class="d-none" id="archivo" name="archivo" accept=".csv" required 
                                        onchange="document.getElementById('archivo-nombre').textContent = this.files[0].name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Poner la imagen de model-registro del csv -->
                    <div id="model-img-registro" class="" style="display: none;">
                        <!-- info de formato de modelo de registro -->
                        <label for="model-img-registro" class="d-block mb-2 text-secondary">Formato de Modelo de Registro</label>
                        <img src="{{ asset('img/model-registro.png') }}" alt="Modelo de Registro" class="img-fluid">
                    </div>
                    
                    <!-- Sección de ASIGNACIÓN (Habilitada en ambos casos, pero con lógica) -->
                    <div id="assignmentContainer" class="section-box mt-4" style="display: none;">
                        <h6 class="mb-3 text-secondary"><i class="bi bi-clipboard-check-fill me-1"></i> 3. Asignación y Rol</h6>
                        @if(Auth::user()->hasAnyRoles([3]))
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="facultad" class="form-label">Facultad</label>
                                    <select class="form-control" id="facultad" name="facultad" required>
                                        <option value="{{ $ap->seccion_academica->facultad->id }}">{{ $ap->seccion_academica->facultad->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="escuela" class="form-label">Escuela</label>
                                    <select class="form-control" id="escuela" name="escuela" required>
                                        <option value="{{ $ap->seccion_academica->escuela->id }}">{{ $ap->seccion_academica->escuela->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="seccion" class="form-label">Sección</label>
                                    <select class="form-control" id="seccion" name="seccion" required>
                                        <option value="{{ $ap->seccion_academica->id }}">{{ $ap->seccion_academica->seccion }}</option>
                                    </select>
                                </div>
                            </div>
                        @else
                        <div class="row g-3">
                            <!-- Facultad -->
                            <div class="col-md-4">
                                <label for="facultad" class="form-label">Facultad</label>
                                <select class="form-control" id="facultad_masivo" name="facultad" required>
                                    <option value="">Seleccione una facultad</option>
                                    @foreach($facultades as $facultad)
                                        @foreach($facultades as $fac)
                                            <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <!-- Escuela -->
                            <div class="col-md-4">
                                <label for="escuela" class="form-label">Escuela</label>
                                <select class="form-control" id="escuela_masivo" name="escuela" required disabled>
                                    <option value="">Seleccione una escuela</option>
                                </select>
                            </div>
                            <!-- Seccion -->
                            <div class="col-md-4">
                                <label for="seccion" class="form-label">Sección</label>
                                <select class="form-control" id="seccion_masivo" name="seccion" disabled>
                                    <option value="">Seleccione una sección</option>
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Cerrar
                </button>
                <button type="submit" form="formUsuarioMasivo" class="btn btn-primary">
                    <i class="bi bi-upload me-2"></i>
                    Importar Usuarios
                </button>
            </div>
        </div>
    </div>
</div>
<!--Fin Carga_Masiva-->

<!--Registro-->
<div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroLabel">
                    <i class="bi bi-person-plus me-2"></i>
                    Añadir Usuario
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Sección de BÚSQUEDA (Siempre Visible) -->
                <div id="search-input-container">
                        <h6 class="mb-3 text-primary"><i class="bi bi-search me-1"></i> 1. Verificar Usuario Existente</h6>
                        <div class="row g-2 align-items-end">
                            <!-- Rol -->
                            <div class="col-md-3">
                                <label for="rolRegistro" class="form-label">Tipo de Usuario (Rol)</label>
                                <select class="form-control" id="rolRegistro" name="rol" required onchange="toggleFacultadEscuela('facultadEscuelaContainerRegistro'); completarCorreo();">
                                    <option value="">Seleccione un tipo de usuario</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Campo de Búsqueda por Correo -->
                            <div class="col-md-7">
                                <label for="searchValue" class="form-label">Correo Institucional</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchValue" placeholder="2020112233 o j_perez" required>
                                    <span class="input-group-text">@unjfsc.edu.pe</span>
                                </div>
                            </div>

                            <!-- Botón de Verificación -->
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success p-3 w-100" id="btnVerify" onclick="verifyUser()">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Verificar
                                </button>
                            </div>
                        </div>
                        <!-- Mensaje de Resultado de Búsqueda -->
                        <div id="searchResult" class="mt-3"></div>
                </div>
                <form id="formRegistro" action="{{ route('personas.store') }}" method="POST" class="mt-4">
                    @csrf
                    <!-- Campo Oculto para el ID del Semestre -->
                    <input type="hidden" name="id_semestre" value="{{ $id_semestre }}">
                    
                    <!-- CAMPO CLAVE: ID de Persona. Será llenado por JS si la persona existe. -->
                    <input type="hidden" id="personaId" name="persona_id">
                    <!-- El rol se toma del select 'rolRegistro' que ahora está dentro del form -->
                    <input type="hidden" id="rolHidden" name="rol">


                    <!-- Sección de DATOS PERSONALES (Oculta por defecto) -->
                    <div id="personalDataContainer" style="display: none;" class="section-box">
                        <h6 class="mb-3 text-secondary"><i class="bi bi-person-lines-fill me-1"></i> 2. Datos Personales</h6>
                        
                        <!-- Campos solo lectura para existentes / Editables para nuevos -->
                        <div class="row g-3">
                            <!-- El campo de código ahora es solo para estudiantes -->
                            <div class="col-md-6">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="tel" class="form-control" id="codigo" name="codigo" maxlength="10" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="correo_inst" class="form-label">Correo Institucional</label>
                                <input type="email" class="form-control" id="correo_inst" name="correo_inst" placeholder="ejemplo@unjfsc.edu.pe" required disabled>
                            </div>                        
                            <div class="col-md-4">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="nombres" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="sexo" class="form-label">Género</label>
                                <select class="form-control" id="sexo" name="sexo" required disabled>
                                    <option value="">Seleccione su género</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 text-secondary d-flex align-items-center justify-content-between">
                            <h6>Datos Adicionales (Opcional)</h6>
                            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#miContenido" aria-expanded="false" aria-controls="miContenido">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                        <div class="collapse row g-3" id="miContenido">
                            <div class="col-md-6">
                                <label for="dni" class="form-label">DNI</label>
                                <input type="tel" class="form-control" id="dni" name="dni" maxlength="8" disabled>
                            </div> 
                            <div class="col-md-6">
                                <label for="celular" class="form-label">Celular</label>
                                <input type="tel" class="form-control" id="celular" name="celular" disabled maxlength="9">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="provincia">Provincia</label>
                                    <select class="form-control" id="provincia" name="provincia">
                                        <option value="">Seleccione una provincia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distrito">Distrito</label>
                                    <select class="form-control" id="distrito" name="distrito" disabled>
                                        <option value="">Seleccione un distrito</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <!-- Sección de ASIGNACIÓN (Habilitada en ambos casos, pero con lógica) -->
                    <div id="assignmentContainer" class="section-box mt-4">
                        <h6 class="mb-3 text-secondary"><i class="bi bi-clipboard-check-fill me-1"></i> 3. Asignación y Rol</h6>
                        @if(Auth::user()->hasAnyRoles([3]))
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="facultad_registro_fixed" class="form-label">Facultad</label>
                                    <select class="form-control" id="facultad_registro_fixed" name="facultad" required>
                                        <option value="{{ $ap->seccion_academica->facultad->id }}">{{ $ap->seccion_academica->facultad->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="escuela_registro_fixed" class="form-label">Escuela</label>
                                    <select class="form-control" id="escuela_registro_fixed" name="escuela" required>
                                        <option value="{{ $ap->seccion_academica->escuela->id }}">{{ $ap->seccion_academica->escuela->name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="seccion_registro_fixed" class="form-label">Sección</label>
                                    <select class="form-control" id="seccion_registro_fixed" name="seccion" required>
                                        <option value="{{ $ap->seccion_academica->id }}">{{ $ap->seccion_academica->seccion }}</option>
                                    </select>
                                </div>
                            </div>
                        @else
                        <div class="row g-3">
                            <!-- Facultad -->
                            <div class="col-md-4">
                                <label for="facultad" class="form-label">Facultad</label>
                                <select class="form-control" id="facultad_registro" name="facultad" required>
                                    <option value="">Seleccione una facultad</option>
                                    @foreach($facultades as $facultad)
                                        @foreach($facultades as $fac)
                                            <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <!-- Escuela -->
                            <div class="col-md-4">
                                <label for="escuela" class="form-label">Escuela</label>
                                <select class="form-control" id="escuela_registro" name="escuela" required disabled>
                                    <option value="">Seleccione una escuela</option>
                                </select>
                            </div>
                            <!-- Seccion -->
                            <div class="col-md-4">
                                <label for="seccion" class="form-label">Sección</label>
                                <select class="form-control" id="seccion_registro" name="seccion" disabled>
                                    <option value="">Seleccione una sección</option>
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Cerrar
                </button>
                <button type="submit" form="formRegistro" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>
                    Registrar Usuario
                </button>
            </div>
        </div>
    </div>
</div>
<!--Fin Registro-->

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
</script>
@endif

<script src="{{ asset('js/cuadro_registro_user.js') }}"></script>
<script>
    // Filtro Facultad - Escuela - Seccion
    function setupDependentSelects(facultadId, escuelaId, seccionId) {
        const facultadSelect = document.getElementById(facultadId);
        const escuelaSelect = document.getElementById(escuelaId);
        const seccionSelect = document.getElementById(seccionId);
        const semestreActivoId = {{ session('semestre_actual_id') ?? 'null' }};

        if (facultadSelect) {
            facultadSelect.addEventListener('change', function () {
                const selectedFacultadId = this.value;
                // Reset dependants
                escuelaSelect.innerHTML = '<option value="">Seleccione una escuela</option>';
                seccionSelect.innerHTML = '<option value="">Seleccione una sección</option>';

                escuelaSelect.disabled = true;
                seccionSelect.disabled = true;
                if (!selectedFacultadId) {
                    return;
                }

                escuelaSelect.innerHTML = '<option value="">Cargando...</option>';
                fetch(`/api/escuelas/${selectedFacultadId}`)
                    .then(res => res.json())
                    .then(data => {
                        let options = '<option value="">Seleccione una escuela</option>';
                        data.forEach(e => {
                            options += `<option value="${e.id}">${e.name}</option>`;
                        });
                        escuelaSelect.innerHTML = options;
                        escuelaSelect.disabled = false;
                    })
                    .catch(() => {
                        escuelaSelect.innerHTML = '<option value="">Error al cargar</option>';
                    });
            });
        }

        if (escuelaSelect) {
            escuelaSelect.addEventListener('change', function () {
                const selectedEscuelaId = this.value;
                seccionSelect.innerHTML = '<option value="">Seleccione una sección</option>';
                seccionSelect.disabled = true;

                if (!selectedEscuelaId || !semestreActivoId) {
                    return;
                }

                seccionSelect.innerHTML = '<option value="">Cargando...</option>';
                fetch(`/api/secciones/${selectedEscuelaId}/${semestreActivoId}`)
                    .then(res => res.json())
                    .then(data => {
                        let options = '<option value="">Seleccione una sección</option>';
                        data.forEach(d => {
                            options += `<option value="${d.id}">${d.name}</option>`;
                        });
                        seccionSelect.innerHTML = options;
                        seccionSelect.disabled = false;
                    })
                    .catch(() => {
                        seccionSelect.innerHTML = '<option value="">Error al cargar</option>';
                    });
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Inicializar para el modal de registro individual
        setupDependentSelects('facultad_registro', 'escuela_registro', 'seccion_registro');
        // Inicializar para el modal de carga masiva
        setupDependentSelects('facultad_masivo', 'escuela_masivo', 'seccion_masivo');
    });

    const ELEMENTS = {
        form: document.getElementById('formRegistro'),
        searchValue: document.getElementById('searchValue'),
        rolRegistro: document.getElementById('rolRegistro'),
        btnVerify: document.getElementById('btnVerify'),
        searchResult: document.getElementById('searchResult'),
        personalDataContainer: document.getElementById('personalDataContainer'),
        btnSubmit: document.querySelector('#modalRegistro button[type="submit"]'),
        
        // Campos del formulario
        inputPersonaId: document.getElementById('personaId'), 
        inputDni: document.getElementById('dni'),
        inputCodigo: document.getElementById('codigo'),
        inputNombres: document.getElementById('nombres'),
        inputApellidos: document.getElementById('apellidos'),
        
        // Campos de Asignación
        facultadRegistro: document.getElementById('facultad_registro') || document.getElementById('facultad_registro_fixed'),
        escuelaRegistro: document.getElementById('escuela_registro') || document.getElementById('escuela_registro_fixed'),
        seccionRegistro: document.getElementById('seccion_registro') || document.getElementById('seccion_registro_fixed'),
        
        // Lista de todos los campos de datos personales (excepto los de búsqueda)
        personalInputs: [
            document.getElementById('dni'),
            document.getElementById('codigo'),
            document.getElementById('nombres'),
            document.getElementById('apellidos'),
            document.getElementById('celular'),
            document.getElementById('correo_inst'), // Este es el campo correcto
            document.getElementById('sexo'),
            document.getElementById('provincia'),
            document.getElementById('distrito'),
        ]
    };

    function showNewUserForm(fullEmail) {
        ELEMENTS.searchResult.innerHTML = `<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>No se encontró usuario. Complete los datos para crearlo.</div>`;
        ELEMENTS.personalDataContainer.style.display = 'block';
        ELEMENTS.form.classList.remove('form-state-existing');
        ELEMENTS.form.classList.add('form-state-new');

        ELEMENTS.personalInputs.forEach(input => {
            input.disabled = false;
            input.readOnly = false;
            if (input.id !== 'correo_inst' && input.id !== 'codigo') {
                 input.value = '';
            }
        });

        ELEMENTS.inputPersonaId.value = '';
        ELEMENTS.inputCodigo.value = ELEMENTS.searchValue.value;
        ELEMENTS.inputCodigo.readOnly = true;
        ELEMENTS.personalInputs.find(i => i.id === 'correo_inst').value = fullEmail;
        ELEMENTS.personalInputs.find(i => i.id === 'correo_inst').readOnly = true;
        
        //ELEMENTS.facultadRegistro.disabled = false;
        //ELEMENTS.escuelaRegistro.disabled = true;
        ELEMENTS.btnSubmit.disabled = false;
        ELEMENTS.inputNombres.focus();
    }

    function showExistingUserForm(persona, isAssigned, ap) {
        const message = isAssigned 
            ? `<div class="alert alert-danger"><i class="bi bi-x-circle-fill me-2"></i>Usuario encontrado, pero <strong>YA ESTÁ ASIGNADO</strong> a este semestre.</div>`
            : `<div class="alert alert-info"><i class="bi bi-info-circle-fill me-2"></i>Usuario encontrado: <strong>${persona.nombres} ${persona.apellidos}</strong>. Proceda a la Asignación.</div>`;
        
        ELEMENTS.searchResult.innerHTML = message;
        ELEMENTS.personalDataContainer.style.display = 'block';
        ELEMENTS.form.classList.remove('form-state-new');
        ELEMENTS.form.classList.add('form-state-existing');

        ELEMENTS.personalInputs.forEach(input => {
            input.disabled = true;
            input.value = persona[input.name] || '';
        });

        ELEMENTS.inputPersonaId.value = persona.id;
        ELEMENTS.btnSubmit.disabled = isAssigned;

        ELEMENTS.facultadRegistro.innerHTML = `<option value="">${ap.seccion_academica.facultad.name}</option>`;
        ELEMENTS.escuelaRegistro.innerHTML = `<option value="">${ap.seccion_academica.escuela.name}</option>`;
        ELEMENTS.seccionRegistro.innerHTML = `<option value="">${ap.seccion_academica.seccion}</option>`;
    }

    function resetForm() {
        ELEMENTS.form.reset();
        ELEMENTS.form.classList.remove('form-state-new', 'form-state-existing');
        ELEMENTS.searchResult.innerHTML = '';
        ELEMENTS.personalDataContainer.style.display = 'none';
        ELEMENTS.btnSubmit.disabled = true;
        ELEMENTS.rolRegistro.disabled = false;
        ELEMENTS.facultadRegistro.disabled = false;
        ELEMENTS.escuelaRegistro.disabled = false;
        ELEMENTS.seccionRegistro.disabled = false;
        ELEMENTS.inputPersonaId.value = '';
        ELEMENTS.searchValue.disabled = false;
    }

    // Verificar usuario existente btnVerify
    async function verifyUser() {
        const searchValue = ELEMENTS.searchValue.value.trim().toLowerCase();
        const rolId = ELEMENTS.rolRegistro.value;
        
        
        if (!rolId || !searchValue) {
            ELEMENTS.searchResult.innerHTML = `<div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Debe seleccionar un rol e ingresar el correo para verificar.</div>`;
            return;
        }

        ELEMENTS.btnVerify.disabled = true;
        ELEMENTS.btnVerify.classList.add('loading');

        // Limpiamos solo el resultado anterior antes de una nueva búsqueda.
        ELEMENTS.searchResult.innerHTML = '';
        // Guardar el rol seleccionado en el campo oculto del formulario
        document.getElementById('rolHidden').value = ELEMENTS.rolRegistro.value;

        const fullEmail = `${searchValue}@unjfsc.edu.pe`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log(fullEmail);

        const response = await fetch(`/api/verificar/${fullEmail}`);

        if(!response.ok) {
            alert('Error al verificar usuario:' + error);
            console.error('Error al verificar usuario:', error);
            ELEMENTS.searchResult.innerHTML = `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Ocurrió un error al verificar.</div>`;
            return;
        }

        ELEMENTS.btnVerify.disabled = false;
        ELEMENTS.btnVerify.classList.remove('loading');

        const result = await response.json();
        const data = result;

        //resetForm();
        console.log(data.persona);
        console.log(data);

        resetForm();

        if(data.persona) {
            showExistingUserForm(data.persona, data.asignacionExistente, data.ap);
        } else {
            showNewUserForm(fullEmail);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const rolSelect = document.getElementById('rolRegistro');
        const searchInput = document.getElementById('searchValue');
        const verifyButton = document.getElementById('btnVerify');
        let isNewUserFormActive = false; // Flag para controlar si el form de nuevo usuario está activo
        let previousRolValue = rolSelect.value;

        // Función para actualizar el estado del formulario de nuevo usuario
        window.setNewUserFormStatus = (isActive) => {
            isNewUserFormActive = isActive;
        };

        // Guardar el valor anterior antes del cambio
        rolSelect.addEventListener('focus', function() {
            previousRolValue = this.value;
        });
    
        function updateSearchInputRestrictions() {
            const selectedValue = rolSelect.value;
            const selectedOption = rolSelect.options[rolSelect.selectedIndex];
            const isStudent = selectedOption.text.toLowerCase().includes('estudiante');

            if (!selectedValue) {
                searchInput.disabled = true;
                verifyButton.disabled = true;
                searchInput.placeholder = 'Seleccione un rol primero';
                return;
            }

            searchInput.disabled = false;
            verifyButton.disabled = false;
            searchInput.value = '';

            if (isStudent) {
                searchInput.type = 'tel';
                searchInput.placeholder = 'Ingrese solo el código de 10 dígitos';
                searchInput.maxLength = 10;
                searchInput.pattern = '[0-9]*';
            } else {
                searchInput.type = 'text';
                searchInput.placeholder = 'ej: jperez';
                searchInput.maxLength = 50;
                searchInput.removeAttribute('pattern');
            }
            searchInput.focus();
        }

        rolSelect.addEventListener('change', function() {
            updateSearchInputRestrictions();
        });

        updateSearchInputRestrictions();
    });

    // rolMasivo
    document.getElementById('rolMasivo').addEventListener('change', function() {
        console.log(this.value);
        //puede ver 2, 3, 4
        if (this.value === '2' || this.value === '3' || this.value === '4') {
            document.getElementById('model-img-registro').style.display = 'block';
            console.log('block');
        } else {
            document.getElementById('model-img-registro').style.display = 'none';
            console.log('none');
        }

        // si value es vacio o nullo
        if(this.value == '' || this.value == null) {
            document.getElementById('assignmentContainer').style.display = 'none';
        } else {
            document.getElementById('assignmentContainer').style.display = 'block';
        }
    });
        
</script>
@endpush
