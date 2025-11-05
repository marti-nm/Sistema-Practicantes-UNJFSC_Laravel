@extends('template')
@section('title', 'Dashboard Administrativo')
@section('subtitle', 'Panel de control y métricas del sistema de prácticas')

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
        background: linear-gradient(90deg, var(--info-color), #0e7490);
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
        color: var(--info-color);
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
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
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
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        outline: none;
    }

    /* Botón de Filtrar */
    .btn-filter {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: var(--shadow-sm);
    }

    .btn-filter:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: white;
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
        background: linear-gradient(90deg, var(--success-color), #047857);
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
        color: var(--success-color);
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

    .metric-card.warning::before {
        background: linear-gradient(90deg, var(--warning-color), #b45309);
    }

    .metric-card.success::before {
        background: linear-gradient(90deg, var(--success-color), #047857);
    }

    .metric-card.danger::before {
        background: linear-gradient(90deg, var(--danger-color), #991b1b);
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

    .metric-card.warning .metric-icon {
        color: var(--warning-color);
    }

    .metric-card.success .metric-icon {
        color: var(--success-color);
    }

    .metric-card.danger .metric-icon {
        color: var(--danger-color);
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

    /* Tabla de Estudiantes */
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
        background: linear-gradient(90deg, var(--warning-color), #b45309);
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
        color: var(--warning-color);
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
        table-layout: fixed;
        width: 100%;
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
        background-color: rgba(30, 58, 138, 0.02);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
    }

    .status-badge.success {
        background: rgba(5, 150, 105, 0.1);
        color: var(--success-color);
        border: 1px solid rgba(5, 150, 105, 0.2);
    }

    .status-badge.warning {
        background: rgba(217, 119, 6, 0.1);
        color: var(--warning-color);
        border: 1px solid rgba(217, 119, 6, 0.2);
    }

    .status-badge.danger {
        background: rgba(220, 38, 38, 0.1);
        color: var(--danger-color);
        border: 1px solid rgba(220, 38, 38, 0.2);
    }

    /* Secciones de Gráficos */
    .chart-section {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .chart-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--danger-color), #991b1b);
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chart-title i {
        color: var(--danger-color);
    }

    .chart-container {
        position: relative;
        height: 360px;
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
        .students-section,
        .chart-section {
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
            min-width: 800px;
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
                <i class="bi bi-speedometer2"></i>
                Panel de Control Administrativo
            </h5>
        </div>

        <div class="dashboard-card-body">
            {{-- Filtros --}}
            <div class="filters-section">
                <h6 class="filters-title">
                    <i class="bi bi-funnel"></i>
                    Filtros de Búsqueda
                </h6>
                <form method="GET" action="{{ route('admin.Dashboard') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="facultad" class="form-label">Facultad:</label>
                            <select id="facultad" name="facultad" class="form-select">
                                <option value="">-- Todas --</option>
                                @foreach($facultades as $fac)
                                    <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="escuela" class="form-label">Escuela:</label>
                            <select id="escuela" name="escuela" class="form-select">
                                <option value="">-- Todas --</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="docente" class="form-label">Docente:</label>
                            <select id="docente" name="docente" class="form-select">
                                <option value="">-- Todos --</option>
                            </select>
                        </div>                        
                        <div class="col-md-3 d-flex align-items-end justify-content-end">
                            <button type="submit" class="btn-filter">
                                <i class="bi bi-filter"></i> 
                                Filtrar Datos
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Métricas --}}
            <div class="metrics-section">
                <h6 class="metrics-title">
                    <i class="bi bi-graph-up"></i>
                    Indicadores Generales
                </h6>
                <div class="row">
                    <div class="col-lg col-md-6 mb-3">
                        <div class="metric-card primary">
                            <i class="bi bi-people-fill metric-icon"></i>
                            <div class="metric-label">Total Alumnos</div>
                            <div class="metric-value">{{ $totalPorEscuelaEnSemestre }}</div>
                        </div>
                    </div>
                    <div class="col-lg col-md-6 mb-3">
                        <div class="metric-card info">
                            <i class="bi bi-person-check-fill metric-icon"></i>
                            <div class="metric-label">Matriculados</div>
                            <div class="metric-value">{{ $totalMatriculados }}</div>
                        </div>
                    </div>
                    <div class="col-lg col-md-6 mb-3">
                        <div class="metric-card warning">
                            <i class="bi bi-person-badge-fill metric-icon"></i>
                            <div class="metric-label">Supervisores</div>
                            <div class="metric-value">{{ $totalSupervisores }}</div>
                        </div>
                    </div>
                    <div class="col-lg col-md-6 mb-3">
                        <div class="metric-card success">
                            <i class="bi bi-file-earmark-check-fill metric-icon"></i>
                            <div class="metric-label">Fichas Completas</div>
                            <div class="metric-value">{{ $completos }}</div>
                        </div>
                    </div>
                    <div class="col-lg col-md-6 mb-3">
                        <div class="metric-card danger">
                            <i class="bi bi-exclamation-circle-fill metric-icon"></i>
                            <div class="metric-label">Pendientes</div>
                            <div class="metric-value">{{ $pendientes }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de Estudiantes --}}
            <div class="students-section">
                <div class="students-header">
                    <h6 class="students-title">
                        <i class="bi bi-list-ul"></i>
                        Lista de Estudiantes Matriculados
                    </h6>
                    <input type="text" id="filtroTabla" class="form-control search-input" 
                           placeholder="🔍 Buscar estudiantes...">
                </div>

                <div class="table-container">
                    <table class="table" id="tablaEstudiantes">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Facultad</th>
                                <th>Escuela</th>
                                <th>Semestre</th>
                                <th>Ficha</th>
                                <th>Record</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listaEstudiantes as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $item->nombres }}</td>
                                    <td>{{ $item->apellidos }}</td>
                                    <td>{{ $item->facultad }}</td>
                                    <td>{{ $item->escuela }}</td>
                                    <td>{{ $item->semestre }}</td>
                                    <td>
                                        @php $estadoFicha = $item->estado_ficha ?? 'Sin registrar'; @endphp
                                        @if($estadoFicha === 'Completo')
                                            <span class="status-badge success">Completo</span>
                                        @elseif($estadoFicha === 'En proceso')
                                            <span class="status-badge warning">En proceso</span>
                                        @else
                                            <span class="status-badge danger">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $estadoRecord = $item->estado_record ?? 'Sin registrar'; @endphp
                                        @if($estadoRecord === 'Completo')
                                            <span class="status-badge success">Completo</span>
                                        @elseif($estadoRecord === 'En proceso')
                                            <span class="status-badge warning">En proceso</span>
                                        @else
                                            <span class="status-badge danger">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            @if($listaEstudiantes->isEmpty())
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <p class="mb-0">No se encontraron estudiantes registrados.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Gráfico de Barras --}}
            <div class="chart-section">
                <h6 class="chart-title">
                    <i class="bi bi-bar-chart"></i>
                    Estado de Fichas por Escuela
                </h6>
                <div class="chart-container">
                    <canvas id="stackedBarChart"></canvas>
                </div>
            </div>

            {{-- Gráfico de Líneas --}}
            <div class="chart-section">
                <h6 class="chart-title">
                    <i class="bi bi-graph-up"></i>
                    Fichas Validadas por Mes ({{ date('Y') }})
                </h6>
                <div class="chart-container">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

        </div>
    </div>
</div>





<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctxLineEl = document.getElementById('lineChart');
        if (ctxLineEl) {
            const ctxLine = ctxLineEl.getContext('2d');

            const lineChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: {!! json_encode($fichasPorMes->pluck('mes')) !!},
                    datasets: [{
                        label: 'Fichas validadas',
                        data: {!! json_encode($fichasPorMes->pluck('total')) !!},
                        borderColor: '#1e3a8a',
                        backgroundColor: 'rgba(30, 58, 138, 0.1)',
                        pointBackgroundColor: '#1e3a8a',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#1e3a8a',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#1e293b',
                                font: {
                                    size: 14,
                                    weight: 'bold',
                                    family: 'Inter'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#ffffff',
                            titleColor: '#1e3a8a',
                            bodyColor: '#64748b',
                            borderColor: '#e2e8f0',
                            borderWidth: 2,
                            cornerRadius: 8,
                            displayColors: false,
                            titleFont: {
                                family: 'Inter',
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: 'Inter'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#64748b',
                                font: {
                                    family: 'Inter',
                                    weight: '500'
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#64748b',
                                stepSize: 1,
                                font: {
                                    family: 'Inter',
                                    weight: '500'
                                }
                            },
                            grid: {
                                color: '#f1f5f9',
                                lineWidth: 1
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<script>
let chart; // Mantén una referencia global al gráfico

function renderChart(data) {
    const labels = data.map(item => item.escuela);
    const completos = data.map(item => item.completos);
    const enProceso = data.map(item => item.en_proceso);
    const pendientes = data.map(item => item.pendientes);

    const ctx = document.getElementById('stackedBarChart').getContext('2d');

    if (chart) chart.destroy(); // Borra el gráfico anterior si ya existe

    chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { 
                    label: 'Completo', 
                    data: completos, 
                    backgroundColor: '#059669',
                    borderColor: '#047857',
                    borderWidth: 1,
                    borderRadius: 4
                },
                { 
                    label: 'En proceso', 
                    data: enProceso, 
                    backgroundColor: '#d97706',
                    borderColor: '#b45309',
                    borderWidth: 1,
                    borderRadius: 4
                },
                { 
                    label: 'Pendiente', 
                    data: pendientes, 
                    backgroundColor: '#dc2626',
                    borderColor: '#991b1b',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { 
                    display: true, 
                    text: 'Estado de fichas por escuela',
                    color: '#1e293b',
                    font: {
                        size: 16,
                        weight: 'bold',
                        family: 'Inter'
                    }
                },
                legend: { 
                    position: 'top',
                    labels: {
                        color: '#1e293b',
                        font: {
                            family: 'Inter',
                            weight: '500'
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#ffffff',
                    titleColor: '#1e293b',
                    bodyColor: '#64748b',
                    borderColor: '#e2e8f0',
                    borderWidth: 2,
                    cornerRadius: 8,
                    titleFont: {
                        family: 'Inter',
                        weight: 'bold'
                    },
                    bodyFont: {
                        family: 'Inter'
                    }
                }
            },
            scales: {
                x: { 
                    stacked: true,
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: 'Inter',
                            weight: '500'
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: { 
                    stacked: true, 
                    beginAtZero: true,
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: 'Inter',
                            weight: '500'
                        }
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                }
            }
        }
    });
}

// Cargar inicial (si no quieres esperar a un filtro)
renderChart(@json($fichasPorEscuela));

// Manejar cambios de filtros
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {

    
    //e.preventDefault(); // evita recargar la página


    const params = new URLSearchParams(new FormData(this)).toString();

    
});
</script>

<script>
    document.getElementById('filtroTabla').addEventListener('keyup', function () {
        const valor = this.value.toLowerCase();
        const filas = document.querySelectorAll('#tablaEstudiantes tbody tr');

        filas.forEach(fila => {
            const texto = fila.textContent.toLowerCase();
            fila.style.display = texto.includes(valor) ? '' : 'none';
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const facultadSelect = document.getElementById('facultad');
    const escuelaSelect = document.getElementById('escuela');
    const docenteSelect = document.getElementById('docente');
    const semestreActivoId = {{ session('semestre_actual_id') ?? 'null' }};

    facultadSelect.addEventListener('change', function () {
        const facultadId = this.value;
        
        // Reset dependants
        escuelaSelect.innerHTML = '<option value="">-- Todas --</option>';
        docenteSelect.innerHTML = '<option value="">-- Todos --</option>';

        if (!facultadId) {
            return;
        }

        escuelaSelect.innerHTML = '<option value="">Cargando...</option>';
        fetch(`/api/escuelas/${facultadId}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Todas --</option>';
                data.forEach(e => {
                    options += `<option value="${e.id}">${e.name}</option>`;
                });
                escuelaSelect.innerHTML = options;
            })
            .catch(() => {
                escuelaSelect.innerHTML = '<option value="">Error al cargar</option>';
            });
    });

    escuelaSelect.addEventListener('change', function () {
        const escuelaId = this.value;
        docenteSelect.innerHTML = '<option value="">-- Todos --</option>';

        if (!escuelaId || !semestreActivoId) {
            return;
        }

        docenteSelect.innerHTML = '<option value="">Cargando...</option>';
        fetch(`/api/docentes/${escuelaId}/${semestreActivoId}`) // <-- Usar semestre activo
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Todos --</option>';
                data.forEach(d => {
                    options += `<option value="${d.id}">${d.nombre}</option>`;
                });
                docenteSelect.innerHTML = options;
            })
            .catch(() => {
                docenteSelect.innerHTML = '<option value="">Error al cargar</option>';
            });
    });
});
</script>




@endsection

@push('js')
<script>
$(document).ready(function() {
    console.log("JS del dashboard administrativo cargado");

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
            }, index * 100);
        });
    }

    // Efectos hover mejorados para metric cards
    $('.metric-card').hover(
        function() {
            $(this).find('.metric-icon').css({
                'transform': 'scale(1.1) rotate(5deg)',
                'transition': 'all 0.3s ease'
            });
            $(this).find('.metric-value').css({
                'color': 'var(--primary-color)',
                'transform': 'scale(1.05)'
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

    // Efectos para la tabla de estudiantes
    $('#tablaEstudiantes tbody tr').hover(
        function() {
            $(this).css({
                'border-left': '4px solid var(--primary-color)',
                'transition': 'all 0.3s ease'
            });
        },
        function() {
            $(this).css({
                'border-left': 'none'
            });
        }
    );

    // Animación para badges de estado
    $('.status-badge').hover(
        function() {
            $(this).css({
                'transform': 'scale(1.05)',
                'box-shadow': 'var(--shadow-sm)'
            });
        },
        function() {
            $(this).css({
                'transform': 'scale(1)',
                'box-shadow': 'none'
            });
        }
    );

    // Efecto loading en formulario de filtros
    $('form').on('submit', function() {
        const button = $(this).find('button[type="submit"]');
        button.html('<i class="bi bi-hourglass-split"></i> Filtrando...');
        button.prop('disabled', true);
        
        // Mostrar indicador de carga
        $('.dashboard-card-body').css({
            'opacity': '0.7',
            'pointer-events': 'none'
        });
    });

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

    // Mejorar búsqueda en tabla con highlight
    $('#filtroTabla').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('#tablaEstudiantes tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            const isMatch = rowText.includes(searchTerm);
            
            $(this).toggle(isMatch);
            
            if (isMatch && searchTerm.length > 0) {
                $(this).css({
                    'animation': 'highlight 0.5s ease',
                    'background-color': 'rgba(30, 58, 138, 0.05)'
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
        const visibleRows = $('#tablaEstudiantes tbody tr:visible').length;
        if (visibleRows === 0 && searchTerm.length > 0) {
            if ($('#no-results').length === 0) {
                $('#tablaEstudiantes tbody').append(`
                    <tr id="no-results">
                        <td colspan="8" class="empty-state">
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

    // Ejecutar animaciones al cargar
    setTimeout(animateMetrics, 300);

    // CSS adicional para animaciones
    $('head').append(`
        <style>
            @keyframes highlight {
                0% { background-color: rgba(30, 58, 138, 0.2); }
                50% { background-color: rgba(30, 58, 138, 0.1); }
                100% { background-color: rgba(30, 58, 138, 0.05); }
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
            
            .table tbody tr {
                transition: all 0.2s ease;
            }
            
            .form-select:focus,
            .form-control:focus {
                transform: scale(1.02);
            }
        </style>
    `);
});
</script>
@endpush
