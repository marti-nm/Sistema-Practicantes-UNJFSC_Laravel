@extends('template')
@section('title', 'Gestión de Grupos de Asignatura')
@section('subtitle', 'Administrar grupos de práctica por asignaturas y docentes')

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

    .asignatura-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0;
    }

    /* Card Principal */
    .asignatura-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .asignatura-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .asignatura-card-header {
        background: linear-gradient(135deg, var(--surface-color) 0%, #f8fafc 100%);
        border-bottom: 2px solid var(--border-color);
        padding: 1.5rem 2rem;
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .asignatura-card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .asignatura-card-title {
        font-size: 1.375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-transform: none;
    }

    .asignatura-card-title i {
        color: var(--primary-color);
        font-size: 1.25rem;
    }

    .asignatura-card-body {
        padding: 1.5rem;
    }

    /* Botón crear grupo */
    .btn-crear-grupo {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: var(--shadow-sm);
    }

    .btn-crear-grupo:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: white;
    }

    .btn-crear-grupo i {
        font-size: 1rem;
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
        text-align: center;
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

    /* Badges para datos */
    .id-badge {
        background: linear-gradient(135deg, var(--secondary-color), #475569);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        box-shadow: var(--shadow-sm);
    }

    .docente-name {
        font-weight: 600;
        color: var(--text-primary);
        text-align: left;
    }

    .semestre-badge {
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

    .escuela-badge {
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
    }

    .grupo-name {
        font-weight: 600;
        color: var(--primary-color);
        text-align: left;
    }

    .estado-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .estado-activo {
        background: rgba(5, 150, 105, 0.1);
        color: var(--success-color);
        border: 1px solid rgba(5, 150, 105, 0.2);
    }

    .estado-inactivo {
        background: rgba(220, 38, 38, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(220, 38, 38, 0.2);
    }

    /* Botones de Acción */
    .btn {
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        margin: 0.125rem;
        min-width: 40px;
    }

    .btn-warning {
        background: var(--warning-color);
        color: white;
    }

    .btn-warning:hover {
        background: #b45309;
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
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
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
        font-size: 1.25rem;
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
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 600;
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
        opacity: 0.7;
    }

    /* Alertas modernas */
    .alert {
        border: none;
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        font-size: 0.9rem;
        border-left: 4px solid;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
    }

    .alert-success {
        background: rgba(5, 150, 105, 0.1);
        border-left-color: var(--success-color);
        color: #047857;
    }

    .alert-danger {
        background: rgba(220, 38, 38, 0.1);
        border-left-color: var(--danger-color);
        color: #991b1b;
    }

    .alert-dismissible .close {
        position: absolute;
        top: 0.5rem;
        right: 1rem;
        padding: 0.5rem;
        color: inherit;
        opacity: 0.7;
    }

    .alert-dismissible .close:hover {
        opacity: 1;
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

    /* Mejoras adicionales para integración completa */
    
    /* Hover effects para badges */
    .id-badge:hover,
    .semestre-badge:hover,
    .escuela-badge:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-md);
    }

    /* Estados de badges con transiciones */
    .estado-badge {
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .estado-badge:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    /* Mejoras en nombres */
    .docente-name:hover,
    .grupo-name:hover {
        color: var(--primary-light);
        transform: translateX(2px);
    }

    /* Form controls estándar */
    .form-control:focus {
        border-color: #8db5ff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }


    /* Mejoras en el modal de crear */
    .modal-dialog {
        transition: transform 0.3s ease-out;
    }

    .modal.fade .modal-dialog {
        transform: translateY(-50px);
    }

    .modal.show .modal-dialog {
        transform: translateY(0);
    }

    /* Table responsive mejorada */
    .table-responsive {
        border-radius: 0.75rem;
        overflow: hidden;
    }

    /* Estados hover para las filas */
    .table tbody tr:hover .id-badge,
    .table tbody tr:hover .semestre-badge,
    .table tbody tr:hover .escuela-badge {
        transform: scale(1.05);
    }

    /* Footer del modal mejorado */
    .modal-footer .btn {
        min-width: 120px;
    }

    /* Responsive mejoras adicionales */
    @media (max-width: 576px) {
        .estado-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .id-badge,
        .semestre-badge,
        .escuela-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .btn {
            min-width: 35px;
            padding: 0.375rem 0.5rem;
        }

        .asignatura-card-title {
            font-size: 1.125rem;
        }

        .modal-title {
            font-size: 1.125rem;
        }
    }

    /* Efectos de aparición para elementos */
    .badge,
    .btn {
        transition: all 0.3s ease;
    }

    /* Estados de validación para selects dependientes */
    select.form-control:disabled {
        background-color: #f8fafc;
        color: var(--text-secondary);
        cursor: not-allowed;
    }

    select.form-control:disabled option {
        color: var(--text-secondary);
    }

    /* Mejoras en spacing */
    .form-group:last-child {
        margin-bottom: 0;
    }

    /* Iconos en labels con mejor spacing */
    .form-group label i {
        margin-right: 0.5rem;
        color: var(--primary-color);
        width: 1rem;
        text-align: center;
    }

    /* ...existing styles... */
</style>
@endpush

@section('content')
<div class="asignatura-container">
    @php /*
    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif*/
    @endphp

    <div class="asignatura-card fade-in">
        <div class="asignatura-card-header">
            <h5 class="asignatura-card-title">
                <i class="bi bi-collection"></i>
                Lista de Grupos de Práctica
            </h5>
            <button class="btn-crear-grupo" data-toggle="modal" data-target="#crearGrupoModal">
                <i class="bi bi-plus-circle"></i> 
                Registrar Grupo
            </button>
        </div>
        
        <div class="asignatura-card-body">
            @if(Auth::user()->hasAnyRoles([1, 2]))
            <x-data-filter
                route="asignacion_index"
                :facultades="$facultades"
            />
            @endif
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Facultad</th>
                                <th>Escuela</th>
                                <th>Sección</th>
                                <th>Nombre de grupo</th>
                                <th>Docente</th>
                                <th>Supervisor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grupos_practica as $index => $grupo)
                            <tr>
                                <td>
                                    <span class="grupo-name">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <span class="grupo-name">{{ $grupo->seccion_academica->facultad->name }}</span>
                                </td>
                                <td>
                                    <span class="grupo-name">{{ $grupo->seccion_academica->escuela->name }}</span>
                                </td>
                                <td>
                                    <span class="grupo-name">{{ $grupo->seccion_academica->seccion }}</span>
                                </td>
                                <td class="grupo-name">{{ $grupo->name }}</td>
                                <td class="docente-name">{{ $grupo->docente->persona->nombres }} {{ $grupo->docente->persona->apellidos }}</td>
                                <td class="docente-name">{{ $grupo->supervisor->persona->nombres }} {{ $grupo->supervisor->persona->apellidos }}</td>
                                
                                <td>
                                    <!-- Editar -->
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalEditar{{ $grupo->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <!-- Eliminar -->
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar{{ $grupo->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @if($grupos_practica->isEmpty())
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="bi bi-collection"></i>
                                    <p class="mb-0">No se encontraron grupos de práctica registrados.</p>
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

<!-- Modales para editar y eliminar -->
@foreach ($grupos_practica as $grupo)
<!-- MODAL EDITAR (Refactorizado) -->
<div class="modal fade" id="modalEditar{{ $grupo->id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('grupos.update', $grupo->id) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-pencil-square"></i>
            Editar Grupo
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info" role="alert" style="font-size: 0.85rem;">
                <i class="bi bi-info-circle-fill"></i>
                Solo se permite editar el <strong>nombre del grupo</strong> y el <strong>supervisor</strong>. Para cambios estructurales, se recomienda eliminar y crear un nuevo grupo.
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label><i class="bi bi-building"></i> Escuela</label>
                    <input type="text" class="form-control" value="{{ $grupo->seccion_academica->escuela->name }}" disabled>
                </div>
                <div class="col-md-6 form-group">
                    <label><i class="bi bi-diagram-3"></i> Sección</label>
                    <input type="text" class="form-control" value="{{ $grupo->seccion_academica->seccion }}" disabled>
                </div>
            </div>

            <div class="form-group">
                <label><i class="bi bi-person-badge"></i> Docente Titular</label>
                <input type="text" class="form-control" value="{{ $grupo->docente->persona->nombres }} {{ $grupo->docente->persona->apellidos }}" disabled>
            </div>

            <hr>

            <div class="form-group">
                <label for="nombre_grupo_{{ $grupo->id }}">
                    <i class="bi bi-collection"></i> Nombre del Grupo (Editable)
                </label>
                <input type="text" id="nombre_grupo_{{ $grupo->id }}" name="nombre_grupo" value="{{ $grupo->name }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="dsupervisor_{{ $grupo->id }}">
                    <i class="bi bi-person-video3"></i> Supervisor (Editable)
                </label>
                <select name="dsupervisor" id="dsupervisor_{{ $grupo->id }}" class="form-control" required data-sa-id="{{ $grupo->id_sa }}" data-current-supervisor="{{ $grupo->id_supervisor }}">
                    <option value="">Cargando supervisores...</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i>
            Guardar cambios
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="bi bi-x-circle"></i>
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL ELIMINAR -->
<div class="modal fade" id="modalEliminar{{ $grupo->id }}" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-exclamation-triangle"></i>
            Eliminar Grupo
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="text-center">
            <i class="bi bi-trash text-danger" style="font-size: 3rem;"></i>
            <p class="mt-3">¿Estás seguro de eliminar el grupo <strong>{{ $grupo->nombre_grupo }}</strong>?</p>
            <small class="text-muted">Esta acción no se puede deshacer</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash"></i>
            Eliminar
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="bi bi-x-circle"></i>
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

<!-- MODAL CREAR GRUPO -->
<div class="modal fade" id="crearGrupoModal" tabindex="-1" aria-labelledby="crearGrupoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('grupos.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="crearGrupoLabel">
            <i class="bi bi-plus-circle"></i>
            Registrar Grupo de Práctica
          </h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Facultad -->
          <div class="form-group">
              <div class="row g-3">
                <!-- Facultad -->
                <div class="col-md-4">
                    <label for="crear-facultad" class="form-label">Facultad</label>
                    <select class="form-control" id="crear-facultad" name="facultad" required>
                        @if($ap->id_rol == 3)
                            <option value="{{ $ap->seccion_academica->id_facultad }}" selected>{{ $ap->seccion_academica->facultad->name ?? 'N/A' }}</option>
                        @else
                        <option value="">Seleccione una facultad</option>
                            @foreach($facultades as $facultad)
                                @foreach($facultades as $fac)
                                    <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                @endforeach
                            @endforeach
                        @endif
                    </select>
                </div>
                <!-- Escuela -->
                <div class="col-md-4">
                    <label for="crear-escuela" class="form-label">Escuela</label>
                    @if ($ap->id_rol == 3)
                        <select class="form-control" id="crear-escuela" name="escuela" required>
                            <option value="{{ $ap->seccion_academica->id_escuela }}" selected>{{ $ap->seccion_academica->escuela->name ?? 'N/A' }}</option>
                        </select>
                    @else
                        <select class="form-control" id="crear-escuela" name="escuela" required disabled>
                            <option value="">Seleccione una escuela</option>
                        </select>
                    @endif
                </div>
                <!-- Seccion -->
                <div class="col-md-4">
                    <label for="crear-seccion" class="form-label"><i class="bi bi-person-badge"></i> Sección</label>
                    @if (Auth::user()->getRolId() == 3)
                        <select class="form-control" id="crear-seccion" name="seccion" required>
                            <option value="{{ $ap->id_sa }}" selected>{{ $ap->seccion_academica->seccion ?? 'N/A' }}</option>
                        </select>
                    @else
                        <select class="form-control" id="crear-seccion" name="seccion" disabled>
                            <option value="">Seleccione una sección</option>
                        </select>
                    @endif
                </div>
            </div>
          </div>
          
          <!-- Docente -->
          <div class="form-group">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="dtitular">
                    <i class="bi bi-person-badge"></i>
                    Docente
                    </label>
                    @if (Auth::user()->getRolId() == 3)
                        <select class="form-control" id="dtitular" name="dtitular" required>
                            <option value="{{ $ap->id }}" selected>{{ $ap->persona->nombres }} {{ $ap->persona->apellidos }}</option>
                        </select>
                    @else
                        <select name="dtitular" id="dtitular" class="form-control" required disabled>
                            <option value="">Seleccione un docente</option>
                        </select>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="dsupervisor">
                    <i class="bi bi-person-badge"></i>
                    Supervisor (Solo docentes acreditados)
                    </label>
                    <select name="dsupervisor" id="dsupervisor" class="form-control" required disabled>
                        <option value="">Seleccione un supervisor disponible</option>
                    </select>
                </div>
            </div>
          </div>
          <!-- Nombre del grupo -->
          <div class="form-group">
            <label for="nombre_grupo">
              <i class="bi bi-collection"></i>
              Nombre del Grupo
            </label>
            <input type="text" name="nombre_grupo" class="form-control" placeholder="Ej: Grupo A - Prácticas 2024" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i>
            Guardar
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="bi bi-x-circle"></i>
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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
    timer: 2000,
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
    timer: 2000,
    timerProgressBar: true,
});
</script>
@endif

<script>
    // Filtro Facultad - Escuela - Seccion
    document.addEventListener("DOMContentLoaded", function () {
        const facultadSelect = document.getElementById('crear-facultad');
        const escuelaSelect = document.getElementById('crear-escuela');
        const seccionSelect = document.getElementById('crear-seccion');
        const dtitularSelect = document.getElementById('dtitular');
        const dsupervisorSelect = document.querySelector('#crearGrupoModal #dsupervisor');
        const currentUserRolId = {{ $ap->id_rol ?? 'null' }}; // Obtener el ID del rol del usuario actual
        const semestreActivoId = {{ session('semestre_actual_id') ?? 'null' }};

        facultadSelect.addEventListener('change', async function () {
            const facultadId = this.value;
            // Reset dependants
            escuelaSelect.innerHTML = '<option value="">Seleccione una escuela</option>';
            seccionSelect.innerHTML = '<option value="">Seleccione una sección</option>';

            dtitularSelect.innerHTML = '<option value="">Seleccione un docente</option>';
            dsupervisorSelect.innerHTML = '<option value="">Seleccione un supervisor disponible</option>';
            dtitularSelect.disabled = true;
            dsupervisorSelect.disabled = true;
            escuelaSelect.disabled = true;
            seccionSelect.disabled = true;
            if (!facultadId) {
                return;
            }

            escuelaSelect.innerHTML = '<option value="">Cargando...</option>';
            const response = await fetch(`/api/escuelas/${facultadId}`);
            const data = await response.json();
            if (response.ok) {
                let options = '<option value="">Seleccione una escuela</option>';
                data.forEach(e => {
                    options += `<option value="${e.id}">${e.name}</option>`;
                });
                escuelaSelect.innerHTML = options;
                escuelaSelect.disabled = false;
            } else {
                escuelaSelect.innerHTML = '<option value="">Error al cargar</option>';
            }
        });

        escuelaSelect.addEventListener('change', async function () {
            const escuelaId = this.value;
            seccionSelect.innerHTML = '<option value="">Seleccione una sección</option>';
            seccionSelect.disabled = true;

            dtitularSelect.innerHTML = '<option value="">Seleccione un docente</option>';
            dsupervisorSelect.innerHTML = '<option value="">Seleccione un supervisor disponible</option>';
            dtitularSelect.disabled = true;
            dsupervisorSelect.disabled = true;

            if (!escuelaId || !semestreActivoId) {
                return;
            }

            seccionSelect.innerHTML = '<option value="">Cargando...</option>';
            const response = await fetch(`/api/secciones/${escuelaId}/${semestreActivoId}`) // <-- Usar semestre activo
            const data = await response.json();
            if (response.ok) {
                let options = '<option value="">Seleccione una sección</option>';
                data.forEach(d => {
                    options += `<option value="${d.id}">${d.name}</option>`;
                });
                seccionSelect.innerHTML = options;
                seccionSelect.disabled = false;
                console.log('Secciones cargadas ', data)
            } else {
                seccionSelect.innerHTML = '<option value="">Error al cargar</option>';
            }
        });

        // Función para cargar Docentes y Supervisores basada en la sección
        async function loadDocentesAndSupervisoresForSection(seccionId) {
            // Cargar Supervisores
            dsupervisorSelect.innerHTML = '<option value="">Cargando...</option>';
            dsupervisorSelect.disabled = true;
            const response = await fetch(`/api/docentes-supervisores/${seccionId}`)
            const data = await response.json();
            if (response.ok) {
                let options = '<option value="">Seleccione un supervisor</option>';
                data.forEach(d => {
                    options += `<option value="${d.people}">${d.nombres} ${d.apellidos}</option>`;
                });
                dsupervisorSelect.innerHTML = options;
                dsupervisorSelect.disabled = false;
            } else {
                dsupervisorSelect.innerHTML = '<option value="">Error al cargar</option>';
            }

            if (currentUserRolId !== 3) {
                dtitularSelect.innerHTML = '<option value="">Cargando...</option>';
                dtitularSelect.disabled = true;
                console.log('Sección seleccionada ', seccionId)
                const response = await fetch(`/api/docentes-titulares/${seccionId}`)
                const data = await response.json();
                console.log('Data ', data);
                if(response.ok) {
                    let options = '<option value="">Seleccione un docente</option>';
                    data.forEach(d => {
                        options += `<option value="${d.people}">${d.nombres} ${d.apellidos}</option>`;
                    });
                    dtitularSelect.innerHTML = options;
                    dtitularSelect.disabled = false;
                } else {
                    dtitularSelect.innerHTML = '<option value="">Error al cargar</option>';
                }
            } else {
                // Si el usuario actual es rol 3, el docente titular ya está pre-seleccionado en Blade, solo aseguramos que esté habilitado.
                dtitularSelect.disabled = false;
            }
        }

        seccionSelect.addEventListener('change', function () {
            const seccionId = this.value;
            loadDocentesAndSupervisoresForSection(seccionId);
        });

        // Disparar la carga cuando el modal se abre, si la sección ya está pre-seleccionada (para rol 3)
        $('#crearGrupoModal').on('show.bs.modal', function () {
            if (currentUserRolId === 3) {
                const preSelectedSeccionId = seccionSelect.value;
                if (preSelectedSeccionId) {
                    loadDocentesAndSupervisoresForSection(preSelectedSeccionId);
                }
            }
        });
    });

    // Carga dinámica de supervisores en el modal de edición
    document.addEventListener('DOMContentLoaded', function() {
        $('.modal[id^="modalEditar"]').on('show.bs.modal', async function (event) {
            const modal = $(this);
            const supervisorSelect = modal.find('select[name="dsupervisor"]');
            const saId = supervisorSelect.data('sa-id');
            const currentSupervisorId = supervisorSelect.data('current-supervisor');
            const rolSupervisor = 4;

            supervisorSelect.html('<option value="">Cargando...</option>').prop('disabled', true);

            if (!saId) {
                supervisorSelect.html('<option value="">Error: No se encontró la sección</option>');
                return;
            }

            const response = await fetch(`/api/docentes-supervisores/${saId}`)
            const data = await response.json();
            if(response.ok) {
                let options = '<option value="">Seleccione un supervisor</option>';
                data.forEach(d => {
                    const isSelected = d.people == currentSupervisorId ? 'selected' : '';
                    options += `<option value="${d.people}" ${isSelected}>${d.nombres} ${d.apellidos}</option>`;
                });
                supervisorSelect.html(options).prop('disabled', false);
            } else {
                supervisorSelect.html('<option value="">Error al cargar supervisores</option>');
            }
        });
    });
</script>
@endpush
