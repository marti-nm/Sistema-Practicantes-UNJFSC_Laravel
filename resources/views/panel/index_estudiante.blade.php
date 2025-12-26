@extends('template')
@section('title', 'Dashboard Estudiante')
@section('subtitle', 'Panel de Información Académica')

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

    .dashboard-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0;
    }

    /* Card Principal */
    .dashboard-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .dashboard-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .dashboard-card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
        border-bottom: none;
    }

    .dashboard-card-title {
        font-size: 1.375rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dashboard-card-body {
        padding: 1.5rem;
    }

    /* Cards de Métricas */
    .metric-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
        text-align: center;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        transition: all 0.3s ease;
    }

    .metric-card.primary::before { background: linear-gradient(90deg, var(--primary-color), var(--primary-light)); }
    .metric-card.info::before { background: linear-gradient(90deg, var(--info-color), #0e7490); }
    .metric-card.warning::before { background: linear-gradient(90deg, var(--warning-color), #b45309); }
    .metric-card.success::before { background: linear-gradient(90deg, var(--success-color), #047857); }

    .metric-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .metric-card.primary .metric-icon { color: var(--primary-color); }
    .metric-card.info .metric-icon { color: var(--info-color); }
    .metric-card.warning .metric-icon { color: var(--warning-color); }
    .metric-card.success .metric-icon { color: var(--success-color); }

    .metric-label {
        font-size: 1rem;
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .fade-in { animation: fadeIn 0.5s ease-in; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
    @php
        $nombreCompleto = $ap->persona->nombres . ' ' . $ap->persona->apellidos;

        $hasPractice = $practicas && $practicas->tipo_practica !== null;
        $practiceType = $practicas->tipo_practica ?? null;

        $archivosPorTipo = $matricula->archivos->groupBy('tipo');

        $getLatest = function ($tipo) use ($archivosPorTipo) {
            $history = $archivosPorTipo->get($tipo);
            return $history ? $history->sortByDesc('created_at')->first() : null; 
        };

        $latestFicha = $getLatest('ficha');
        $estadoFicha = $latestFicha ? $latestFicha->estado_archivo : 'Falta';
        $msjFicha = ($estadoFicha === 'Corregir') ? $latestFicha->comentario : null;

        $latestRecord = $getLatest('record');
        $estadoRecord = $latestRecord ? $latestRecord->estado_archivo : 'Falta';
        $msjRecord = ($estadoRecord === 'Corregir') ? $latestRecord->comentario : null;
    @endphp

    <div class="dashboard-container fade-in" id="mainContentView">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="bi bi-person-workspace"></i> Panel del Estudiante
                </h5>
            </div>
            
            <div class="dashboard-card-body">
                <!-- Cards Informativos -->
                <div class="row">
                    <!-- Card Matrícula -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="metric-card info">
                            <i class="bi bi-journal-bookmark metric-icon"></i>
                            <div class="metric-label">Matrícula Académica</div>
                            <div class="metric-value">
                                @if(isset($matricula))
                                    @if ($matricula->estado_matricula == 'Completo')
                                        Completo
                                    @elseif ($matricula->estado_matricula == 'Pendiente')
                                        En Proceso
                                    @else
                                        {{ $matricula->estado_matricula }}
                                    @endif
                                @else
                                    Pendiente
                                @endif
                            </div>
                            <a href="{{ route('matricula.estudiante') }}" class="btn btn-info text-white w-100">
                                <i class="bi bi-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>

                    <!-- Card Prácticas -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="metric-card success">
                            <i class="bi bi-briefcase metric-icon"></i>
                            <div class="metric-label">Prácticas Pre-profesionales</div>
                            <div class="metric-value">
                                @if(isset($practicas))
                                    @if ($practicas->state == 5)
                                        Completo
                                    @elseif ($practicas->estado_practica == 'en proceso' || $practicas->estado_practica == 'rechazado')
                                        En Proceso
                                    @else
                                        Etapa {{ $practicas->state ?? 'Inicial' }}
                                    @endif
                                @else
                                    Sin Iniciar
                                @endif
                            </div>
                            @if(isset($matricula) && $matricula->estado_matricula == 'Completo')
                                <a href="{{ route('practicas.estudiante') }}" class="btn btn-success text-white w-100">
                                    <i class="bi bi-diagram-2"></i> Gestión de Prácticas
                                </a>
                            @else
                                <button class="btn btn-secondary text-white w-100" onclick="showAlert('Atención', 'Debes completar tu matrícula para acceder a prácticas.', 'warning')">
                                    <i class="bi bi-lock-fill"></i> Acceso Bloqueado
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Card Perfil -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="metric-card warning">
                            <i class="bi bi-person-badge metric-icon"></i>
                            <div class="metric-label">Mi Perfil</div>
                            <div class="metric-value" style="font-size: 1.1rem;">
                                {{ $ap->seccion_academica->escuela->name ?? 'Estudiante' }}
                            </div>
                            <button class="btn btn-warning text-white w-100" data-bs-toggle="modal" data-bs-target="#modalPerfil">
                                <i class="bi bi-pencil-square"></i> Ver Mis Datos
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Alert de Pendientes -->
                @if( isset($matricula) && ($matricula->estado_matricula != 'Completo') )
                     <div class="alert alert-warning mt-3">
                         <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                         <strong>Importante:</strong> Tienes pendientes en tu matrícula. Por favor completa los requisitos para continuar.
                     </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Includes Modals y Lógica -->
    @include('segmento.view_estu')
    
    <!-- Modal Prácticas Logic -->
    @if(isset($matricula))
        @include('practicas.estudiante.practica')
    @else
        <div class="d-flex justify-content-center align-items-center my-5">
            <div class="alert alert-danger shadow-lg p-5 rounded-lg text-center" style="max-width: 600px; width: 100%;">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                </div>
                <h2 class="font-weight-bold mb-3">¡Atención!</h2>
                <p class="mb-0" style="font-size: 20px;">
                    Primero debes completar tu matrícula para acceder.
                </p>
            </div>
        </div>
    @endif

    <!-- Vista de Práctica (Dinámica) -->
    <div id="practiceViewContainer" style="display: none;">
        @if($practicas && $practicas->tipo_practica)
            @if($practicas->tipo_practica === 'desarrollo')
                @include('practicas.estudiante.desarrollo.est_des')
            @elseif($practicas->tipo_practica === 'convalidacion')
                @include('practicas.estudiante.convalidacion.est_con')
            @endif
        @endif
    </div>
@endsection

@push('js')
    <script>
        // Función para mostrar alertas
        function showAlert(title, message, type = 'info') {
            const alertClass = type === 'error' ? 'alert-danger' : type === 'success' ? 'alert-success' : 'alert-info';
            // Usa SweetAlert si está disponible, sino fallback
            if(typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: type === 'error' ? 'error' : type,
                    timer: 5000
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkPracticeView();
            
            // Verificar si ya tiene práctica seleccionada
            const hasPractice = @json($hasPractice);
            const practiceType = @json($practiceType);
            
            // Mostrar modal solo si no tiene práctica seleccionada
            const modalPracticasBtn = document.querySelector('[data-bs-target="#modalPracticas"]');
            if(modalPracticasBtn) {
                modalPracticasBtn.addEventListener('click', function(e) {
                    if (hasPractice) {
                        e.preventDefault();
                        showPracticeView(practiceType);
                    }
                });
            }
            
            // Configurar botones de selección
            document.querySelectorAll('.practice-option button').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.closest('.practice-option').dataset.practiceType;
                    selectPracticeType(type);
                });
            });
        });

        function selectPracticeType(type) {
            Swal.fire({
                title: '¿Confirmar selección?',
                text: `¿Deseas seleccionar la práctica de ${type === 'desarrollo' ? 'Desarrollo' : 'Convalidación'}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, seleccionar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const route = type === 'desarrollo' 
                        ? '{{ route("desarrollo.store", ["ed" => 1]) }}' 
                        : '{{ route("desarrollo.store", ["ed" => 2]) }}';
                    
                    axios.post(route)
                        .then(response => {
                            if (response.data.success) {
                                // Ocultar modal y mostrar vista de práctica
                                const modalElement = document.getElementById('modalPracticas');
                                if(modalElement) {
                                    const modal = bootstrap.Modal.getInstance(modalElement);
                                    if (modal) modal.hide();
                                }
                                
                                showPracticeView(type);
                                showAlert('Check', 'Tipo de práctica seleccionado correctamente', 'success');
                                setTimeout(() => location.reload(), 1000);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('Error', 'Error al guardar la selección', 'error');
                        });
                }
            });
        }

        function showPracticeView(type) {
            // Ocultar contenido principal
            const mainContent = document.getElementById('mainContentView');
            if(mainContent) mainContent.style.display = 'none';
            
            // Mostrar vista de práctica
            const practiceView = document.getElementById('practiceViewContainer');
            if(practiceView) practiceView.style.display = 'block';
            
            // Actualizar URL
            history.pushState({ practiceView: true }, '', '?view=practice');
        }

        function checkPracticeView() {
            const hasPractice = @json($hasPractice);
            const practiceType = @json($practiceType);
            const urlParams = new URLSearchParams(window.location.search);
            
            if (hasPractice) {
                if (urlParams.get('view') === 'practice') {
                    showPracticeView(practiceType);
                } else {
                    const mainContent = document.getElementById('mainContentView');
                    const practiceView = document.getElementById('practiceViewContainer');
                    if(practiceView) practiceView.style.display = 'none';
                    if(mainContent) mainContent.style.display = 'block';
                }
            }
        }

        function goHome() {
            const mainContent = document.getElementById('mainContentView');
            const practiceView = document.getElementById('practiceViewContainer');
            
            if(practiceView) practiceView.style.display = 'none';
            if(mainContent) mainContent.style.display = 'block'; // Ensure flex or block class is applied if needed, but div is block by default
            
            history.pushState({}, '', window.location.pathname);
        }

        window.addEventListener('popstate', function(event) {
            checkPracticeView();
        });

        // SweetAlert2 CDN
        if (typeof Swal === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(script);
        }
    </script>
    <script src="{{ asset('js/perfil_edit.js') }}"></script>
    <script>
        // Funcionalidad para subir foto (Perfil)
        const fotoInput = document.getElementById('fotoInput');
        if(fotoInput){
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('previewImage');
                        if(preview) preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
@endpush