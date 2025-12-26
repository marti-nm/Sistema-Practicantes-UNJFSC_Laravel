@extends('template')
@section('title', 'Gestión de Estudiantes')
@section('subtitle', 'Administrar y visualizar información de estudiantes')

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
        --info-color: #0891b2;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .docentes-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0;
    }

    /* Card Principal */
    .docentes-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .docentes-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .docentes-card-header {
        background: linear-gradient(135deg, var(--surface-color) 0%, #f8fafc 100%);
        border-bottom: 2px solid var(--border-color);
        padding: 1.5rem 2rem;
        position: relative;
    }

    .docentes-card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .docentes-card-title {
        font-size: 1.375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-transform: none;
    }

    .docentes-card-title i {
        color: var(--primary-color);
        font-size: 1.25rem;
    }

    .docentes-card-body {
        padding: 1.5rem;
    }

    /* Tabla Moderna */
    .table-container {
        background: var(--surface-color);
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table {
        margin: 0;
        border: none;
        font-size: 0.9rem;
    }

    .table thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: none;
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        color: var(--text-primary);
        padding: 1rem 0.75rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(30, 58, 138, 0.02);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Botones de Acción */
    .btn {
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem; /* Reducido */
        font-size: 0.75rem; /* Tamaño más pequeño */
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem; /* Ligero espacio entre ícono y texto */
        margin: 0 0.125rem;
        min-width: 30px;  /* Opcional: controla el ancho mínimo */
        height: 30px;     
    }

    .btn-info {
        background: var(--info-color);
        color: white;
    }

    .btn-info:hover {
        background: #0e7490;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .btn-danger:hover {
        background: #991b1b;
        border-color: #991b1b;
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background: #047857;
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

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        color: white;
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
        display: flex;
        align-items: center;
        gap: 0.5rem;
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
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
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

    .form-control[readonly], .form-control[disabled] {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: var(--text-secondary);
    }

    /* Badge mejorado */
    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Links mejorados */
    .text-decoration-none {
        color: var(--primary-color);
        transition: color 0.2s ease;
    }

    .text-decoration-none:hover {
        color: var(--primary-light);
        text-decoration: none !important;
    }

    /* Botones con espaciado */
    .btn + .btn {
        margin-left: 0.25rem;
    }

    /* Confirmación de eliminación mejorada */
    .btn-danger:hover {
        background: #991b1b;
        border-color: #991b1b;
    }

    /* Estados de formulario */
    .form-control:not([readonly]):not([disabled]) {
        border-color: var(--primary-color);
        background: var(--surface-color);
    }

    .form-control:not([readonly]):not([disabled]):focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Mejoras en la transición de estados */
    .form-control {
        transition: all 0.3s ease;
    }

    /* Estilo para campos deshabilitados en modo edición */
    .form-control[disabled] {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #64748b;
        opacity: 0.8;
    }

    /* Mejoras para la sección de foto */
    .photo-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1.5rem;
        padding: 2rem 1.5rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 1rem;
        border: 2px dashed var(--border-color);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .photo-section::before {
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

    .photo-section:hover {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(30, 58, 138, 0.02) 0%, rgba(59, 130, 246, 0.02) 100%);
        box-shadow: var(--shadow-sm);
    }

    .photo-section:hover::before {
        transform: scaleX(1);
    }

    .profile-photo {
        width: 180px;
        height: 180px;
        border-radius: 1rem;
        object-fit: cover;
        border: 4px solid var(--surface-color);
        box-shadow: var(--shadow-lg);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .profile-photo:hover {
        transform: scale(1.05);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border-color: var(--primary-color);
    }

    .default-avatar {
        width: 180px;
        height: 180px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 4rem;
        box-shadow: var(--shadow-lg);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .default-avatar::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(45deg);
        transition: all 0.6s ease;
        opacity: 0;
    }

    .default-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 20px 40px rgba(30, 58, 138, 0.3);
    }

    .default-avatar:hover::before {
        opacity: 1;
        animation: shimmer 1.5s ease-in-out;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    /* Upload Button Mejorado */
    .upload-btn {
        background: var(--surface-color);
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
        cursor: pointer;
        font-weight: 600;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 160px;
        justify-content: center;
        box-shadow: var(--shadow-sm);
    }

    .upload-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(30, 58, 138, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .upload-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-light);
        text-decoration: none;
    }

    .upload-btn:hover::before {
        left: 100%;
    }

    .upload-btn:active {
        transform: translateY(0);
        box-shadow: var(--shadow-sm);
    }

    .upload-btn i {
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .upload-btn:hover i {
        transform: scale(1.1);
    }

    /* Información de archivo mejorada */
    .photo-section small {
        font-size: 0.8rem;
        color: var(--text-secondary);
        text-align: center;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 0.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.5);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .photo-section small::before {
        content: '\f1c5'; /* Bootstrap icon info-circle */
        font-family: 'bootstrap-icons';
        color: var(--info-color);
        font-size: 0.875rem;
    }

    /* Información de archivo estructurada */
    .photo-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(226, 232, 240, 0.6);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-sm);
    }

    .photo-info .info-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-secondary);
        padding: 0.25rem 0;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .photo-info .info-item:hover {
        background: rgba(30, 58, 138, 0.05);
        color: var(--primary-color);
    }

    .photo-info .info-item i {
        color: var(--primary-color);
        font-size: 0.875rem;
        min-width: 16px;
        transition: transform 0.2s ease;
    }

    .photo-info .info-item:hover i {
        transform: scale(1.1);
    }

    .photo-info .info-item span {
        font-weight: 500;
        letter-spacing: 0.025em;
    }

    /* Container para la foto con posición relativa */
    .photo-container {
        position: relative;
        display: inline-block;
        border-radius: 1rem;
        overflow: hidden;
    }

    /* Estados de carga y error */
    .photo-loading {
        position: relative;
    }

    .photo-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 2rem;
        height: 2rem;
        margin: -1rem 0 0 -1rem;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top: 3px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 10;
    }

    .photo-error {
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 0.5rem;
    }

    .photo-error i {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .photo-error span {
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
    }

    /* Validación visual */
    .photo-section.valid {
        border-color: var(--success-color);
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.02) 0%, rgba(16, 185, 129, 0.02) 100%);
    }

    .photo-section.valid::before {
        background: linear-gradient(90deg, var(--success-color), #10b981);
        transform: scaleX(1);
    }

    .photo-section.invalid {
        border-color: var(--danger-color);
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.02) 0%, rgba(239, 68, 68, 0.02) 100%);
    }

    .photo-section.invalid::before {
        background: linear-gradient(90deg, var(--danger-color), #ef4444);
        transform: scaleX(1);
    }

    /* Tooltip personalizado para información */
    .photo-tooltip {
        position: relative;
        cursor: help;
    }

    .photo-tooltip::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: var(--text-primary);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .photo-tooltip::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(2px);
        border: 4px solid transparent;
        border-top-color: var(--text-primary);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .photo-tooltip:hover::before,
    .photo-tooltip:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-8px);
    }

    /* Animaciones de entrada */
    .photo-section {
        animation: photoFadeIn 0.5s ease;
    }

    @keyframes photoFadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* ...existing photo styles... */
</style>
@endpush

@section('content')
<div class="docentes-container">
    <div class="docentes-card fade-in">
        <div class="docentes-card-header">
            <h5 class="docentes-card-title">
                <i class="bi bi-mortarboard"></i>
                Lista de Estudiantes
            </h5>
        </div>
        <div class="docentes-card-body">
            {{-- Filtros --}}
            @if(Auth::user()->hasAnyRoles([1, 2]))
            <x-data-filter
                route="estudiante"
                :facultades="$facultades"
            />
            @endif
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Email</th>
                                <th>Apellidos y Nombres</th>
                                <th>Facultad</th>
                                <th>Escuela</th>
                                <th>Sección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($personas as $index => $persona)
                            <tr data-docente-id="{{ $persona->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge badge-light" style="background: var(--background-color); color: var(--text-primary); font-weight: 500;">
                                        {{ $persona->correo_inst }}
                                    </span>
                                </td>
                                <td>{{ strtoupper($persona->apellidos . ' ' . $persona->nombres) }}</td>
                                <td>{{ $persona->asignacion_persona->seccion_academica->facultad->name }}</td>
                                <td>{{ $persona->asignacion_persona->seccion_academica->escuela->name }}</td>
                                <td>{{ $persona->asignacion_persona->seccion_academica->seccion}}</td>
                                <td>
                                    @if($persona->asignacion_persona->state == 1 || $persona->asignacion_persona->state == 2)
                                    <button type="button" class="btn btn-info" 
                                    data-toggle="modal" data-target="#modalEditar{{ $persona->id }}" 
                                    data-d="{{ $persona->distrito }}" data-p="{{ $persona->provincia }}"
                                    data-f="{{ $persona->escuela->facultad_id ?? '' }}" data-e="{{ $persona->id_escuela ?? '' }}">
                                        <i class="bi bi-eye"></i>
                                        
                                    </button>
                                    @if(!$semestre_bloqueado)
                                    <button type="button" class="btn btn-danger btn-disabled-ap" 
                                        data-id-ap="{{ $persona->asignacion_persona->id }}"
                                        data-id-sa="{{ $persona->asignacion_persona->seccion_academica->id }}"
                                        data-state-ap="{{ $persona->asignacion_persona->state }}"
                                        data-nombre-ap="{{ $persona->apellidos . ' ' . $persona->nombres }}"
                                        data-email-ap="{{ $persona->correo_inst }}">
                                        <i class="bi bi-person-x-fill"></i>
                                    </button>
                                    @endif
                                    @elseif($persona->asignacion_persona->state == 3 && Auth::user()->hasAnyRoles([1,2]))
                                    <button type="button" class="btn btn-secondary btn-management-ap" 
                                        data-id-ap="{{ $persona->asignacion_persona->id }}"
                                        data-id-sa="{{ $persona->asignacion_persona->seccion_academica->id }}"
                                        data-nombre-ap="{{ $persona->apellidos . ' ' . $persona->nombres }}"
                                        data-email-ap="{{ $persona->correo_inst }}">
                                        <i class="bi bi-hourglass-bottom"></i>
                                    </button>
                                    @elseif($persona->asignacion_persona->state == 3)
                                        <label class="badge badge-warning text-black">Pendiente</label>
                                    @elseif($persona->asignacion_persona->state == 4)
                                        <!-- button para habilitar -->
                                        <button type="button" class="btn btn-warning btn-disabled-ap" 
                                            data-id-ap="{{ $persona->asignacion_persona->id }}"
                                            data-id-sa="{{ $persona->asignacion_persona->seccion_academica->id }}"
                                            data-state-ap="{{ $persona->asignacion_persona->state }}"
                                            data-nombre-ap="{{ $persona->apellidos . ' ' . $persona->nombres }}"
                                            data-email-ap="{{ $persona->correo_inst }}">
                                            <i class="bi bi-person-check-fill"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @if($personas->isEmpty())
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="bi bi-person-x"></i>
                                    <p class="mb-0">No se encontraron docentes registrados.</p>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Modal-->
@foreach ($personas as $persona)
<div class="modal fade" id="modalEditar{{ $persona->id }}" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true"> 
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerLabel">
                    <i class="bi bi-person-vcard"></i>
                    Información del Docente
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditPersona{{ $persona->id }}" method="POST" action="{{ route('persona.editar') }}" enctype="multipart/form-data">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    @csrf
                    @method('POST')
                    <input type="hidden" id="persona_id" name="persona_id" value="{{ $persona->id }}">
                    <div class="row">
                        <!-- Columna izquierda: Formulario -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="codigo">Código</label>
                                        <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $persona->codigo }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dni">DNI</label>
                                        <input type="text" class="form-control" id="dni" name="dni" value="{{ $persona->dni }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="celular">Celular</label>
                                        <input type="tel" class="form-control" id="celular" name="celular" value="{{ $persona->celular }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombres">Nombres</label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" value="{{ $persona->nombres }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apellidos">Apellidos</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ $persona->apellidos }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="correo_inst">Correo Institucional</label>
                                        <input type="email" class="form-control" id="correo_inst" name="correo_inst" value="{{ $persona->correo_inst }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="departamento">Departamento</label>
                                        <input type="text" class="form-control" id="departamento" name="departamento" value="{{ $persona->departamento }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sexo">Género</label>
                                        <select class="form-control" id="sexo" name="sexo" value="{{ $persona->sexo }}" disabled>
                                            <option value="">Seleccione</option>
                                            <option value="M"{{ $persona->sexo == 'M' ? 'selected' : '' }}>Masculino</option>
                                            <option value="F"{{ $persona->sexo == 'F' ? 'selected' : '' }}>Femenino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="provincia">Provincia</label>
                                        <select class="form-control" id="provincia" name="provincia" disabled>
                                            <option value="">Seleccione</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="distrito">Distrito</label>
                                        <select class="form-control" id="distrito" name="distrito" value="{{ $persona->distrito }}"  disabled>
                                            <option value="">Seleccione</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="facultad">Facultad</label>
                                        <select class="form-control" id="facultad" name="facultad" disabled>
                                            <option value="">Seleccione</option>
                                            @foreach($facultades as $facultad)
                                                <option value="{{ $facultad->id }}">{{ $facultad->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="escuela">Escuela</label>
                                        <select class="form-control" id="escuela" name="escuela" disabled>
                                            <option value="">Seleccione</option>
                                            @foreach($escuelas as $escuela)
                                                <option value="{{ $escuela->id }}" data-facultad="{{ $escuela->facultad_id }}" hidden>
                                                    {{ $escuela->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna derecha: Fotografía -->
                        <div class="col-md-4">
                            <div class="photo-section">
                                <div class="photo-container">
                                    @if ($persona->ruta_foto)
                                        <img src="{{ asset($persona->ruta_foto) }}" alt="Foto del docente" class="profile-photo" id="previewFoto">
                                    @else
                                        <div class="default-avatar" id="iconoDefault">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <input type="file" name="ruta_foto" id="ruta_foto{{ $persona->id }}" accept="image/*" onchange="previewImagen(event)" style="display: none;">
                                
                                <label for="ruta_foto{{ $persona->id }}" class="upload-btn">
                                    <i class="bi bi-cloud-upload"></i>
                                    Actualizar Foto
                                </label>
                                
                                <div class="photo-info">
                                    <div class="info-item">
                                        <i class="bi bi-file-earmark-image"></i>
                                        <span>JPG, PNG, GIF</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-hdd"></i>
                                        <span>Máximo 2MB</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-aspect-ratio"></i>
                                        <span>Recomendado: 500x500px</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" id="btnEditar">
                    <i class="bi bi-pencil-square"></i> 
                    Editar 
                </button>
                <button type="submit" form="formEditPersona{{ $persona->id }}" class="btn btn-success d-none" id="btnUpdate">
                    <i class="bi bi-check-circle"></i>
                    Guardar Cambios
                </button>
                <button type="button" class="btn btn-secondary" id="btnCancelar" data-dismiss="modal">
                    <i class="bi bi-x-circle"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{--  
<div class="modal" id="modalDisabledAp">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deshabilitar Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- mensaje que primero tendra ser aprobado por el administrador -->
                <div id="alertDeshabilitarAp" class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Advertencia!</strong>
                    <p>El estudiante debe ser aprobado por el administrador antes de deshabilitarlo o eliminado.</p>
                </div>
                <!-- mensaje que tendra el docente para habilitar a un estudiante -->
                <div id="alertHabilitarAp" class="alert alert-info">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Advertencia!</strong>
                    <p>Para habilitar a un estudiante debe ser aprobado por el administrador.</p>
                </div>
                <form id="formEliminar" action="{{ route('solicitud_ap') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_ap" id="id_ap">
                    <input type="hidden" name="id_sa" id="id_sa">
                    <!-- mostrar el nombre del estudiante -->
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="bi bi-person"></i> Nombre del Estudiante:</label>
                        <p id="nombre_ap" class="font-weight-bold align-items-center"></p>
                    </div>
                    <div class="form-group mt-3" id="correccion-options-ap">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Seleccionar una opción:</label>
                        <div id="option-deshabilitar-ap">
                            <div class="row g-2">
                                <div class="col">
                                    <div class="correccion-cell h-100" id="deshabilitar-ap" onclick="selectCorreccion('deshabilitar', 'ap')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;" data-toggle="tooltip" data-placement="top" title="Podra deshabilitar y luego habilitar.">
                                        <i class="bi bi-lock" style="font-size: 1.5em;"></i><br>Deshabilitar
                                        <input type="radio" name="opcion" id="correccionDeshabilitar-ap" value="1" class="d-none" checked>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="correccion-cell h-100" id="eliminar-ap" onclick="selectCorreccion('eliminar', 'ap')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;" data-toggle="tooltip" data-placement="top" title="Tendrá que enviar otra nota y aprueba el archivo.">
                                        <i class="bi bi-trash" style="font-size: 1.5em;"></i><br>Eliminar
                                        <input type="radio" name="opcion" id="correccionEliminar-ap" value="2" class="d-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="option-habilitar-ap" class="row g-2">
                            <div class="col">
                                <div class="correccion-cell h-100" id="habilitar-ap" onclick="selectCorreccion('habilitar', 'ap')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;" data-toggle="tooltip" data-placement="top" title="Podra deshabilitar y luego habilitar.">
                                    <i class="bi bi-unlock" style="font-size: 1.5em;"></i><br>Habilitar
                                    <input type="radio" name="opcion" id="correccionHabilitar-ap" value="3" class="d-none" checked>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="comentario-ap" class="font-weight-bold">
                            <i class="bi bi-chat-dots"></i> Comentario
                        </label>
                        <textarea name="comentario" id="comentario-ap" class="form-control" required rows="3" placeholder="Ej: La firma no es visible, por favor, vuelva a escanear el documento."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEliminar" class="btn btn-danger" id="btnEliminar">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalManagementAp">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formManagement" action="{{ route('solicitud.ap') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_sol" id="id_sol">
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="bi bi-person"></i> Nombre del Estudiante:</label>
                        <p id="nombre_m_ap" class="font-weight-bold align-items-center text-capitalize"></p>
                    </div>
                    <!-- mostrar el estado de la solicitud de baja -->
                    <div class="form-group mt-3" id="estado-solicitud-ap">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Acción a realizar:</label>
                        <p id="accion_ap" class="font-weight-bold align-items-center"></p>
                    </div>
                    <!-- justificacion de la solicitud -->
                    <div class="form-group mt-3">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Justificación:</label>
                        <p id="justificacion_ap" class="font-weight-bold align-items-center"></p>
                    </div>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Seleccionar una opción:</label>
                        <div class="row g-2">
                            <div class="col">
                                <div class="correccion-cell h-100" id="aprobar-management" onclick="selectGestion('aprobar')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;">
                                    <i class="bi bi-check-circle" style="font-size: 1.5em;"></i><br>Aprobar
                                    <input type="radio" name="estado" id="gestionAprobar" value="1" class="d-none" checked>
                                </div>
                            </div>
                            <div class="col">
                                <div class="correccion-cell h-100" id="rechazar-management" onclick="selectGestion('rechazar')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;">
                                    <i class="bi bi-x-circle" style="font-size: 1.5em;"></i><br>Rechazar
                                    <input type="radio" name="estado" id="gestionRechazar" value="2" class="d-none">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="comentario-ap" class="font-weight-bold">
                            <i class="bi bi-chat-dots"></i> Comentario
                        </label>
                        <textarea name="comentario" id="comentario-management" required class="form-control" rows="3" placeholder="Ej: Documentación incompleta."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" form="formManagement" class="btn btn-primary" id="btnManagement">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal para enabledAp -->
<div class="modal" id="modalEnabledAp">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Habilitar Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de habilitar al estudiante?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnEnabledAp">Habilitar</button>
            </div>
        </div>
    </div>
</div>
 --}}

 @include('list_users.partials.modales_gestion_estado')
@endsection

@push('js')
<script src="{{ asset('js/persona_edit.js') }}"></script>
<script src="{{ asset('js/gestion_estado_usuario.js') }}"></script>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const facultadSelect = document.getElementById('facultad');
        const escuelaSelect = document.getElementById('escuela');
        const docenteSelect = document.getElementById('docente');

        facultadSelect.addEventListener('change', function () {
            const facultadId = this.value;
            escuelaSelect.innerHTML = '<option value="">Cargando...</option>';
            docenteSelect.innerHTML = '<option value="">-- Todos --</option>';

            if (!facultadId) {
                escuelaSelect.innerHTML = '<option value="">-- Todas --</option>';
                return;
            }

            fetch(`/api/escuelas/${facultadId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Todas --</option>';
                    data.forEach(e => {
                        options += `<option value="${e.id}">${e.name}</option>`;
                    });
                    escuelaSelect.innerHTML = options;
                })
                .catch(() => {
                    escuelaSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        });

        escuelaSelect.addEventListener('change', function () {
            const escuelaId = this.value;
            docenteSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!escuelaId) {
                docenteSelect.innerHTML = '<option value="">-- Todos --</option>';
                return;
            }

            fetch(`/api/docentes/${escuelaId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Todos --</option>';
                    data.forEach(d => {
                        options += `<option value="${d.id}">${d.nombre}</option>`;
                    });
                    docenteSelect.innerHTML = options;
                })
                .catch(() => {
                    docenteSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        });

        const semestreSelect = document.getElementById('semestre');

        docenteSelect.addEventListener('change', function () {
            const docenteId = this.value;
            semestreSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!docenteId) {
                semestreSelect.innerHTML = '<option value="">-- Todos --</option>';
                return;
            }

            fetch(`/api/semestres/${docenteId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Todos --</option>';
                    data.forEach(s => {
                        options += `<option value="${s.id}">${s.codigo}</option>`;
                    });

                    semestreSelect.innerHTML = options;
                })
                .catch(() => {
                    semestreSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        });
    });
</script>
{{--  
<script>
    // btn-disabled-ap
    document.addEventListener('DOMContentLoaded', function () {
        const btnDisabledAp = document.querySelectorAll('.btn-disabled-ap');

        const MODAL_SELECTOR = '#modalDisabledAp';
        const modalElement = document.querySelector(MODAL_SELECTOR);
        const myModal = new bootstrap.Modal(modalElement);

        btnDisabledAp.forEach(btn => {
            btn.addEventListener('click', function () {
                const ID_AP = this.getAttribute('data-id-ap');
                document.getElementById('id_ap').value = ID_AP;
                document.getElementById('nombre_ap').textContent = this.getAttribute('data-nombre-ap');
                document.getElementById('id_sa').value = this.getAttribute('data-id-sa');
                const stateAp = this.getAttribute('data-state-ap');

                const optionHabilitarAp = document.getElementById('option-habilitar-ap');
                const optionDeshabilitarAp = document.getElementById('option-deshabilitar-ap');
                if(stateAp < 4){
                    document.getElementById('alertDeshabilitarAp').classList.remove('d-none');
                    document.getElementById('alertHabilitarAp').classList.add('d-none');

                    optionHabilitarAp.style.display = 'none';
                    optionDeshabilitarAp.style.display = 'block';

                    selectCorreccion('deshabilitar', 'ap');
                }else{
                    document.getElementById('alertDeshabilitarAp').classList.add('d-none');
                    document.getElementById('alertHabilitarAp').classList.remove('d-none');

                    optionHabilitarAp.style.display = 'block';
                    optionDeshabilitarAp.style.display = 'none';
                    selectCorreccion('habilitar', 'ap');
                }
                
                myModal.show();
            });
        });

        // Inicializar estilo por defecto
        updateCorreccionStyles('deshabilitar', 'ap');
    });

    // Función global para seleccionar corrección
    window.selectCorreccion = function(tipo, suffix) {
        // Actualizar Radio Button
        const radioId = 'correccion' + tipo.charAt(0).toUpperCase() + tipo.slice(1) + '-' + suffix;
        const radioParams = document.getElementById(radioId);
        if(radioParams) radioParams.checked = true;

        // Actualizar Estilos Visuales
        updateCorreccionStyles(tipo, suffix);
    };

    function updateCorreccionStyles(selectedTipo, suffix) {
        // IDs de las celdas
        const cellDeshabilitar = document.getElementById('deshabilitar-' + suffix);
        const cellEliminar = document.getElementById('eliminar-' + suffix);

        // Reset styles
        [cellDeshabilitar, cellEliminar].forEach(cell => {
            if(cell) {
                cell.classList.remove('bg-primary', 'text-white', 'border-primary');
                cell.classList.add('border-secondary', 'text-secondary');
                cell.style.backgroundColor = '#f8f9fa'; // Un gris muy claro por defecto
            }
        });

        // Apply active style
        const activeCell = document.getElementById(selectedTipo + '-' + suffix);
        if (activeCell) {
            activeCell.classList.remove('border-secondary', 'text-secondary');
            activeCell.classList.add('bg-primary', 'text-white', 'border-primary');
            activeCell.style.backgroundColor = ''; // Limpiar inline style para que tome la clase bg-primary
        }
    }

    const btnManagementAp = document.querySelectorAll('.btn-management-ap');
    document.addEventListener('DOMContentLoaded', function () {
        const MODAL_SELECTOR = '#modalManagementAp';
        const modalElement = document.querySelector(MODAL_SELECTOR);
        const myModal = new bootstrap.Modal(modalElement);

        btnManagementAp.forEach(button => {
            button.addEventListener('click', async function () {
                const idAp = this.getAttribute('data-id-ap');
                const nombreAp = this.getAttribute('data-nombre-ap');
                const idSa = this.getAttribute('data-id-sa');
                document.getElementById('id_ap').value = idAp;
                document.getElementById('nombre_m_ap').textContent = nombreAp;

                const reponse = await fetch(`/api/solicitud/getSolicitudAp/${idAp}`);
                const data = await reponse.json();
                console.log(data);

                document.getElementById('id_sol').value = data.id;
                document.getElementById('accion_ap').textContent = data.data.opcion.toUpperCase() ?? 'Error';
                document.getElementById('accion_ap').classList.add('font-weight-bold');

                document.getElementById('justificacion_ap').textContent = data.motivo.toUpperCase() ?? 'Error';
                document.getElementById('justificacion_ap').classList.add('font-weight-bold');

                selectGestion('aprobar');
                myModal.show();
            });
        });

        if(document.getElementById('aprobar-management')) {
            updateGestionStyles('aprobar');
        }
    })

    // Función para seleccionar gestión (Aprobar/Rechazar)
    window.selectGestion = function(tipo) {
        const radioId = 'gestion' + tipo.charAt(0).toUpperCase() + tipo.slice(1);
        const radioBtn = document.getElementById(radioId);
        if(radioBtn) radioBtn.checked = true;

        updateGestionStyles(tipo);
    };

    function updateGestionStyles(selectedTipo) {
        const cellAprobar = document.getElementById('aprobar-management');
        const cellRechazar = document.getElementById('rechazar-management');

        // Reset
        [cellAprobar, cellRechazar].forEach(cell => {
            if(cell) {
                cell.classList.remove('bg-success', 'bg-danger', 'text-white', 'border-success', 'border-danger');
                cell.classList.add('border-secondary', 'text-secondary');
                cell.style.backgroundColor = '#f8f9fa';
            }
        });

        // Active
        const activeCell = document.getElementById(selectedTipo + '-management');
        if(activeCell) {
            activeCell.classList.remove('border-secondary', 'text-secondary');
            activeCell.style.backgroundColor = '';
            
            if(selectedTipo === 'aprobar') {
                activeCell.classList.add('bg-success', 'text-white', 'border-success');
            } else {
                activeCell.classList.add('bg-danger', 'text-white', 'border-danger');
            }
        }
    }

    const btnEnabledAp = document.querySelectorAll('.btn-enabled-ap');
    document.addEventListener('DOMContentLoaded', function () {
        const MODAL_SELECTOR = '#modalEnabledAp';
        const modalElement = document.querySelector(MODAL_SELECTOR);
        const myModal = new bootstrap.Modal(modalElement);

        btnEnabledAp.forEach(button => {
            button.addEventListener('click', function () {
                console.log('Habilitar');
                myModal.show();
            });
        });
    });

</script>
--}}
@endpush
