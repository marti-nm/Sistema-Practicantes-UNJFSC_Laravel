@extends('template')
@section('title', 'Supervisión de Prácticas')
@section('subtitle', 'Monitorear y gestionar el proceso de prácticas preprofesionales')

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

    .supervision-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0;
    }

    /* Card Principal */
    .supervision-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .supervision-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .supervision-card-header {
        background: linear-gradient(135deg, var(--surface-color) 0%, #f8fafc 100%);
        border-bottom: 2px solid var(--border-color);
        padding: 1.5rem 2rem;
        position: relative;
    }

    .supervision-card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .supervision-card-title {
        font-size: 1.375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-transform: none;
    }

    .supervision-card-title i {
        color: var(--primary-color);
        font-size: 1.25rem;
    }

    .supervision-card-body {
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
        text-align: center;
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

    /* Badges para tipo de práctica y área */
    .practice-badge {
        background: linear-gradient(135deg, var(--info-color), #0e7490);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
    }

    .area-badge {
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
    }

    .no-registered {
        color: var(--text-secondary);
        font-style: italic;
        font-size: 0.875rem;
    }

    /* Botones de Acción */
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

    .btn-info {
        background: var(--info-color);
        color: white;
    }

    .btn-info:hover {
        background: #0e7490;
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

    .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .btn-close:hover {
        opacity: 1;
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

    /* Botones de Etapas */
    .etapas-container {
        margin-bottom: 2rem;
    }

    .btn-etapa {
        background: var(--surface-color);
        border: 2px solid var(--border-color);
        color: var(--text-primary);
        padding: 1rem;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    .btn-etapa::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--secondary-color), var(--text-secondary));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .btn-etapa:hover {
        border-color: var(--primary-color);
        background: rgba(30, 58, 138, 0.02);
        color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-etapa:hover::before {
        transform: scaleX(1);
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .btn-etapa.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        box-shadow: var(--shadow-md);
    }

    .btn-etapa.active::before {
        transform: scaleX(1);
        background: linear-gradient(90deg, var(--primary-light), white);
    }

    .btn-etapa.completed {
        background: var(--success-color);
        border-color: var(--success-color);
        color: white;
    }

    .btn-etapa.completed::before {
        transform: scaleX(1);
        background: linear-gradient(90deg, #047857, white);
    }

    /* Contenedor de etapas */
    .etapa-content {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-top: 1rem;
        box-shadow: var(--shadow-sm);
    }

    /* Estados vacíos */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--border-color);
        margin-bottom: 1rem;
    }

    /* Progress indicator */
    .progress-indicator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        position: relative;
    }

    .progress-indicator::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--border-color);
        z-index: 0;
    }

    .progress-indicator::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 0%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        border-radius: 2px;
        transition: width 0.5s ease;
        z-index: 0;
    }

    .progress-step {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: var(--surface-color);
        border: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: var(--text-secondary);
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .progress-step.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .progress-step.completed {
        background: var(--success-color);
        border-color: var(--success-color);
        color: white;
    }

    .progress-indicator.step-1::after { width: 25%; }
    .progress-indicator.step-2::after { width: 50%; }
    .progress-indicator.step-3::after { width: 75%; }
    .progress-indicator.step-4::after { width: 100%; }

    /* Responsive Design */
    @media (max-width: 768px) {
        .supervision-card-header {
            padding: 1.25rem 1.5rem;
        }

        .supervision-card-body {
            padding: 1rem;
        }

        .supervision-card-title {
            font-size: 1.25rem;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            min-width: 700px;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
        }

        .btn-etapa {
            padding: 0.75rem;
            font-size: 0.875rem;
        }

        .progress-step {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .etapas-container .col-md-3 {
            margin-bottom: 0.75rem;
        }

        .btn-etapa {
            padding: 0.75rem 0.5rem;
        }

        .btn-etapa i {
            font-size: 1rem;
        }

        .btn-etapa div {
            font-size: 0.875rem;
        }

        .progress-indicator {
            flex-direction: column;
            gap: 1rem;
        }

        .progress-indicator::before,
        .progress-indicator::after {
            display: none;
        }

        .progress-step {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }
    }

    /* Animaciones */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease;
    }

    .etapa-content {
        animation: fadeIn 0.3s ease;
    }

    /* Mejoras adicionales para integración completa */
    
    /* Estados de badges mejorados */
    .practice-badge,
    .area-badge {
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .practice-badge:hover,
    .area-badge:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    /* Texto de estudiante con estilo mejorado */
    .table tbody td strong {
        color: var(--text-primary);
        font-weight: 600;
        letter-spacing: -0.025em;
    }

    /* Warning state mejorado */
    .text-warning {
        color: var(--warning-color) !important;
    }

    /* Mejoras en los botones de etapa */
    .btn-etapa i {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .btn-etapa div {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .btn-etapa small {
        font-size: 0.75rem;
        opacity: 0.8;
        font-weight: 400;
    }

    /* Estados específicos para el progress indicator */
    .progress-indicator::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 0%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        border-radius: 2px;
        transition: width 0.5s ease;
        z-index: 0;
    }

    .progress-indicator.step-1::after { width: 25%; }
    .progress-indicator.step-2::after { width: 50%; }
    .progress-indicator.step-3::after { width: 75%; }
    .progress-indicator.step-4::after { width: 100%; }

    /* Mejoras en el modal de proceso */
    .modal-lg {
        max-width: 900px;
    }

    /* Contenido de etapa con padding mejorado */
    .etapa-content {
        min-height: 300px;
        display: flex;
        flex-direction: column;
    }

    /* Estados de botón activo/completado mejorados */
    .btn-etapa.active {
        transform: scale(1.02);
    }

    .btn-etapa.completed {
        transform: scale(1.02);
    }

    .btn-etapa.completed i::before {
        content: '\f633'; /* Bootstrap icon check-circle */
    }

    /* Mejoras en hover effects */
    .table tbody tr:hover td .practice-badge,
    .table tbody tr:hover td .area-badge {
        transform: scale(1.05);
    }

    /* Footer del modal mejorado */
    .modal-footer .btn {
        min-width: 120px;
    }

    /* Estados de carga */
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

    /* Mejoras en responsive para etapas */
    @media (max-width: 576px) {
        .etapas-container .col-md-3 {
            margin-bottom: 0.75rem;
        }

        .btn-etapa {
            padding: 0.75rem 0.5rem;
        }

        .btn-etapa i {
            font-size: 1rem;
        }

        .btn-etapa div {
            font-size: 0.875rem;
        }

        .progress-indicator {
            flex-direction: column;
            gap: 1rem;
        }

        .progress-indicator::before,
        .progress-indicator::after {
            display: none;
        }

        .progress-step {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }
    }

    /* Mejoras en el estado vacío */
    .empty-state {
        animation: fadeIn 0.5s ease;
    }

    .empty-state p {
        font-size: 1rem;
        margin-top: 1rem;
    }

    /* Transiciones suaves para cambio de etapas */
    .etapa-content > div {
        transition: all 0.3s ease;
    }

    .etapa-content > div[style*="display: none"] {
        opacity: 0;
        transform: translateY(10px);
    }

    /* Focus states mejorados */
    .btn-etapa:focus,
    .btn:focus {
        outline: 0;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.25);
    }

    /* ...existing styles... */
</style>
@endpush

@push('css')
  <style>
    .stepper {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 2rem 0;
        position: relative;
    }
    /* Línea de progreso (fondo) */
    .stepper::before {
        content: '';
        position: absolute;
        top: 3.5rem; /* Ajustado al centro del círculo */
        left: 0;
        right: 0;
        height: 4px;
        background-color: #e9ecef;
        z-index: 1;
        border-radius: 10px;
        transform: translateY(-50%);
    }

    .stepper-item {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        cursor: default;
        transition: all 0.3s ease;
    }

    .stepper-item:not(.locked) {
        cursor: pointer;
    }

    .stepper-circle {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        background-color: #fff;
        border: 3px solid #e9ecef;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 700;
        color: #adb5bd;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 1rem;
        font-size: 1.1rem;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .stepper-label {
        font-size: 0.8rem;
        color: #adb5bd;
        font-weight: 600;
        text-align: center;
        transition: color 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.5rem;
    }

    /* ESTADOS */
        
    /* Completado */
    .stepper-item.completed .stepper-circle {
        background-color: #198754;
        border-color: #198754;
        color: white;
        box-shadow: 0 4px 6px rgba(25, 135, 84, 0.25);
    }
    .stepper-item.completed .stepper-label {
        color: #198754;
    }
    
    /* Actual - Diseño Elegante */
    .stepper-item.current .stepper-circle {
        background-color: #fff;
        border-color: #0d6efd;
        color: #0d6efd;
        transform: scale(1.3);
        box-shadow: 0 0 0 6px rgba(13, 110, 253, 0.15);
        z-index: 11;
    }
    .stepper-item.current .stepper-label {
        color: #0d6efd;
        font-weight: 800;
        margin-top: 1rem; /* Ajuste por el scale */
    }

    /* Bloqueado */
    .stepper-item.locked .stepper-circle {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #ced4da;
    }
    .stepper-item.locked .stepper-label {
        color: #ced4da;
    }
    
    /* Hover para items desbloqueados */
    .stepper-item:not(.locked):not(.current):hover .stepper-circle {
        border-color: #0d6efd;
        color: #0d6efd;
        transform: translateY(-3px);
    }

    /* bg Header*/
    .btn-ver-pdf.fut, .btn-ver-pdf.plan-actividades, .bg-header.fut, .bg-header.plan_actividades_ppp {
        background: linear-gradient(135deg, var(--info-color), #0e7490);
        color: white;
    }

    .btn-ver-pdf.fut:hover, .btn-ver-pdf.plan-actividades:hover {
        background: linear-gradient(135deg, #0e7490, #0c4a6e);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .btn-ver-pdf.carta-presentacion, .bg-header.carta_presentacion {
        background: linear-gradient(135deg, var(--warning-color), #b45309);
        color: white;
    }

    .btn-ver-pdf.carta-presentacion:hover {
        background: linear-gradient(135deg, #b45309, #92400e);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .btn-ver-pdf.carta-aceptacion, .bg-header.carta_aceptacion {
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
    }

    .btn-ver-pdf.carta-aceptacion:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .btn-ver-pdf.constancia, .bg-header.constancia_cumplimiento {
        background: linear-gradient(135deg, var(--rose-color), #be123c);
        color: white;
    }

    .btn-ver-pdf.constancia:hover {
        background: linear-gradient(135deg, #be123c, #9f1239);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        color: white;
        text-decoration: none;
    }

    .btn-ver-pdf.informe-final, .bg-header.informe_final_ppp {
        background: linear-gradient(135deg, var(--indigo-color), #3730a3);
        color: white;
    }

    .btn-ver-pdf.informe-final:hover {
        background: linear-gradient(135deg, #3730a3, #312e81);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        color: white;
        text-decoration: none;
    }
  </style>  
@endpush

@section('content')
<div class="supervision-container">
    <div class="supervision-card fade-in">
        <div class="supervision-card-header">
            <h5 class="supervision-card-title">
                <i class="bi bi-eye"></i>
                Supervisión de Estudiantes en Prácticas
            </h5>
        </div>
        <div class="supervision-card-body">
            @if(Auth::user()->hasAnyRoles([1, 2]))
                <x-data-filter
                    route="supervision"
                    :facultades="$facultades"
                />
            @endif
            <div class="table-container">
                <table class="table" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="align-middle text-center">#</th>
                            <th class="align-middle text-center">Escuela</th>
                            <th class="align-middle text-center">Sección</th>
                            <th class="align-middle text-center">Tipo de Práctica</th>
                            <th class="align-middle text-center">Apellidos y Nombres</th>
                            <th class="align-middle text-center">Área</th>
                            <th class="align-middle text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($personas as $index => $persona)
                            @php
                                // 1. Usa optional() para manejar posibles valores null en asignacion_persona
                                // 2. Usa last() para obtener el último MODELO de la colección 'practicas'
                                $practica = optional($persona->asignacion_persona)->practicas->last();
                            @endphp
                        <tr data-estudiante-id="{{ $persona->id }}">
                            <td class="align-middle text-center">{{ $index + 1 }}</td>
                            <td class="align-middle text-center">{{ $persona->asignacion_persona->seccion_academica->escuela->name }}</td>
                            <td class="align-middle text-center">{{ $persona->asignacion_persona->seccion_academica->seccion }}</td>
                            <td class="align-middle text-center">
                                @if($practica)
                                    <span class="practice-badge">{{ $practica->tipo_practica }}</span>
                                @else
                                    <span class="no-registered">Sin asignar</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <strong>{{ strtoupper($persona->apellidos . ' ' . $persona->nombres) }}</strong>
                            </td>
                            <td class="align-middle text-center">
                                @if($practica && $practica->jefeInmediato)
                                    <span class="area-badge">{{ $practica->jefeInmediato->area ?: 'Área no reg.' }}</span>
                                @else
                                    <span class="no-registered">Sin asignar</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if($practica)
                                <button 
                                    id="btnProceso"
                                    class="btn btn-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalProceso"
                                    data-id-practica="{{ $practica->id }}">
                                    <i class="bi bi-list-check"></i>
                                    Ver
                                </button>
                                @else
                                <span class="no-registered">
                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                    Sin práctica asignada
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if($personas->isEmpty())
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="bi bi-person-x"></i>
                                <p class="mb-0">No se encontraron estudiantes en prácticas.</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Proceso -->
<div class="modal fade" id="modalProceso" tabindex="-1" role="dialog" aria-labelledby="modalProcesoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProcesoLabel">
                    <i class="bi bi-diagram-3"></i>
                    Proceso de Supervisión
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="stepper" class="stepper">
                    <!-- Stepper Items (Generated/Updated by JS) -->
                    <div class="stepper-item" data-stage="1">
                        <div class="stepper-circle">1</div>
                        <span class="stepper-label">Inicio</span>
                    </div>
                    <div class="stepper-item" data-stage="2">
                        <div class="stepper-circle">2</div>
                        <span class="stepper-label">Desarrollo</span>
                    </div>
                    <div class="stepper-item" data-stage="3">
                        <div class="stepper-circle">3</div>
                        <span class="stepper-label">Seguimiento</span>
                    </div>
                    <div class="stepper-item" data-stage="4">
                        <div class="stepper-circle">4</div>
                        <span class="stepper-label">Finalización</span>
                    </div>
                    <div class="stepper-item" data-stage="5">
                        <div class="stepper-circle">5</div>
                        <span class="stepper-label">Evaluación</span>
                    </div>
                </div>

                <!-- Stage Content -->
                <div class="tab-content" id="supervisionTabContent">
                    <!-- Stage 1: Inicio -->
                    <div class="tab-pane fade show active" id="content-stage-1" role="tabpanel">
                        @include('practicas.admin.supervision.supe_E1')
                    </div>

                    <!-- Stage 2: Desarrollo -->
                    <div class="tab-pane fade" id="content-stage-2" role="tabpanel">
                        <div id="etapa2-content">
                            @include('practicas.admin.supervision.supe_E2', ['etapa' => 1])
                        </div>
                        <!-- Internal nav for E2 (Boss details again? It was in the original file) -->
                         <div id="etapa2-jefe" style="display: none;">
                            @include('practicas.admin.supervision.supe_E2', ['etapa' => 3])
                        </div>
                    </div>

                    <!-- Stage 3: Seguimiento -->
                    <div class="tab-pane fade" id="content-stage-3" role="tabpanel">
                        @include('practicas.admin.supervision.supe_E3')
                    </div>

                    <!-- Stage 4: Finalización -->
                    <div class="tab-pane fade" id="content-stage-4" role="tabpanel">
                        @include('practicas.admin.supervision.supe_E4')
                    </div>

                    <!-- Stage 5: Evaluación -->
                    <div class="tab-pane fade" id="content-stage-5" role="tabpanel">
                        @include('practicas.admin.supervision.supe_E5')
                    </div>
                </div>

                <!-- Generic Review Form (Hidden by default, shown via JS) -->
                @include('practicas.admin.supervision.review_form')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')

<script src="{{ asset('js/supervision_practica.js') }}"></script>

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
    @if(session('error'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 4000, // Un poco más de tiempo para errores
            timerProgressBar: true,
        });
    </script>
    @endif
@endpush
