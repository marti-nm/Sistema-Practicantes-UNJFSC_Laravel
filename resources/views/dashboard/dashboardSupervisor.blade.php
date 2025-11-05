@extends('template')
@section('title', 'Dashboard Supervisor')
@section('subtitle', 'Panel de supervisión y seguimiento de estudiantes')

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
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
        border-bottom: none;
    }

    .dashboard-card-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .dashboard-card-title {
        font-size: 1.375rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-transform: none;
    }

    .dashboard-card-title i {
        font-size: 1.25rem;
    }

    .dashboard-card-body {
        padding: 1.5rem;
    }

    /* Sección de Filtros */
    .filters-section {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .filters-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #7c3aed, #5b21b6);
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .filters-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filters-title i {
        color: #7c3aed;
    }

    /* Form Controls */
    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-select {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: var(--surface-color);
    }

    .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        outline: none;
    }

    .form-control {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: var(--surface-color);
    }

    .form-control:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        outline: none;
    }

    .form-control[readonly] {
        background: #f8fafc;
        border-color: var(--border-color);
        color: var(--text-secondary);
    }

    /* Métricas */
    .metrics-section {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .metrics-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--info-color), #0e7490);
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .metrics-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .metrics-title i {
        color: var(--info-color);
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

    .metric-card.primary::before {
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .metric-card.info::before {
        background: linear-gradient(90deg, var(--info-color), #0e7490);
    }

    .metric-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .metric-card.primary .metric-icon {
        color: var(--primary-color);
    }

    .metric-card.info .metric-icon {
        color: var(--info-color);
    }

    .metric-label {
        font-size: 0.9rem;
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    /* Lista de Estudiantes Supervisados */
    .students-section {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .students-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--success-color), #047857);
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .students-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .students-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .students-title i {
        color: var(--success-color);
    }

    .search-input {
        max-width: 400px;
        min-width: 300px;
    }

    /* Tabla */
    .table-container {
        background: var(--surface-color);
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        max-height: 500px;
        overflow-y: auto;
    }

    .table {
        margin: 0;
        border: none;
        font-size: 0.9rem;
    }

    .table thead th {
        background: linear-gradient(135deg, #1e293b, #334155);
        color: white;
        border: none;
        font-weight: 600;
        padding: 1rem 0.75rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table tbody td {
        padding: 0.875rem 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-primary);
        vertical-align: middle;
        text-align: center;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(124, 58, 237, 0.02);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Botones de Ver PDF */
    .btn-view-pdf {
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
        border: none;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .btn-view-pdf:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .btn-view-pdf i {
        font-size: 0.75rem;
    }

    /* Badges de Estado */
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-block;
        transition: all 0.2s ease;
    }

    .status-badge.not-uploaded {
        background: rgba(100, 116, 139, 0.1);
        color: var(--secondary-color);
        border: 1px solid rgba(100, 116, 139, 0.2);
    }

    .status-badge:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-sm);
    }

    /* Estado vacío */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--border-color);
        margin-bottom: 1rem;
        display: block;
    }

    /* Botón de filtro */
    .btn-filter {
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-filter:hover {
        background: linear-gradient(135deg, #5b21b6, #4c1d95);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-card-header {
            padding: 1.25rem 1.5rem;
        }

        .dashboard-card-body {
            padding: 1rem;
        }

        .filters-section,
        .metrics-section,
        .students-section {
            padding: 1rem;
        }

        .students-header {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input {
            max-width: none;
            min-width: auto;
        }

        .table {
            min-width: 700px;
        }

        .metric-card {
            margin-bottom: 1rem;
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

    /* Scroll personalizado */
    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: var(--background-color);
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: var(--secondary-color);
    }
</style>
@endpush

@section('content')

<div class="dashboard-container fade-in">
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h5 class="dashboard-card-title">
                <i class="bi bi-eye"></i>
                Dashboard Supervisor - Panel de Seguimiento
            </h5>
        </div>

        <div class="dashboard-card-body">

            {{-- Filtros --}}
            <div class="filters-section">
                <h6 class="filters-title">
                    <i class="bi bi-funnel"></i>
                    Filtros de Búsqueda
                </h6>
                <form method="GET" class="row g-3">
                    {{-- FACULTAD --}}
                    <div class="col-md-4">
                        <label class="form-label">Facultad:</label>
                        <select name="facultad_id" id="facultad_id" class="form-select">
                            <option value="">-- Seleccione --</option>
                            @foreach($facultades as $facultad)
                                <option value="{{ $facultad->id }}" {{ $facultadId == $facultad->id ? 'selected' : '' }}>
                                    {{ $facultad->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ESCUELA --}}
                    <div class="col-md-4">
                        <label class="form-label">Escuela:</label>
                        <select name="escuela_id" id="escuela_id" class="form-select">
                            <option value="">-- Seleccione --</option>
                            @foreach($escuelas as $escuela)
                                <option value="{{ $escuela->id }}" {{ $escuelaId == $escuela->id ? 'selected' : '' }}>
                                    {{ $escuela->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SEMESTRE --}}
                    <div class="col-md-4">
                        <label class="form-label">Semestre:</label>
                        <select id="semestre" name="semestre" class="form-select">
                            <option value="">-- Todos --</option>
                        </select>
                    </div>
                    
                    <div class="col-12 text-end">
                        <button type="submit" class="btn-filter">
                            <i class="bi bi-funnel"></i>
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Métricas --}}
            <div class="metrics-section">
                <h6 class="metrics-title">
                    <i class="bi bi-graph-up"></i>
                    Indicadores de Supervisión
                </h6>
                <div class="row">
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="metric-card primary">
                            <i class="bi bi-people-fill metric-icon"></i>
                            <div class="metric-label">Total Estudiantes</div>
                            <div class="metric-value">{{ $totalFiltrados }}</div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 mb-3">
                        <div class="metric-card info">
                            <i class="bi bi-file-earmark-check-fill metric-icon"></i>
                            <div class="metric-label">Anexos Completos</div>
                            <div class="metric-value">{{ $totalCompletos }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de alumnos supervisados --}}
            <div class="students-section">
                <div class="students-header">
                    <h6 class="students-title">
                        <i class="bi bi-clipboard-check"></i>
                        Alumnos Supervisados con Anexos
                    </h6>
                    <input type="text" id="buscarAlumnos" class="form-control search-input" 
                           placeholder="🔍 Buscar estudiantes...">
                </div>

                <div class="table-container">
                    <table class="table" id="tablaAlumnos">
                        <thead>
                            <tr>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Escuela</th>
                                <th>Anexo 7</th>
                                <th>Anexo 8</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alumnos as $alumno)
                                <tr>
                                    <td>{{ $alumno->nombres }}</td>
                                    <td>{{ $alumno->apellidos }}</td>
                                    <td>{{ $alumno->escuela }}</td>
                                    <td>
                                        @if($alumno->anexo_6)
                                            <a href="{{ asset('storage/' . $alumno->anexo_6) }}" target="_blank" class="btn-view-pdf">
                                                <i class="bi bi-file-pdf"></i>
                                                Ver PDF
                                            </a>
                                        @else
                                            <span class="status-badge not-uploaded">No subido</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alumno->anexo_7)
                                            <a href="{{ asset('storage/' . $alumno->anexo_7) }}" target="_blank" class="btn-view-pdf">
                                                <i class="bi bi-file-pdf"></i>
                                                Ver PDF
                                            </a>
                                        @else
                                            <span class="status-badge not-uploaded">No subido</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alumno->anexo_8)
                                            <a href="{{ asset('storage/' . $alumno->anexo_8) }}" target="_blank" class="btn-view-pdf">
                                                <i class="bi bi-file-pdf"></i>
                                                Ver PDF
                                            </a>
                                        @else
                                            <span class="status-badge not-uploaded">No subido</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <p class="mb-0">No hay estudiantes supervisados registrados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const facultadSelect = document.getElementById('facultad_id');
    const escuelaSelect = document.getElementById('escuela_id');
    const semestreSelect = document.getElementById('semestre_codigo');

    const selectedEscuela = "{{ request('escuela_id') }}";
    const selectedSemestre = "{{ request('semestre_codigo') }}";

    function cargarEscuelas(facultadId, callback) {
        fetch(`/api/escuelas/${facultadId}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Seleccione --</option>';
                data.forEach(e => {
                    const selected = e.id == selectedEscuela ? 'selected' : '';
                    options += `<option value="${e.id}" ${selected}>${e.name}</option>`;
                });
                escuelaSelect.innerHTML = options;

                if (selectedEscuela && callback) callback(selectedEscuela);
            })
            .catch(() => {
                escuelaSelect.innerHTML = '<option value="">Error al cargar</option>';
            });
    }

    // El componente semestre-selector maneja automáticamente la carga de semestres
    // No necesitamos esta función

    // Eventos
    facultadSelect.addEventListener('change', function () {
        const facultadId = this.value;
        escuelaSelect.innerHTML = '<option value="">Cargando...</option>';

        if (facultadId) {
            cargarEscuelas(facultadId);
        } else {
            escuelaSelect.innerHTML = '<option value="">-- Seleccione --</option>';
        }
    });

    // El componente semestre-selector maneja automáticamente la carga de semestres
    // No necesitamos este evento

    // Carga inicial si viene con datos filtrados
    if (facultadSelect.value && selectedEscuela) {
        cargarEscuelas(facultadSelect.value);
    }

    // Animaciones progresivas para las métricas
    function animateMetrics() {
        $('.metric-card').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(20px)'
            });
            
            setTimeout(() => {
                $(this).css({
                    'transition': 'all 0.5s ease',
                    'opacity': '1',
                    'transform': 'translateY(0)'
                });
            }, index * 150);
        });
    }

    // Efectos hover mejorados para metric cards
    $('.metric-card').hover(
        function() {
            $(this).find('.metric-icon').css({
                'transform': 'scale(1.2) rotate(5deg)',
                'transition': 'all 0.3s ease'
            });
            $(this).find('.metric-value').css({
                'color': '#7c3aed',
                'transform': 'scale(1.1)'
            });
        },
        function() {
            $(this).find('.metric-icon').css({
                'transform': 'scale(1) rotate(0deg)'
            });
            $(this).find('.metric-value').css({
                'color': 'var(--text-primary)',
                'transform': 'scale(1)'
            });
        }
    );

    // Efectos para la tabla de estudiantes supervisados
    $('#tablaAlumnos tbody tr').hover(
        function() {
            $(this).css({
                'border-left': '4px solid #7c3aed',
                'transition': 'all 0.3s ease'
            });
        },
        function() {
            $(this).css({
                'border-left': 'none'
            });
        }
    );

    // Animación para botones PDF
    $('.btn-view-pdf').hover(
        function() {
            $(this).find('i').css({
                'transform': 'scale(1.2) rotate(5deg)',
                'transition': 'all 0.3s ease'
            });
        },
        function() {
            $(this).find('i').css({
                'transform': 'scale(1) rotate(0deg)'
            });
        }
    );

    // Animación para badges de estado
    $('.status-badge').hover(
        function() {
            $(this).css({
                'transform': 'scale(1.1) rotate(2deg)',
                'box-shadow': 'var(--shadow-sm)'
            });
        },
        function() {
            $(this).css({
                'transform': 'scale(1) rotate(0deg)',
                'box-shadow': 'none'
            });
        }
    );

    // Contador animado para métricas
    function animateCounter(element, targetValue) {
        const startValue = 0;
        const duration = 1500;
        const startTime = performance.now();
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);
            
            element.textContent = currentValue;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }
        
        requestAnimationFrame(updateCounter);
    }

    // Inicializar contadores animados
    $('.metric-value').each(function() {
        const targetValue = parseInt($(this).text());
        if (!isNaN(targetValue)) {
            animateCounter(this, targetValue);
        }
    });

    // Función de búsqueda mejorada
    $('#buscarAlumnos').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('#tablaAlumnos tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            const isMatch = rowText.includes(searchTerm);
            
            $(this).toggle(isMatch);
            
            if (isMatch && searchTerm.length > 0) {
                $(this).css({
                    'animation': 'highlight 0.5s ease',
                    'background-color': 'rgba(124, 58, 237, 0.05)'
                });
                
                setTimeout(() => {
                    $(this).css({
                        'background-color': '',
                        'animation': ''
                    });
                }, 500);
            }
        });
        
        // Mostrar mensaje si no hay resultados
        const visibleRows = $('#tablaAlumnos tbody tr:visible').length;
        if (visibleRows === 0 && searchTerm.length > 0) {
            if ($('#no-results').length === 0) {
                $('#tablaAlumnos tbody').append(`
                    <tr id="no-results">
                        <td colspan="5" class="empty-state">
                            <i class="bi bi-search"></i>
                            <p class="mb-0">No se encontraron resultados para "${searchTerm}"</p>
                        </td>
                    </tr>
                `);
            }
        } else {
            $('#no-results').remove();
        }
    });

    // Efectos para selects de filtros
    $('.form-select, .form-control').focus(function() {
        $(this).css({
            'transform': 'scale(1.02)',
            'transition': 'all 0.2s ease'
        });
    }).blur(function() {
        $(this).css({
            'transform': 'scale(1)'
        });
    });

    // Efecto loading en formulario de filtros
    $('form').on('submit', function() {
        $('.dashboard-card-body').css({
            'opacity': '0.7',
            'pointer-events': 'none'
        });
        
        // Mostrar indicador de carga
        $('.filters-section').append(`
            <div class="loading-indicator" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <i class="bi bi-hourglass-split" style="font-size: 2rem; color: #7c3aed; animation: spin 1s linear infinite;"></i>
            </div>
        `);
    });

    // Animación de entrada progresiva para filas
    function animateTableRows() {
        const rows = $('#tablaAlumnos tbody tr');
        rows.each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateX(-20px)'
            });
            
            setTimeout(() => {
                $(this).css({
                    'transition': 'all 0.4s ease',
                    'opacity': '1',
                    'transform': 'translateX(0)'
                });
            }, index * 100 + 700);
        });
    }

    // Ejecutar animaciones al cargar
    setTimeout(animateMetrics, 300);
    if ($('#tablaAlumnos tbody tr').length > 0) {
        setTimeout(animateTableRows, 500);
    }

    // CSS adicional para animaciones
    $('head').append(`
        <style>
            @keyframes highlight {
                0% { background-color: rgba(124, 58, 237, 0.2); }
                50% { background-color: rgba(124, 58, 237, 0.1); }
                100% { background-color: rgba(124, 58, 237, 0.05); }
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .metric-value {
                transition: all 0.3s ease;
            }
            
            .metric-icon {
                transition: all 0.3s ease;
            }
            
            .status-badge {
                transition: all 0.2s ease;
                cursor: default;
            }
            
            .btn-view-pdf {
                transition: all 0.3s ease;
            }
            
            .btn-view-pdf i {
                transition: all 0.3s ease;
            }
            
            .table tbody tr {
                transition: all 0.2s ease;
            }
            
            .form-select:focus,
            .form-control:focus {
                transform: scale(1.02);
            }
            
            .loading-indicator {
                z-index: 100;
            }
        </style>
    `);
});
</script>

@push('js')
@endpush

