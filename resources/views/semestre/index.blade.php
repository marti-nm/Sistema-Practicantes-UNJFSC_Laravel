@extends('template')

@section('title', 'Gestión de Semestres')

{{-- CSS DataTables --}}
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush

@section('content')

<div class="container mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary text-uppercase">Gestión de Semestres</h6>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="tablaSemestres" class="table table-bordered table-hover">
                    <!-- Agregar otro campo estado del semestres, activo (1)/finalizado (0), de la bd del campo es_actual, con el JS -->
                    <!-- si esta finalizado, no se puede editar ni eliminar -->
                    <thead class="table-dark text-center">
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Ciclo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($semestres as $semestre)
                        <tr>
                            <td>{{ $semestre->id }}</td>
                            <td>{{ $semestre->codigo }}</td>
                            <td>{{ $semestre->ciclo }}</td>
                            <td>
                                @if($semestre->state == 1)
                                    <span class="badge bg-success">Activo</span>
                                @elseif($semestre->state == 2)
                                    <span class="badge bg-secondary">Registrado</span>
                                @else
                                    <span class="badge bg-secondary">Finalizado</span>
                                @endif
                            </td>
                            <td>
                                @if($semestre->state == 1)
                                    {{-- Acciones para Semestre ACTIVO --}}
                                    
                                    {{-- Botón Finalizar --}}
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalFinalizar-{{ $semestre->id }}" title="Finalizar Semestre">
                                        Finalizar
                                    </button>

                                    {{-- Botón Editar --}}
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar-{{ $semestre->id }}" title="Editar Ciclo">
                                        <i class="bi bi-pencil-square"></i> 
                                    </button>

                                    {{-- Botón Retroceder --}}
                                    <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#modalRetroceder-{{ $semestre->id }}" title="Retroceder al anterior">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                
                                @elseif($semestre->state == 0)
                                    {{-- Acciones para Semestre FINALIZADO --}}
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalVisualizar-{{ $semestre->id }}" title="Ver Detalle">
                                        <i class="bi bi-eye"></i> Detalle
                                    </button>
                                @else
                                    {{-- Estado 2 u otros --}}
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            {{-- Columna Acciones extra borrada, se unificó en la anterior o se usa esta estructura --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Nuevo Semestre --}}
<div class="modal fade" id="modalNuevoSemestre" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('semestre.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Semestre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Código</label>
                    <input type="text" name="codigo" class="form-control" required placeholder="Ej: 2024-1">
                </div>
                <div class="form-group mt-2">
                    <label>Ciclo</label>
                    <input type="text" name="ciclo" class="form-control" required placeholder="Ej: IX Ciclo">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modales por Semestre --}}
@foreach($semestres as $semestre)

{{-- Modal Visualizar (Detalle) --}}
<div class="modal fade" id="modalVisualizar-{{ $semestre->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle Semestre {{ $semestre->codigo }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Código:</strong> {{ $semestre->codigo }}</p>
                <p><strong>Ciclo:</strong> {{ $semestre->ciclo }}</p>
                <p><strong>Estado:</strong> {{ $semestre->state == 0 ? 'Finalizado' : 'Activo' }}</p>
                <p><strong>Fecha Creación:</strong> {{ $semestre->date_create }}</p>
                <p><strong>Fecha Actualización:</strong> {{ $semestre->date_update }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Editar --}}
<div class="modal fade" id="modalEditar-{{ $semestre->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('semestre.update', $semestre->id) }}" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Editar Semestre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Código</label>
                    <input type="text" name="codigo" class="form-control" value="{{ $semestre->codigo }}" {{ $semestre->state == 1 ? 'readonly' : '' }} required>
                    @if($semestre->state == 1)
                        <small class="text-muted">El código no se puede editar en un semestre activo.</small>
                    @endif
                </div>
                <div class="form-group mt-2">
                    <label>Ciclo</label>
                    <input type="text" name="ciclo" class="form-control" value="{{ $semestre->ciclo }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Finalizar --}}
@if($semestre->state == 1)
<div class="modal fade" id="modalFinalizar-{{ $semestre->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('semestre.finalizar', $semestre->id) }}" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Finalizar Semestre</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i> Al finalizar este semestre, <strong>se creará automáticamente el siguiente</strong> y este pasará a estado inactivo.
                </div>
                <p>¿Confirma finalizar el semestre <strong>{{ $semestre->codigo }}</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Confirmar Finalización</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Retroceder --}}
<div class="modal fade" id="modalRetroceder-{{ $semestre->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('semestre.retroceder', $semestre->id) }}" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Retroceder Semestre</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-octagon-fill"></i> <strong>¡Acción Destructiva!</strong>
                </div>
                <p>Esta acción eliminará el semestre actual (<strong>{{ $semestre->codigo }}</strong>) y reactivará el semestre anterior finalizado.</p>
                <p><strong>Requisitos:</strong></p>
                <ul>
                    <li>No deben existir asignaciones ni registros vinculados a este semestre.</li>
                </ul>
                <p>¿Está seguro de proceder?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-dark">Confirmar Retroceso</button>
            </div>
        </form>
    </div>
</div>
@endif

@endforeach

@endsection



{{-- JS DataTables --}}
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
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaSemestres').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first":      "Primero",
                "last":       "Último",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
        }
    });
});
</script>
@endpush
