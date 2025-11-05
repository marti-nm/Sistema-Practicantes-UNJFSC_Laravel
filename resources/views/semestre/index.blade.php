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
            {{-- Botón NUEVO SEMESTRE --}}
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalNuevoSemestre">
                <i class="bi bi-plus-circle"></i> Nuevo Semestre
            </button>

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
                                @if($semestre->es_actual == 1)
                                    <span class="badge bg-success">Activo</span>
                                    <button class="btn btn-sm btn-outline-danger ms-2" data-bs-toggle="modal" data-bs-target="#modalFinalizar-{{ $semestre->id }}">
                                        Finalizar
                                    </button>
                                @else
                                    <span class="badge bg-secondary">Finalizado</span>
                                @endif
                            </td>
                            <td>
                                @if ($semestre->es_actual == 1)
                                    {{-- Botón Editar --}}
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar-{{ $semestre->id }}">
                                        <i class="bi bi-pencil-square"></i> 
                                    </button>

                                    {{-- Botón Eliminar --}}
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminar-{{ $semestre->id }}">
                                        <i class="bi bi-trash"></i> 
                                    </button>
                                @else
                                    {{-- Agregar boton que diga visualizar o revisar --}}
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalVisualizar-{{ $semestre->id }}">
                                        <i class="bi bi-eye"></i> 
                                    </button>
                                @endif
                                
                            </td>
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
                    <input type="text" name="codigo" class="form-control" required>
                </div>
                <div class="form-group mt-2">
                    <label>Ciclo</label>
                    <input type="text" name="ciclo" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modales Editar y Eliminar --}}
@foreach($semestres as $semestre)
{{-- Modal Editar --}}
<div class="modal fade" id="modalEditar-{{ $semestre->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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
                    <input type="text" name="codigo" class="form-control" value="{{ $semestre->codigo }}" required>
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

{{-- Modal Eliminar --}}
<div class="modal fade" id="modalEliminar-{{ $semestre->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form method="POST" action="{{ route('semestre.destroy', $semestre->id) }}" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Semestre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de eliminar el semestre <strong>{{ $semestre->codigo }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Confirmar</button>
            </div>
        </form>
    </div>
</div>
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
