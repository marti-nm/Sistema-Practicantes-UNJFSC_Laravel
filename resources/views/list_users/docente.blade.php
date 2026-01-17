@extends('template')
@section('title', 'Gestión de Docentes')
@section('subtitle', 'Administrar y visualizar información de docentes')

@section('content')
<div class="docentes-container">
    <div class="docentes-card fade-in">
        <div class="docentes-card-header d-flex align-items-center justify-content-between">
            <h5 class="docentes-card-title">
                <i class="bi bi-mortarboard"></i>
                Lista de Docentes
            </h5>
        </div>
        <div class="docentes-card-body">
            <x-data-filter
                route="docente"
                :facultades="$facultades"
            />
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
                                <td>{{ strtoupper($persona->apellidos . ' ' . $persona->nombres) . ' - ' . $persona->asignacion_persona->id }}</td>
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
                                    <button type="button" class="btn btn-danger btn-disabled-ap" 
                                        data-id-ap="{{ $persona->asignacion_persona->id }}"
                                        data-id-sa="{{ $persona->asignacion_persona->seccion_academica->id }}"
                                        data-state-ap="{{ $persona->asignacion_persona->state }}"
                                        data-nombre-ap="{{ $persona->apellidos . ' ' . $persona->nombres }}"
                                        data-email-ap="{{ $persona->correo_inst }}">
                                    <i class="bi bi-person-x-fill"></i>
                                    </button>
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

@if(session('error'))
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    </script>
@endif
@endpush

@push('js')
<script>
    // Pequeño ajuste para que el botón de añadir use los atributos correctos de Bootstrap 4
    document.querySelector('[data-bs-target="#modalAgregarDocente"]').setAttribute('data-toggle', 'modal');
    document.querySelector('[data-bs-target="#modalAgregarDocente"]').setAttribute('data-target', '#modalAgregarDocente');
</script>
@endpush
