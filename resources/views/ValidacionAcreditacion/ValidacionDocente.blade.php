@extends('template')
@section('title', 'Acreditación del Docente')
@section('subtitle', 'Gestionar y validar documentos académicos del docente titular')

@section('content')
    <div class="app-container">
        <div class="app-card fade-in">
            <div class="app-card-header">
                <h5 class="app-card-title">
                    <i class="bi bi-clipboard-check"></i>
                    Lista de {{ $msj }} para Acreditar
                </h5>
            </div>
            <div class="app-card-body">
                @if(auth()->user()->getRolId() == 1)
                <x-data-filter
                route="docente"
                :facultades="$facultades"
                />
                @endif
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Docente</th>
                                    <th>Semestre</th>
                                    <th>Escuela</th>
                                    <th>C. Lectiva</th>
                                    <th>Horario</th>
                                    @if($option == 2)
                                    <th>Resolucion</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($acreditar as $index => $item)
                                    @php
                                        $acreditacion = $item->asignacion_persona->acreditacion->first();
                                        $archivosPorTipo = $acreditacion ? $acreditacion->archivos->groupBy('tipo') : collect();

                                        $getLatest = function ($tipo) use ($archivosPorTipo) {
                                            $history = $archivosPorTipo->get($tipo);
                                            return $history ? $history->sortByDesc('created_at')->first() : null;
                                        };

                                        $getBgColor = function ($estado) {
                                            switch ($estado) {
                                                case 'Aprobado':
                                                    return 'success';
                                                case 'Enviado':
                                                    return 'warning';
                                                case 'Corregir':
                                                    return 'danger';
                                                default:
                                                    return 'secondary';
                                            }
                                        };
 
                                        $latestCL = $getLatest('carga_lectiva');
                                        $estadoCL = $latestCL ? $latestCL->estado_archivo : 'Falta';
                                        $bg_cl = $getBgColor($estadoCL);
 
                                        $latestHorario = $getLatest('horario');
                                        $estadoHorario = $latestHorario ? $latestHorario->estado_archivo : 'Falta';
                                        $bg_horario = $getBgColor($estadoHorario);
 
                                        $bg_resolucion = 'secondary';
                                        if ($item->asignacion_persona->id_rol == 4) {
                                            $latestResolucion = $getLatest('resolucion');
                                            $estadoResolucion = $latestResolucion ? $latestResolucion->estado_archivo : 'Falta';
                                            $bg_resolucion = $getBgColor($estadoResolucion);
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $index+1 }}</td>
                                        <td>{{ $item->apellidos }}, {{ $item->nombres }}</td>
                                        <td>{{ $item->asignacion_persona->semestre->codigo }}</td>
                                        <td>{{ $item->asignacion_persona->seccion_academica->escuela->name }}</td>
                                        <td>
                                            <!--<button type="button" class="btn btn-{{ $bg_cl }}" data-toggle="modal" data-target="#modalCLectiva{{ $item->id }}">
                                                <i class="bi bi-file-earmark-text"></i>
                                                Carga Lectiva
                                            </button>-->
                                            <button class="btn btn-sm btn-{{ $bg_cl }} btn-validacion-docente"
                                                data-id-a="{{ $acreditacion->id ?? '' }}"
                                                data-type-file="carga_lectiva"><i class="bi bi-file-earmark-text"></i>Carga Lectiva</button>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-{{ $bg_horario }} btn-validacion-docente"
                                                data-id-a="{{ $acreditacion->id ?? '' }}"
                                                data-type-file="horario"><i class="bi bi-file-earmark-text"></i>Horario de Clases</button>
                                        </td>
                                        @if($item->asignacion_persona->id_rol == 4)
                                        <td>
                                            <button class="btn btn-sm btn-{{ $bg_resolucion }} btn-validacion-docente"
                                                data-id-a="{{ $acreditacion->id ?? '' }}"
                                                data-type-file="resolucion"><i class="bi bi-file-earmark-text"></i>Resolución</button>  
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="modalValidacionDocente" tabindex="-1" role="dialog" aria-labelledby="modalValidacionDocenteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalValidacionDocenteLabel">Validación de Docente</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="approved-file-container" class="">
                    <div class="alert alert-success d-flex justify-content-between align-items-center" style="display: none;">
                        <div>
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Estado:</strong> Completo
                        </div>
                        <a id="approved-file-link" href="#" class="btn btn-outline-success file-link" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                        </a>
                    </div>
                </div>
                <div id="not-file-container" class="alert alert-warning text-center" style="display: none;">
                    <i class="bi bi-file-earmark-x" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2"><strong>Documento no disponible para revisión</strong></p>
                    <small>El docente debe enviar o corregir el archivo.</small>
                </div>
                <form id="formValidacionDocente" action="{{ route('actualizar.estado.archivo') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="tipo" id="tipo">
                    <input type="hidden" name="acreditacion" id="acreditacion">
                    <div class="col-md-12 d-flex flex-column">
                        <label class="font-weight-bold"><i class="bi bi-paperclip"></i> Archivo enviado:</label>
                        <div class="alert alert-light p-2 d-flex justify-content-between align-items-center border flex-grow-1">
                            <span class="text-truncate"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Anexo_7_Estudiante.pdf</span>
                            <a id="file-send-link" href="#" class="btn btn-sm btn-outline-primary flex-shrink-0 ms-2 file-link" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Ver</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="estado"><i class="bi bi-gear"></i> Estado del Documento</label>
                        <select class="form-control" id="estado" name="estado">
                            <option value="">Seleccione un estado</option>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Corregir">Corregir</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="comentario"><i class="bi bi-chat-dots"></i> Comentario (Requerido si se marca para corregir)</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="3"></textarea>
                    </div>
                </form>
                <h6 class="mt-4">Documentos enviados (Historial)</h6>
                <ul id="document-history" class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">Versión:</span> 23-10-2023
                            <span class="badge bg-danger ms-3">Corregir</span>
                        </div>
                        <a href="#" class="btn btn-outline-success" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                        </a>
                    </li>
                </ul>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" id="btnGuardarValidacionDocente" form="formValidacionDocente">Guardar</button>
            </div>
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
        timer: 3000,
        timerProgressBar: true,
    });
</script>
@endif

<script>
    const MODAL_SELECTOR = '#modalValidacionDocente';
    const modalElement = document.querySelector(MODAL_SELECTOR);
    const myModal = new bootstrap.Modal(modalElement);
    const fileButtons = document.querySelectorAll('.btn-validacion-docente');
    fileButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const ID_ACR = this.getAttribute('data-id-a');
            const typeFile = this.getAttribute('data-type-file');
            console.log(ID_ACR, typeFile);
            // Crear un ruta API para obtener todos los archivos de un tipo, de la tabla archivos buscado por id de acreditacion
            // El ultimo archivo enviado del tipo se evalua el estado para mostrar el modal, el resto
            try {
                const response = await fetch(`/api/acreditacion/archivos/${ID_ACR}/${typeFile}`);
                const data = await response.json();
                console.log(data);

                const approvedFileContainer = document.getElementById('approved-file-container');
                const notFileContainer = document.getElementById('not-file-container');
                const formValidacionDocente = document.getElementById('formValidacionDocente');

                const historyList = document.getElementById('document-history');

                // Limpiar historial
                historyList.innerHTML = '';
                approvedFileContainer.style.display = 'none';
                notFileContainer.style.display = 'none';
                formValidacionDocente.style.display = 'none';
                
                if (data.length > 0) {
                    const ldata = data[0];
                    console.log(ldata);

                    if(ldata.estado_archivo === 'Aprobado') {
                        approvedFileContainer.style.display = 'block';
                        formValidacionDocente.style.display = 'none';
                        // class file-link
                        const fileLink = document.querySelector('.file-link');
                        fileLink.href = ldata.ruta;
                    } else if(ldata.estado_archivo === 'Corregir') {
                        notFileContainer.style.display = 'block';
                        formValidacionDocente.style.display = 'none';

                        /*document.getElementById('id').value = ldata.id;
                        document.getElementById('tipo').value = ldata.tipo;
                        document.getElementById('acreditacion').value = ID_ACR;*/
                    } else if(ldata.estado_archivo === 'Enviado') {
                        console.log('Enviado');
                        notFileContainer.style.display = 'none';
                        formValidacionDocente.style.display = 'block';
                        approvedFileContainer.style.display = 'none';
                        const fileSendLink = document.querySelector('#file-send-link');
                        fileSendLink.href = ldata.ruta;

                        document.getElementById('id').value = ldata.id;
                        document.getElementById('tipo').value = ldata.tipo;
                        document.getElementById('acreditacion').value = ID_ACR;
                    }
                }else{
                    notFileContainer.style.display = 'block';
                }
                myModal.show();
            } catch (error) {
                console.log(error);
            }
        });
    });
</script>
@endpush