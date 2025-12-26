@php
    //$supervisor = $persona->gruposEstudiante->supervisor;
    //$docente = $persona->gruposEstudiante->grupo->docente;
@endphp

@push('css')
  <style>
    .stage-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
    }

    .stage-icon.company {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .stage-icon.supervisor {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

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
  </style>  
@endpush

<div class="container-fluid practice-development-view">
    <div class="container">
        <!-- <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="section-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Detalles de la Práctica Desarrollo
                </h3>
                <button class="btn btn-outline-secondary btn-sm" onclick="goHome()">
                    <i class="bi bi-arrow-left me-1"></i>
                    Volver al Inicio
                </button>
        </div> -->
        <h2 class="fs-5 fw-semibold text-dark mb-4">
            Fase de Prácticas: <span id="current-stage-title">Segunda Etapa - Documentación</span>
        </h2>

        <div class="bg-white p-4 rounded-3 shadow-sm mb-5">
            <div id="stepper" class="stepper">
                @php
                    $stageNames = ['Registro','Documentación','Doc. de Informes','Ejecución','Finalización'];
                    $maxStage = isset($practicas->state) ? min(intval($practicas->state), 5) : 1;
                @endphp

                @for ($k = 1; $k <= 5; $k++)
                    @php
                        if ($k < $maxStage) {
                            $cls = 'stepper-item completed';
                        } elseif ($k == $maxStage) {
                            $cls = 'stepper-item current';
                        } else {
                            $cls = 'stepper-item locked';
                        }
                    @endphp
                    <div class="{{ $cls }}" data-stage="{{ $k }}" @if($k <= $maxStage) onclick="navigateToStage({{ $k }})" @endif>
                        <div class="stepper-circle">
                            @if($k < $maxStage)
                                <i class="fas fa-check"></i>
                            @elseif($k == $maxStage)
                                {{ $k }}
                            @else
                                <i class="fas fa-lock"></i>
                            @endif
                        </div>
                        <span class="stepper-label">{{ $stageNames[$k-1] }}</span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Incluir los partials desbloqueados y envolverlos para mostrar/ocultar por JS --}}
        @for ($i = 1; $i <= $maxStage; $i++)
            <div id="stage-content-{{ $i }}" class="stage-content" style="display: {{ $i == $maxStage ? 'block' : 'none' }};">
                @includeIf('practicas.estudiante.desarrollo.est_des_'.$i)
            </div>
        @endfor

        @if ($practicas->state >= 5 && ($practicas->estado_proceso ?? '') === 'completo')
            <div class="alert alert-success mt-4" id="completionAlert">
                <div class="text-center">
                    <i class="bi bi-check-circle" style="font-size: 3rem; color: #16a34a;"></i>
                    <h4 class="mt-3 mb-3">¡Felicitaciones!</h4>
                    <p class="mb-0">
                        Has completado exitosamente todas las etapas de tus prácticas pre-profesionales. Tu proceso ha sido aprobado.
                    </p>
                </div>
            </div>
        @endif
                    
    </div>
    <!-- Modal Formulario -->
<div class="modal fade" id="archivoModal" tabindex="-1" aria-labelledby="archivoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Formulario de...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="approved-file-container" style="display: none;">
                    <div class="alert alert-success text-center">
                        <i class="bi bi-clipboard-check" style="font-size: 2rem;"></i>
                        <h5 class="alert-heading mt-2">Aprobado el Archivo</h5>
                        <p>El docente ya revisó y ha aprobado este anexo. No es posible modificarlo.</p>
                    </div>
                    <div class="d-flex flex-column">
                        <label class="font-weight-bold"><i class="bi bi-paperclip"></i> Archivo aprobado:</label>
                        <div class="alert alert-light p-2 d-flex justify-content-between align-items-center border flex-grow-1">
                            <span class="text-truncate"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Anexo_7_Estudiante.pdf</span>
                            <a href="#" id="approved-ruta" class="btn btn-sm btn-outline-primary flex-shrink-0 ms-2" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Ver</a>
                        </div>
                    </div>
                </div>
                <div id="pending-review-container" style="display: none;">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                        <h5 class="alert-heading mt-2">Enviado para Revisión</h5>
                        <p>Ya has enviado este anexo. El docente lo está revisando.</p>
                    </div>
                    <div class="d-flex flex-column">
                        <label class="font-weight-bold"><i class="bi bi-paperclip"></i> Archivo enviado:</label>
                        <div class="alert alert-light p-2 d-flex justify-content-between align-items-center border flex-grow-1">
                            <span class="text-truncate"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Anexo_7_Estudiante.pdf</span>
                            <a href="#" id="pending-ruta" class="btn btn-sm btn-outline-primary flex-shrink-0 ms-2" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Ver</a>
                        </div>
                    </div>
                </div>
                <form id="submission-form" action="{{ route('subir.documento') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="practica" name="practica" value="{{ $practicas->id }}">
                    <input type="hidden" id="tipo" name="tipo">
                    <div class="mb-3" id="archivoAnexo">
                        <label class="form-label">
                            <i class="bi bi-file-pdf"></i>
                            Anexo # (PDF)
                        </label>
                        <input type="file" name="archivo" class="form-control" accept="application/pdf"
                            onchange="validateFileSize(this, 10)">
                        <small class="text-muted">Archivo PDF, máximo 10MB</small>
                    </div>
                    <div class="mb-3 d-flex justify-content-between gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="saveEvaluation">Guardar</button>
                    </div>
                </form>
                <div class="history-container mb-3" id="history-container" style="display: none;">
                    <h6 class="mt-4">Documentos enviados (Historial)</h6>
                    <ul class="list-group history-list" id="archivosEnviadosList">
                        <!-- Los elementos de la lista se agregarán dinámicamente aquí -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    
    .practice-development-view {
        background-color: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .info-card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .info-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        color: #0d6efd;
    }
    
    .info-header i {
        font-size: 1.5rem;
        margin-right: 0.75rem;
    }
    
    .info-header h4 {
        margin: 0;
        font-weight: 600;
    }
    
    .info-content p {
        margin: 0;
        font-size: 1.1rem;
    }
    
    .section-title {
        color: var(--primary-blue);
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

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
<script>
    // Allowed stage viene del servidor
    const allowedStage = @json(isset($practicas->state) ? min(intval($practicas->state), 5) : 1);

    // Actualiza visual del stepper y muestra el contenido del stage seleccionado
    async function navigateToStage(stageId) {
        if (!stageId || stageId < 1) return;

        if (stageId > allowedStage) {
            console.error('Etapa bloqueada o no clickeable.');
            return;
        }

        // 1. Actualiza el estado de las etapas en el DOM
        document.querySelectorAll('.stepper-item').forEach(item => {
            const currentId = parseInt(item.getAttribute('data-stage'));
            item.classList.remove('current', 'completed', 'locked');

            const circle = item.querySelector('.stepper-circle');

            if (currentId < stageId) {
                item.classList.add('completed');
                if (circle) circle.innerHTML = '<i class="fas fa-check"></i>';
            } else if (currentId === stageId) {
                item.classList.add('current');
                if (circle) circle.innerHTML = currentId;
            } else {
                // Si es mayor al seleccionado
                if (currentId <= allowedStage) {
                    // Está desbloqueado pero es futuro visualmente
                    item.classList.add('completed'); // Usamos estilo completed pero sin check
                    if (circle) circle.innerHTML = currentId;
                } else {
                    // Realmente bloqueado
                    item.classList.add('locked');
                    if (circle) circle.innerHTML = '<i class="fas fa-lock"></i>';
                }
            }
        });

        // 2. Actualiza el título de la etapa
        const stageNames = ['Primera','Segunda','Tercera','Cuarta','Quinta','Sexta'];
        const stageLabels = ['Registro','Documentación','Doc. de Informes','Ejecución','Finalización'];
        const stageTitle = stageNames[stageId - 1] || '';
        const stageName = stageLabels[stageId - 1] || '';
        const titleEl = document.getElementById('current-stage-title');
        if (titleEl) titleEl.textContent = `${stageTitle} Etapa - ${stageName}`;
        const subtitleEl = document.getElementById('subtitle');
        if (subtitleEl) subtitleEl.textContent = `${stageTitle} Etapa - ${stageName}`;

        // 3. Mostrar/ocultar los contenidos de las etapas
        document.querySelectorAll('.stage-content').forEach(container => {
            const id = container.id.replace('stage-content-','');
            if (parseInt(id) === stageId) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });

        const ID_PRACTICA = {{ $practicas->id }};

        if(stageId === 1) {
            console.log('Entrando a Etapa 1: carga lazy de Empresa y Jefe Inmediato');
            try {
                const resEmpresa = await fetch(`/api/empresa/${ID_PRACTICA}`);
                if (resEmpresa.ok) {
                    const empresaData = await resEmpresa.json();
                    console.log('empresa:', empresaData);
                } else {
                    console.warn('API /api/empresa returned', resEmpresa.status);
                }
            } catch (err) {
                console.error('Error fetch /api/empresa', err);
            }

        } else if(stageId === 2) {
            console.log('Hello AQUI STATE 2');
            try {
                const type_fut = "fut";
                const resFut = await fetch(`/api/documento/${ID_PRACTICA}/${type_fut}`);
                if(resFut.ok) {
                    const data = await resFut.json();
                    console.log('fut: ', data[0]);

                    if(data != null) {
                        document.getElementById('status-file-fut').textContent = data[0].estado_archivo;
                    } else {
                        document.getElementById('btn-upload-fut').style.display='block';
                    }
                }else {
                    console.warn('API /api/empresa returned', resFut.status);
                }
            } catch (err) {
                console.error('Error fetch /api/empresa', err);
                }
        }
        console.log(`Navegando a la Etapa ${stageId}: ${stageName}`);
    }

    // Inicializar con la etapa máxima permitida por servidor (estado actual)
    document.addEventListener('DOMContentLoaded', function () {
        try {
            navigateToStage(allowedStage);
        } catch (e) {
            console.error(e);
        }
    });


    const archivoButtons = document.querySelectorAll('.btn-view-archivo');
    const modalElement = document.getElementById('archivoModal');
    const modal = new bootstrap.Modal(modalElement);

    // Contenedores del modal (los definimos fuera para mejor acceso)
    const approvedFileContainer = document.getElementById('approved-file-container');
    const pendingReviewContainer = document.getElementById('pending-review-container');
    const formContainer = document.getElementById('submission-form');
    const historyContainer = document.getElementById('history-container');

    // Opcional: Agregar un contenedor de carga (spinner) al modal para UX
    // const loadingContainer = document.getElementById('loading-spinner'); 

    archivoButtons.forEach(button => {
        button.addEventListener('click', async function (event) {
            event.preventDefault();
            
            approvedFileContainer.style.display = 'none';
            pendingReviewContainer.style.display = 'none';
            formContainer.style.display = 'none';
            historyContainer.style.display = 'none';

            modal.show();

            // ----------------------------------------------
            const ID_PRACTICA = {{ $practicas->id }};
            const type = this.getAttribute('data-type');

            try {
                const response = await fetch(`/api/documento/${ID_PRACTICA}/${type}`);

                // Opcional: Ocultar el indicador de carga al recibir respuesta
                // if (loadingContainer) loadingContainer.style.display = 'none'; 

                if (response.ok) {
                    const data = await response.json();
                    console.log('data ', data);
                    
                    // --- 🔄 PASO 2: ACTUALIZACIÓN CON DATOS ---
                    if(data != null && data.length > 0) { // Usar data.length > 0 es más seguro
                        const ldata = data[0];

                        if (ldata.estado_archivo === 'Enviado') {
                            // Usé 'Aprobar' para ambos, ya que tu código original tenía la misma lógica de display
                            approvedFileContainer.style.display = 'none'; // Se mantiene el 'none'
                            pendingReviewContainer.style.display = 'block';
                            formContainer.style.display = 'none';
                            document.getElementById('pending-ruta').href = '/documento/' + ldata.ruta;
                        } else if (ldata.estado_archivo === 'Aprobado') {
                            approvedFileContainer.style.display = 'block';
                            pendingReviewContainer.style.display = 'none';
                            formContainer.style.display = 'none';
                            document.getElementById('approved-ruta').href = '/documento/' + ldata.ruta;
                        } else if (ldata.estado_archivo === 'Corregir') {
                            formContainer.style.display = 'block';
                            approvedFileContainer.style.display = 'none';
                            pendingReviewContainer.style.display = 'none';

                            document.getElementById('tipo').value = type;
                        }

                        if(data.length > 1) { // Usa length sin paréntesis
                            historyContainer.style.display = 'block';
                            const historyList = document.getElementById('archivosEnviadosList');
                            historyList.innerHTML = '';
                            
                            // a partir del segundo elemento
                            data.forEach((item, index) => {
                                if(index > 0) {
                                    const li = document.createElement('li');
                                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                                    li.innerHTML = `
                                    <span>${new Date(item.created_at).toLocaleString()}</span>
                                    <span>${item.estado_archivo}</span>
                                    <a href="/documento/${item.ruta}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i> Ver</a>
                                `;
                                    historyList.appendChild(li);
                                }
                            });
                        }
                    } else {
                        // Si no hay datos, mostrar el formulario de envío
                        formContainer.style.display = 'block';
                        approvedFileContainer.style.display = 'none';
                        pendingReviewContainer.style.display = 'none';
                        historyContainer.style.display = 'none';

                        document.getElementById('tipo').value = type;
                    }
                } else {
                    console.warn('API /api/documento returned', response.status);
                }
            } catch (err) {
                // Opcional: Ocultar el indicador de carga en caso de error
                // if (loadingContainer) loadingContainer.style.display = 'none'; 
                console.error('Error fetch /api/documento', err);
            }

            // Ya no necesitas 'modal.show()' aquí, lo movimos al inicio para UX.
        });
    });
</script>
@endpush