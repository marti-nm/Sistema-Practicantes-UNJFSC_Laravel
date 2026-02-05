@extends('template')
@section('title', 'Gestión de Usuarios')
@section('subtitle', 'Administrar y visualizar información de usuarios')

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

    .docentes-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0;
    }

    /* Card Principal */
    .docentes-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .docentes-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .docentes-card-header {
        background: linear-gradient(135deg, var(--surface-color) 0%, #f8fafc 100%);
        border-bottom: 2px solid var(--border-color);
        padding: 1.5rem 2rem;
        position: relative;
    }

    .docentes-card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .docentes-card-title {
        font-size: 1.375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-transform: none;
    }

    .docentes-card-title i {
        color: var(--primary-color);
        font-size: 1.25rem;
    }

    .docentes-card-body {
        padding: 1.5rem;
    }

    /* Tabla Moderna */
    .table-container {
        background: var(--surface-color);
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table {
        margin: 0;
        border: none;
        font-size: 0.9rem;
    }

    .table thead th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: none;
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        color: var(--text-primary);
        padding: 1rem 0.75rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(30, 58, 138, 0.02);
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Botones de Acción */
    .btn {
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem; /* Reducido */
        font-size: 0.75rem; /* Tamaño más pequeño */
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem; /* Ligero espacio entre ícono y texto */
        margin: 0 0.125rem;
        min-width: 30px;  /* Opcional: controla el ancho mínimo */
        height: 30px;
    }

    .btn-info {
        background: var(--info-color);
        color: white;
    }

    .btn-info:hover {
        background: #0e7490;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .btn-danger:hover {
        background: #991b1b;
        border-color: #991b1b;
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background: #047857;
        color: white;
    }

    .btn-secondary {
        background: var(--secondary-color);
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
        color: white;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        color: white;
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        border-radius: 1rem 1rem 0 0;
        padding: 1.5rem 2rem;
        border-bottom: none;
    }

    .modal-title {
        font-size: 1.375rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-header .close {
        background: transparent;
        border: none;
        font-size: 1.2rem;
        color: #ffffffcc;
        padding: 0.5rem 0.7rem;
        border-radius: 50%;
        transition: all 0.3s ease-in-out;
        position: absolute;
        top: 15px;
        right: 15px;
    }

    .modal-header .close:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
        transform: rotate(90deg);
        box-shadow: 0 0 5px #ffffff88;
    }

    .modal-body {
        padding: 2rem;
        background: var(--surface-color);
    }

    .modal-footer {
        background: var(--background-color);
        border-top: 1px solid var(--border-color);
        border-radius: 0 0 1rem 1rem;
        padding: 1.5rem 2rem;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: block;
    }

    .form-control {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        padding: 0.875rem 1rem;
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

    .form-control[readonly], .form-control[disabled] {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: var(--text-secondary);
    }

    /* Badge mejorado */
    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Links mejorados */
    .text-decoration-none {
        color: var(--primary-color);
        transition: color 0.2s ease;
    }

    .text-decoration-none:hover {
        color: var(--primary-light);
        text-decoration: none !important;
    }

    /* Botones con espaciado */
    .btn + .btn {
        margin-left: 0.25rem;
    }

    /* Confirmación de eliminación mejorada */
    .btn-danger:hover {
        background: #991b1b;
        border-color: #991b1b;
    }

    /* Estados de formulario */
    .form-control:not([readonly]):not([disabled]) {
        border-color: var(--primary-color);
        background: var(--surface-color);
    }

    .form-control:not([readonly]):not([disabled]):focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Mejoras en la transición de estados */
    .form-control {
        transition: all 0.3s ease;
    }

    /* Estilo para campos deshabilitados en modo edición */
    .form-control[disabled] {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #64748b;
        opacity: 0.8;
    }

    /* Mejoras para la sección de foto */
    .photo-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1.5rem;
        padding: 2rem 1.5rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 1rem;
        border: 2px dashed var(--border-color);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .photo-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .photo-section:hover {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, rgba(30, 58, 138, 0.02) 0%, rgba(59, 130, 246, 0.02) 100%);
        box-shadow: var(--shadow-sm);
    }

    .photo-section:hover::before {
        transform: scaleX(1);
    }

    .profile-photo {
        width: 180px;
        height: 180px;
        border-radius: 1rem;
        object-fit: cover;
        border: 4px solid var(--surface-color);
        box-shadow: var(--shadow-lg);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .profile-photo:hover {
        transform: scale(1.05);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border-color: var(--primary-color);
    }

    .default-avatar {
        width: 180px;
        height: 180px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 4rem;
        box-shadow: var(--shadow-lg);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .default-avatar::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: rotate(45deg);
        transition: all 0.6s ease;
        opacity: 0;
    }

    .default-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 20px 40px rgba(30, 58, 138, 0.3);
    }

    .default-avatar:hover::before {
        opacity: 1;
        animation: shimmer 1.5s ease-in-out;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    /* Upload Button Mejorado */
    .upload-btn {
        background: var(--surface-color);
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
        cursor: pointer;
        font-weight: 600;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 160px;
        justify-content: center;
        box-shadow: var(--shadow-sm);
    }

    .upload-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(30, 58, 138, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .upload-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-light);
        text-decoration: none;
    }

    .upload-btn:hover::before {
        left: 100%;
    }

    .upload-btn:active {
        transform: translateY(0);
        box-shadow: var(--shadow-sm);
    }

    .upload-btn i {
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .upload-btn:hover i {
        transform: scale(1.1);
    }

    /* Información de archivo mejorada */
    .photo-section small {
        font-size: 0.8rem;
        color: var(--text-secondary);
        text-align: center;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 0.5rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.5);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .photo-section small::before {
        content: '\f1c5'; /* Bootstrap icon info-circle */
        font-family: 'bootstrap-icons';
        color: var(--info-color);
        font-size: 0.875rem;
    }

    /* Información de archivo estructurada */
    .photo-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        width: 100%;
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem;
        border-radius: 0.75rem;
        border: 1px solid rgba(226, 232, 240, 0.6);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-sm);
    }

    .photo-info .info-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-secondary);
        padding: 0.25rem 0;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .photo-info .info-item:hover {
        background: rgba(30, 58, 138, 0.05);
        color: var(--primary-color);
    }

    .photo-info .info-item i {
        color: var(--primary-color);
        font-size: 0.875rem;
        min-width: 16px;
        transition: transform 0.2s ease;
    }

    .photo-info .info-item:hover i {
        transform: scale(1.1);
    }

    .photo-info .info-item span {
        font-weight: 500;
        letter-spacing: 0.025em;
    }

    /* Container para la foto con posición relativa */
    .photo-container {
        position: relative;
        display: inline-block;
        border-radius: 1rem;
        overflow: hidden;
    }

    /* Estados de carga y error */
    .photo-loading {
        position: relative;
    }

    .photo-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 2rem;
        height: 2rem;
        margin: -1rem 0 0 -1rem;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-top: 3px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 10;
    }

    .photo-error {
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 0.5rem;
    }

    .photo-error i {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .photo-error span {
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
    }

    /* Validación visual */
    .photo-section.valid {
        border-color: var(--success-color);
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.02) 0%, rgba(16, 185, 129, 0.02) 100%);
    }

    .photo-section.valid::before {
        background: linear-gradient(90deg, var(--success-color), #10b981);
        transform: scaleX(1);
    }

    .photo-section.invalid {
        border-color: var(--danger-color);
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.02) 0%, rgba(239, 68, 68, 0.02) 100%);
    }

    .photo-section.invalid::before {
        background: linear-gradient(90deg, var(--danger-color), #ef4444);
        transform: scaleX(1);
    }

    /* Tooltip personalizado para información */
    .photo-tooltip {
        position: relative;
        cursor: help;
    }

    .photo-tooltip::before {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: var(--text-primary);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .photo-tooltip::after {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(2px);
        border: 4px solid transparent;
        border-top-color: var(--text-primary);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .photo-tooltip:hover::before,
    .photo-tooltip:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-8px);
    }

    /* Animaciones de entrada */
    .photo-section {
        animation: photoFadeIn 0.5s ease;
    }

    @keyframes photoFadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* ...existing photo styles... */
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        // Modal states
        editAPModalOpen: false,
        solicitudAPModalOpen: false,
        managementAPModalOpen: false,

        // Loading states
        loading: false,
        loadingEdit: false,

        // Data containers
        requireData: { id_ap: null, id_sa: null, state_ap: null, nombre_ap: null },
        solicitudData: { id: null, accion: '', justificacion: '' },
        editData: {},
        provincias: [],
        all_distritos: {},
        distritos_options: [],
        selectedProvincia: '',
        selectedDistrito: '',

        // API fetch methods
        async initLocations() {
            if (this.provincias.length > 0) return; // Already loaded
            try {
                const [provRes, distRes] = await Promise.all([
                    fetch('/data/provincias.json'),
                    fetch('/data/distritos.json')
                ]);
                const provData = await provRes.json();
                const distData = await distRes.json();
                this.provincias = provData.provincias || [];
                this.all_distritos = distData.distritos || {};
            } catch (error) {
                console.error('Error loading locations:', error);
            }
        },

        updateDistritos() {
            this.distritos_options = this.all_distritos[this.selectedProvincia] || [];
            // If the currently selected district is not in the new options, clear it
            if (!this.distritos_options.find(d => d.id == this.selectedDistrito)) {
                this.selectedDistrito = '';
            }
        },

        async fetchEditPersona(personaId) {
            this.loadingEdit = true;
            this.editData = {};
            this.selectedProvincia = '';
            this.selectedDistrito = '';

            try {
                // Load locations and data in parallel if possible, but we need locations to set selected
                await this.initLocations();

                const response = await fetch(`/api/persona/${personaId}`);
                this.editData = await response.json();

                // Logic to match Province (ID or Name)
                if (this.editData.provincia) {
                    let prov = this.provincias.find(p => p.id == this.editData.provincia);
                    if (!prov) {
                        prov = this.provincias.find(p => p.nombre.toLowerCase() === this.editData.provincia.toLowerCase());
                    }
                    if (prov) {
                        this.selectedProvincia = prov.id;
                        this.updateDistritos();

                        // Logic to match District
                        if (this.editData.distrito) {
                            let dist = this.distritos_options.find(d => d.id == this.editData.distrito);
                            if (!dist) {
                                dist = this.distritos_options.find(d => d.nombre.toLowerCase() === this.editData.distrito.toLowerCase());
                            }
                            if (dist) this.selectedDistrito = dist.id;
                        }
                    }
                }
            } catch (error) {
                console.error('Error fetching persona data:', error);
            } finally {
                this.loadingEdit = false;
            }
        },

        async fetchManagementAP(id) {
            this.loading = true;
            this.solicitudData = { id: null, accion: 'Cargando...', justificacion: 'Cargando...' };
            try {
                const response = await fetch(`/api/solicitud/getSolicitudAp/${id}`);
                const result = await response.json();
                this.solicitudData.id = result.id;
                this.solicitudData.accion = result.data?.opcion?.toUpperCase() || 'SIN DATOS';
                this.solicitudData.justificacion = result.motivo?.toUpperCase() || 'SIN DATOS';
            } finally { this.loading = false; }
        },

        // Modal open handlers
        openEditModal(data) {
            this.requireData = data;
            this.editAPModalOpen = true;
            this.fetchEditPersona(data.id_ap)
        },

        openSolicitudModal(data) {
            this.requireData = data;
            this.solicitudAPModalOpen = true;
        },

        openManagementModal(data) {
            this.requireData = data;
            this.managementAPModalOpen = true;
            this.fetchManagementAP(data.id_ap); // id_ap here is asignacion_persona id
        }
    }">
    <x-header-content
        title="Lista de {{ $cargo }}"
        subtitle="Gestionar y validar documentos académicos del {{ $cargo }}"
        icon="bi-person-badge-fill"
        :enableButton="true"
        msj="Registrar {{ $cargo }}"
        icon_msj="bi-person-badge-fill"
        route="usuario.registrar"
    />

    @if(Auth::user()->hasAnyRoles([1, 2]))
        <x-data-filter
            route="{{ $rutaFilter }}"
            :facultades="$facultades"
        />
    @endif

    @include('components.skeletonLoader-table')
    <div class="overflow-x-auto">
        <table id="tablaEstudiantes" class="w-full text-left border-collapse table-skeleton-ready rounded-t-2xl overflow-hidden">
            <thead>
                <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">#</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Email</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Apellidos y Nombres</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Facultad</th>
                    @if($cargo != "Sub Administrador")
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Escuela</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Sección</th>
                    @endif
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:bgdivide-slate-800 bg-white dark:bg-slate-900/50">
                @foreach ($personas as $index => $persona)
                <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                    <td class="px-6 py-2 text-center">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-6 py-2">
                        <span class="text-sm text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $persona->correo_inst }}</span>
                    </td>
                    <td class="px-6 py-2">
                        <span class="text-sm text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ strtoupper($persona->apellidos . ' ' . $persona->nombres) }}</span>
                    </td>
                    <td class="px-6 py-2">
                        <span class="text-sm text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $persona->asignacion_persona->seccion_academica->facultad->name }}</span>
                    </td>
                    @if($cargo != "Sub Administrador")
                        <td class="px-6 py-2">
                            <span class="text-sm text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $persona->asignacion_persona->seccion_academica->escuela->name }}</span>
                        </td>
                        <td class="px-6 py-2">
                            <span class="text-sm text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $persona->asignacion_persona->seccion_academica->seccion}}</span>
                        </td>
                    @endif
                    <td class="px-6 py-2 text-center">
                        @if($persona->asignacion_persona->state == 1 || $persona->asignacion_persona->state == 2)
                        <button type="button" class="btn btn-info"
                            @click="openEditModal({
                                id_ap: {{ $persona->id }},
                                id_sa: {{ $persona->asignacion_persona->seccion_academica->id }},
                                state_ap: {{ $persona->asignacion_persona->state }},
                                nombre_ap: '{{ $persona->apellidos . ' ' . $persona->nombres }}',
                                facultad: '{{ $persona->asignacion_persona->seccion_academica->facultad->name }}',
                                escuela: '{{ $persona->asignacion_persona->seccion_academica->escuela->name }}',
                                seccion: '{{ $persona->asignacion_persona->seccion_academica->seccion }}'
                            })">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        @if(!$semestre_bloqueado)
                        <button type="button" class="btn btn-danger"
                            @click="openSolicitudModal({
                                id_ap: {{ $persona->asignacion_persona->id }},
                                id_sa: {{ $persona->asignacion_persona->seccion_academica->id }},
                                state_ap: {{ $persona->asignacion_persona->state }},
                                nombre_ap: '{{ $persona->apellidos . ' ' . $persona->nombres }}'
                            })">
                            <i class="bi bi-person-x-fill"></i>
                        </button>
                        @endif
                        @elseif($persona->asignacion_persona->state == 3 && Auth::user()->hasAnyRoles([1,2]))
                        <button type="button" class="btn btn-secondary"
                            @click="openManagementModal({
                                id_ap: {{ $persona->asignacion_persona->id }},
                                id_sa: {{ $persona->asignacion_persona->seccion_academica->id }},
                                nombre_ap: '{{ $persona->apellidos . ' ' . $persona->nombres }}'
                            })">
                            <i class="bi bi-hourglass-bottom"></i>
                        </button>
                        @elseif($persona->asignacion_persona->state == 3)
                            <label class="badge badge-warning text-black">Pendiente</label>
                        @elseif($persona->asignacion_persona->state == 4)
                            <!-- button para habilitar -->
                            <button type="button" class="btn btn-warning"
                                @click="openSolicitudModal({
                                    id_ap: {{ $persona->asignacion_persona->id }},
                                    id_sa: {{ $persona->asignacion_persona->seccion_academica->id }},
                                    state_ap: {{ $persona->asignacion_persona->state }},
                                    nombre_ap: '{{ $persona->apellidos . ' ' . $persona->nombres }}'
                                })">
                                <i class="bi bi-person-check-fill"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('list_users.partials.modales_gestion_estado')
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaEstudiantes').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar escuela...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Sig.",
                "previous": "Ant."
            },
        },
        pageLength: 10,
        responsive: true,
        dom: '<"flex flex-col md:flex-row md:items-center justify-between gap-4 py-8 px-2"lf>rt<"flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4 pb-2 px-2"ip>',
        initComplete: function() {
            // Hide skeleton and show table
            $('#skeletonLoader').addClass('hidden');
            $('#tablaEstudiantes').addClass('dt-ready');
        }
    });
});
</script>
@endpush

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
    document.addEventListener("DOMContentLoaded", function () {
        const facultadSelect = document.getElementById('facultad');
        const escuelaSelect = document.getElementById('escuela');
        const docenteSelect = document.getElementById('docente');

        facultadSelect.addEventListener('change', function () {
            const facultadId = this.value;
            escuelaSelect.innerHTML = '<option value="">Cargando...</option>';
            docenteSelect.innerHTML = '<option value="">-- Todos --</option>';

            if (!facultadId) {
                escuelaSelect.innerHTML = '<option value="">-- Todas --</option>';
                return;
            }

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
            docenteSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!escuelaId) {
                docenteSelect.innerHTML = '<option value="">-- Todos --</option>';
                return;
            }

            fetch(`/api/docentes/${escuelaId}`)
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

        const semestreSelect = document.getElementById('semestre');

        docenteSelect.addEventListener('change', function () {
            const docenteId = this.value;
            semestreSelect.innerHTML = '<option value="">Cargando...</option>';

            if (!docenteId) {
                semestreSelect.innerHTML = '<option value="">-- Todos --</option>';
                return;
            }

            fetch(`/api/semestres/${docenteId}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Todos --</option>';
                    data.forEach(s => {
                        options += `<option value="${s.id}">${s.codigo}</option>`;
                    });

                    semestreSelect.innerHTML = options;
                })
                .catch(() => {
                    semestreSelect.innerHTML = '<option value="">Error al cargar</option>';
                });
        });
    });
</script>
@endpush
