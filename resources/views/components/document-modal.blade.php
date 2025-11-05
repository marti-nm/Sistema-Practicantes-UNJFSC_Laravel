@props([
    'item',
    'modalId',
    'title',
    'icon',
    'bgClass',
    'estadoKey',      // La clave del estado en el modelo (e.g., 'estado_cl', 'estado_horario')
    'rutaKey',        // La clave de la ruta en el modelo (e.g., 'ruta_cl', 'ruta_horario')
    'updateRoute',    // La ruta de actualización
    'latestArchivo' => null, // Para el caso de Resolución, que usa el modelo Archivo
    'historialArchivos' => null, // Para el historial, como en Resolución
    'isArchivoModel' => false, // Bandera para saber si usa el modelo Archivo (Resolución)
])

@php
    // Determinar las variables clave basadas en si es un modelo Archivo o el modelo Acreditacion
    $ruta = $isArchivoModel ? ($latestArchivo->ruta ?? null) : ($item->$rutaKey ?? null);
    $estado = $isArchivoModel ? ($latestArchivo->estado_archivo ?? '') : ($item->$estadoKey ?? '');
    $idToUpdate = $isArchivoModel ? ($latestArchivo->id ?? $item->id) : $item->id;
    $estadoFieldName = $isArchivoModel ? 'estado' : $estadoKey;
@endphp

<div class="modal fade" id="{{ $modalId }}{{ $item->id }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header btn-{{ $bgClass }} text-white">
                <h5 class="modal-title" id="{{ $modalId }}Label{{ $item->id }}">
                    <i class="bi bi-{{ $icon }}"></i>
                    {{ $title }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @if($estado == 'Aprobado')
                    {{-- Bloque de estado Completo --}}
                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Estado:</strong> Completo
                        </div>
                        @if($ruta)
                            <a href="{{ asset($ruta) }}" class="btn btn-outline-success" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                            </a>
                        @else
                            <div class="alert alert-warning m-0 p-2 rounded">
                                <i class="bi bi-exclamation-triangle"></i> Sin PDF disponible
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Bloque de formulario de corrección/revisión --}}
                    @if($estado != 'Corregir')
                        <form action="{{ $latestArchivo ? route($updateRoute, $latestArchivo->id) : route($updateRoute, $item->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="estado-{{ $modalId }}-{{ $item->id }}" class="font-weight-bold">
                                    <i class="bi bi-gear"></i> Estado del Documento
                                </label>
                                <select name="{{ $estadoFieldName }}" id="estado-{{ $modalId }}-{{ $item->id }}" class="form-control">
                                    {{-- Opciones para Carga Lectiva y Horario (estado_cl/estado_horario) --}}
                                    @if (!$isArchivoModel)
                                        <option value="En proceso" {{ ($estado ?? '') == 'En proceso' ? 'selected' : '' }}>En proceso</option>
                                    @endif

                                    {{-- Opciones para Resolución (estado_archivo) --}}
                                    @if ($isArchivoModel)
                                        <option value="Enviado" {{ ($estado ?? '') == 'Enviado' ? 'selected' : '' }}>Enviado</option>
                                    @endif

                                    <option value="Corregir" {{ $estado == 'Corregir' ? 'selected' : '' }}>Corregir</option>
                                    <option value="Aprobado" {{ $estado == 'Aprobar' ? 'selected' : '' }}>Completo</option>
                                </select>
                            </div>

                            @if (!$isArchivoModel || $isArchivoModel)
                                <div class="form-group mt-3">
                                    <label for="comentario-{{ $modalId }}-{{ $item->id }}" class="font-weight-bold">
                                        <i class="bi bi-chat-dots"></i> Comentario (Opcional)
                                    </label>
                                    {{-- Nota: El campo de comentario debería ser 'comentario_admin' si el historial lo usa, o 'comentario' si lo usa el controlador --}}
                                    <textarea name="comentario" id="comentario-{{ $modalId }}-{{ $item->id }}" class="form-control" rows="3"></textarea>
                                </div>
                            @endif

                            <div class="document-actions mt-3">
                                <a href="{{ asset($ruta) }}" class="btn btn-outline-{{$bgClass}}" target="_blank">
                                    <i class="bi bi-eye"></i> Ver PDF
                                </a>
                                <button type="submit" class="btn btn-{{$bgClass}}">
                                    <i class="bi bi-save"></i> Guardar cambios
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-file-earmark-x" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2"><strong>Sin PDF disponible</strong></p>
                            <small>El docente aún no ha subido {{ strtolower($title) }}.</small>
                        </div>
                    @endif
                @endif

                {{-- Historial (Solo para Resolución) --}}
                @if($historialArchivos)
                    <h6 class="mt-4">Documentos enviados (Historial)</h6>
                    <ul class="list-group list-group-flush">
                        @foreach ($historialArchivos->sortByDesc('created_at') as $historial)
                            @php
                                $badge_class = match($historial->estado_archivo) {
                                    'Aprobado' => 'success',
                                    'Corregir' => 'danger',
                                    default => 'warning',
                                };
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">Versión:</span> {{ $historial->created_at->format('d/m/Y H:i') }}
                                    <span class="badge bg-{{ $badge_class }} ms-3">{{ $historial->estado_archivo }}</span>
                                    @if($historial->comentario)
                                        <small class="text-danger d-block fst-italic">Corrección: {{ $historial->comentario }}</small>
                                    @endif
                                </div>
                                <a href="{{ asset($historial->ruta) }}" class="btn btn-sm btn-outline-{{ $badge_class }}" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> Ver
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>