{{-- Componente de Filtros de Búsqueda --}}
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
                    
                </select>
            </div>
            <div class="col-md-3">
                <label for="escuela" class="form-label">Escuela:</label>
                <select id="escuela" name="escuela" class="form-select">
                    <option value="">-- Todas --</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end justify-content-end">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-filter"></i> 
                    Filtrar Datos
                </button>
            </div>
        </div>
    </form>
</div>