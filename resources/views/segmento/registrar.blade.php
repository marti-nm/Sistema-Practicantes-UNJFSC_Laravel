@extends('template')
@section('title', 'Registro de Usuarios')
@section('subtitle', 'Agregar nuevos usuarios al sistema')

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
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .registration-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 0;
    }

    .registration-card {
        background: var(--surface-color);
        border: 2px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        cursor: pointer;
        height: 320px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .registration-card:before {
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

    .registration-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-color);
    }

    .registration-card:hover:before {
        transform: scaleX(1);
    }

    .registration-icon {
        font-size: 4rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .registration-card:hover .registration-icon {
        color: var(--primary-light);
        transform: scale(1.1);
    }

    .registration-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.025em;
    }

    .registration-subtitle {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-top: 0.5rem;
        margin-bottom: 0;
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

    .form-control:disabled {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: var(--text-secondary);
    }

    /* Button Styles */
    .btn {
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
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

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-success:hover {
        background: #047857;
        color: white;
    }

    /* File Upload Styles */
    .file-upload-container {
        border: 2px dashed var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.2s ease;
        background: var(--background-color);
        position: relative;
    }

    .file-upload-container:hover {
        border-color: var(--primary-color);
        background: rgba(30, 58, 138, 0.02);
    }

    .file-upload-container::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 48px;
        height: 48px;
        opacity: 0.1;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23059669' viewBox='0 0 16 16'%3E%3Cpath d='M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z'/%3E%3Cpath d='M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        pointer-events: none;
    }

    .file-name {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-top: 0.5rem;
        font-style: italic;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .registration-container {
            padding: 1rem 0;
        }
        
        .registration-card {
            height: 280px;
            padding: 1.5rem;
        }
        
        .registration-icon {
            font-size: 3rem;
        }
        
        .registration-title {
            font-size: 1.25rem;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
    }

    /* Estilos adicionales para mejor integración */
    .modal-dialog.modal-lg {
        max-width: 900px;
    }

    /* Mejoras en formularios */
    .form-row {
        margin-bottom: 1rem;
    }

    /* Indicadores de campos obligatorios */
    .form-group label::after {
        content: '*';
        color: var(--danger-color);
        margin-left: 4px;
        font-weight: 600;
    }

    .form-group label[for="departamento"]::after,
    .form-group label[for="correo_inst"]::after {
        display: none;
    }

    /* Estados de validación */
    .form-control.is-valid {
        border-color: var(--success-color);
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .form-control.is-invalid {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    /* Mejoras en los select */
    .form-control option {
        padding: 0.5rem;
        font-weight: 500;
    }

    /* Loading states */
    .btn.loading {
        position: relative;
        color: transparent;
    }

    .btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 1rem;
        height: 1rem;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        color: white;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* ...existing styles... */
</style>
@endpush

@section('content')
<div class="registration-container">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-6 mb-4">
            <div class="registration-card" data-toggle="modal" data-target="#modalRegistro">
                <i class="bi bi-person-plus registration-icon"></i>
                <h3 class="registration-title">Añadir Usuario</h3>
                <p class="registration-subtitle">Registrar un nuevo usuario individualmente</p>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6 col-md-6 mb-4">
            <div class="registration-card" data-toggle="modal" data-target="#modalCargaMasiva">
                <i class="bi bi-people registration-icon"></i>
                <h3 class="registration-title">Carga Masiva</h3>
                <p class="registration-subtitle">Importar múltiples usuarios desde archivo CSV</p>
            </div>
        </div>
    </div>
</div>

@php
    $id_semestre = session('semestre_actual_id');
@endphp

<!--Carga_Masiva-->
<div class="modal fade" id="modalCargaMasiva" tabindex="-1" role="dialog" aria-labelledby="modalCargaMasivaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCargaMasivaLabel">
                    <i class="bi bi-people me-2"></i>
                    Carga Masiva de Usuarios
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formUsuarioMasivo" enctype="multipart/form-data" action="{{ route('usuarios.masivos.store') }}" method="POST">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    @csrf
                    @method('POST')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rol">Tipo de Usuario</label>
                                <select class="form-control" id="rolMasivo" name="rol" required onchange="toggleFacultadEscuela('facultadEscuelaContainerMasivo')">
                                    <option value="">Seleccione un tipo de usuario</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archivo" class="d-block mb-2">Archivo CSV</label>
                                <div class="file-upload-container" onclick="document.getElementById('archivo').click()">
                                    <i class="bi bi-cloud-upload me-2"></i>
                                    <span class="file-upload-text">Seleccionar Archivo</span>
                                    <div class="file-name" id="archivo-nombre">Ningún archivo seleccionado</div>
                                    <input type="file" class="d-none" id="archivo" name="archivo" accept=".csv" required 
                                        onchange="document.getElementById('archivo-nombre').textContent = this.files[0].name">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="facultadEscuelaContainerMasivo">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="facultadMasiva">Facultad</label>
                                    <select class="form-control" id="facultadMasiva" name="facultad">
                                        <option value="">Seleccione una facultad</option>
                                        @foreach($facultades as $facultad)
                                            <option value="{{ $facultad->id }}">{{ $facultad->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="escuelaMasiva">Escuela</label>
                                    <select class="form-control" id="escuelaMasiva" name="escuela" disabled>
                                        <option value="">Seleccione una escuela</option>
                                        @foreach($escuelas as $escuela)
                                            <option value="{{ $escuela->id }}" data-facultad="{{ $escuela->facultad_id }}" hidden>
                                                {{ $escuela->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Cerrar
                </button>
                <button type="submit" form="formUsuarioMasivo" class="btn btn-primary">
                    <i class="bi bi-upload me-2"></i>
                    Importar Usuarios
                </button>
            </div>
        </div>
    </div>
</div>
<!--Fin Carga_Masiva-->

<!--Registro-->
<div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog" aria-labelledby="modalRegistroLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroLabel">
                    <i class="bi bi-person-plus me-2"></i>
                    Añadir Usuario {{ $id_semestre }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Sección de BÚSQUEDA (Siempre Visible) -->
                    <div id="search-input-container">
                        <h6 class="mb-3 text-primary"><i class="bi bi-search me-1"></i> 1. Verificar Usuario Existente</h6>
                        <div class="row g-3 align-items-end">
                            <!-- Selector DNI/CÓDIGO -->
                            <div class="col-md-4">
                                <label for="searchType" class="form-label">Buscar por:</label>
                                <select id="searchType" class="form-control">
                                    <option value="dni">DNI</option>
                                    <option value="codigo" selected>Código</option>
                                </select>
                            </div>
                            <!-- Campo de Búsqueda -->
                            <div class="col-md-5">
                                <label for="searchValue" class="form-label">Valor de Búsqueda</label>
                                <input type="text" class="form-control" id="searchValue" placeholder="Ingrese Código o DNI" minlength="8" maxlength="10" required>
                            </div>
                            <!-- Botón de Verificación -->
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-success p-3 w-100" id="btnVerify" onclick="verifyUser()">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Verificar
                                </button>
                            </div>
                        </div>
                        
                        <!-- Mensaje de Resultado de Búsqueda -->
                        <div id="searchResult" class="mt-3"></div>
                    </div>
                <form id="formRegistro" action="{{ route('personas.store') }}" method="POST">
                    @csrf
                    <!-- Campo Oculto para el ID del Semestre -->
                    <input type="hidden" name="id_semestre" value="{{ $id_semestre }}">
                    
                    <!-- CAMPO CLAVE: ID de Persona. Será llenado por JS si la persona existe. -->
                    <input type="hidden" id="personaId" name="persona_id">

                    <!-- Sección de DATOS PERSONALES (Oculta por defecto) -->
                    <div id="personalDataContainer" style="display: none;" class="section-box">
                        <h6 class="mb-3 text-secondary"><i class="bi bi-person-lines-fill me-1"></i> 2. Datos Personales</h6>
                        
                        <!-- Campos solo lectura para existentes / Editables para nuevos -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="codigo" class="form-label">Código</label>
                                <input type="tel" class="form-control" id="codigo" name="codigo" maxlength="10" required disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="dni" class="form-label">DNI</label>
                                <input type="tel" class="form-control" id="dni" name="dni" maxlength="8" required disabled>
                            </div>                            
                            <div class="col-md-4">
                                <label for="nombres" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="celular" class="form-label">Celular</label>
                                <input type="tel" class="form-control" id="celular" name="celular" required disabled maxlength="9">
                            </div>
                            <div class="col-md-4">
                                <label for="correo_inst" class="form-label">Correo Institucional</label>
                                <input type="email" class="form-control" id="correo_inst" name="correo_inst" placeholder="ejemplo@unjfsc.edu.pe" required disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="sexo" class="form-label">Género</label>
                                <select class="form-control" id="sexo" name="sexo" required disabled>
                                    <option value="">Seleccione su género</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="provincia">Provincia</label>
                                    <select class="form-control" id="provincia" name="provincia" required>
                                        <option value="">Seleccione una provincia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="distrito">Distrito</label>
                                    <select class="form-control" id="distrito" name="distrito" required disabled>
                                        <option value="">Seleccione un distrito</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Sección de ASIGNACIÓN (Habilitada en ambos casos, pero con lógica) -->
                    <div id="assignmentContainer" class="section-box mt-4">
                        <h6 class="mb-3 text-secondary"><i class="bi bi-clipboard-check-fill me-1"></i> 3. Asignación y Rol</h6>
                        <div class="row g-3">
                            <!-- Rol -->
                            <div class="col-md-4">
                                <label for="rolRegistro" class="form-label">Tipo de Usuario (Rol)</label>
                                <select class="form-control" id="rolRegistro" name="rol" required onchange="toggleFacultadEscuela('facultadEscuelaContainerRegistro'); completarCorreo();">
                                    <option value="">Seleccione un tipo de usuario</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Facultad -->
                            <div class="col-md-4">
                                <label for="facultadRegistro" class="form-label">Facultad</label>
                                <select class="form-control" id="facultadRegistro" name="facultad" >
                                    <option value="">Seleccione una facultad</option>
                                    @foreach($facultades as $facultad)
                                        <option value="{{ $facultad->id }}">{{ $facultad->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Escuela -->
                            <div class="col-md-4">
                                <label for="escuelaRegistro" class="form-label">Escuela</label>
                                <select class="form-control" id="escuelaRegistro" name="escuela" disabled>
                                    <option value="">Seleccione una escuela</option>
                                    @foreach($escuelas as $escuela)
                                        <option value="{{ $escuela->id }}" data-facultad="{{ $escuela->facultad_id }}" hidden>
                                            {{ $escuela->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Cerrar
                </button>
                <button type="submit" form="formRegistro" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>
                    Registrar Usuario
                </button>
            </div>
        </div>
    </div>
</div>
<!--Fin Registro-->

@endsection

@push('js')
<script>
    function toggleFacultadEscuela(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        // Find the closest form and then find the role select within that form
        const form = container.closest('form');
        let rolSelect;
        
        // Handle both modals
        if (form.id === 'formUsuarioMasivo') {
            rolSelect = document.getElementById('rolMasivo');
        } else if (form.id === 'formRegistro') {
            rolSelect = document.getElementById('rolRegistro');
        }
        
        if (!rolSelect) return;
        
        const selectedRole = parseInt(rolSelect.value);
        
        // Show/hide based on selected role (2 or 3)
        /*if (selectedRole === 2) {
            container.style.display = 'none';
        } else {
            container.style.display = 'block';
        }*/
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize both containers
        toggleFacultadEscuela('facultadEscuelaContainerMasivo');
        toggleFacultadEscuela('facultadEscuelaContainerRegistro');
        
        // Add event listeners for select changes
        document.getElementById('rolMasivo')?.addEventListener('change', function() {
            toggleFacultadEscuela('facultadEscuelaContainerMasivo');
        });
        
        document.getElementById('rolRegistro')?.addEventListener('change', function() {
            toggleFacultadEscuela('facultadEscuelaContainerRegistro');
        });
    });
</script>
<script src="{{ asset('js/cuadro_registro_user.js') }}"></script>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /**
     * Configura el filtrado dinámico de escuelas basado en la facultad seleccionada.
     * @param {string} facultySelectId - El ID del <select> de facultades.
     * @param {string} schoolSelectId - El ID del <select> de escuelas.
     */
    function setupDynamicSchools(facultySelectId, schoolSelectId) {
        const facultySelect = document.getElementById(facultySelectId);
        const schoolSelect = document.getElementById(schoolSelectId);

        if (!facultySelect || !schoolSelect) {
            console.warn(`No se encontraron los selectores: ${facultySelectId} o ${schoolSelectId}`);
            return;
        }

        facultySelect.addEventListener('change', function () {
            const selectedFacultyId = this.value;
            const schoolOptions = schoolSelect.options;

            // Resetea y deshabilita el selector de escuelas
            schoolSelect.value = '';
            schoolSelect.disabled = true;

            if (selectedFacultyId) {
                let hasVisibleSchools = false;
                // Itera sobre las opciones de escuela para mostrarlas/ocultarlas
                for (let i = 0; i < schoolOptions.length; i++) {
                    const option = schoolOptions[i];
                    if (option.value === '') continue; // No tocar el placeholder

                    if (option.getAttribute('data-facultad') === selectedFacultyId) {
                        option.hidden = false;
                        hasVisibleSchools = true;
                    } else {
                        option.hidden = true;
                    }
                }
                // Habilita el selector de escuelas solo si hay opciones disponibles
                if (hasVisibleSchools) {
                    schoolSelect.disabled = false;
                }
            }
        });
    }

    // Aplicar la lógica a ambos modales
    setupDynamicSchools('facultadRegistro', 'escuelaRegistro'); // Para el modal de registro individual
    setupDynamicSchools('facultadMasiva', 'escuelaMasiva');   // Para el modal de carga masiva
    

});

    const ELEMENTS = {
        form: document.getElementById('formRegistro'),
        searchType: document.getElementById('searchType'),
        searchValue: document.getElementById('searchValue'),
        btnVerify: document.getElementById('btnVerify'),
        searchResult: document.getElementById('searchResult'),
        personalDataContainer: document.getElementById('personalDataContainer'),
        btnSubmit: document.querySelector('#modalRegistro .modal-footer button[type="submit"]'), // Obtener el botón de submit
        
        // Campos del formulario
        inputPersonaId: document.getElementById('personaId'), 
        inputDni: document.getElementById('dni'),
        inputCodigo: document.getElementById('codigo'),
        inputNombres: document.getElementById('nombres'),
        inputApellidos: document.getElementById('apellidos'),
        
        // Campos de Asignación
        rolRegistro: document.getElementById('rolRegistro'),
        facultadRegistro: document.getElementById('facultadRegistro'),
        escuelaRegistro: document.getElementById('escuelaRegistro'),
        
        // Lista de todos los campos de datos personales (excepto los de búsqueda)
        personalInputs: [
            document.getElementById('dni'),
            document.getElementById('codigo'),
            document.getElementById('nombres'),
            document.getElementById('apellidos'),
            document.getElementById('celular'),
            document.getElementById('correo_inst'),
            document.getElementById('sexo'),
            document.getElementById('provincia'),
            document.getElementById('distrito'),
        ]
    };

    /**
     * Establece el estado (habilitado/deshabilitado) y el valor de los campos de datos personales.
     * @param {boolean} disabled - Si los campos deben estar deshabilitados (true) o habilitados (false).
     * @param {object} persona - Objeto con los datos de la persona si existe, o null si es nuevo.
     */
    function setPersonalDataState(disabled, persona = null) {
        ELEMENTS.personalInputs.forEach(input => {
            // Habilitar/Deshabilitar todos los campos
            input.disabled = disabled;
            
            // Manejar el atributo 'required'
            if (disabled) {
                input.removeAttribute('required');
            } else if (input.id !== 'provincia' && input.id !== 'distrito') {
                // Re-establecer required solo para campos obligatorios si son nuevos
                input.setAttribute('required', 'required');
            }
            
            // Rellenar valores si la persona existe
            if (persona) {
                // Usamos input.name ya que coincide con las claves del objeto persona
                input.value = persona[input.name] || ''; 
                if (input.tagName === 'SELECT' && persona[input.name]) {
                    input.value = persona[input.name];
                }
            } else {
                // Limpiar valores para nuevo registro, excepto los rellenados por verifyUser
                if (input.id !== 'dni' && input.id !== 'codigo') {
                    input.value = '';
                }
            }
        });

        // Los campos de Asignación se habilitan solo si el proceso está listo para un submit
        ELEMENTS.rolRegistro.disabled = false;
        ELEMENTS.facultadRegistro.disabled = false;
        ELEMENTS.escuelaRegistro.disabled = true; // Se habilita con la lógica de escuelas
    }

    /**
     * Resetea el formulario a su estado inicial.
     */
    function resetForm() {
        ELEMENTS.searchResult.innerHTML = '';
        ELEMENTS.personalDataContainer.style.display = 'none';
        ELEMENTS.inputPersonaId.value = '';
        ELEMENTS.btnSubmit.disabled = true;
        ELEMENTS.rolRegistro.disabled = true;
        ELEMENTS.facultadRegistro.disabled = true;
        ELEMENTS.escuelaRegistro.disabled = true;
    }

    // Verificar usuario existente btnVerify
    function verifyUser() {
        const searchType = ELEMENTS.searchType.value;
        const searchValue = ELEMENTS.searchValue.value.trim();
        
        if (!searchValue) {
            ELEMENTS.searchResult.innerHTML = `<div class="alert alert-warning">Ingrese un valor de ${searchType.toUpperCase()} para buscar.</div>`;
            resetForm();
            return;
        }

        // Limpia resultados previos y prepara el botón
        ELEMENTS.searchResult.innerHTML = '';
        ELEMENTS.personalDataContainer.style.display = 'none';
        ELEMENTS.btnVerify.disabled = true;
        ELEMENTS.btnVerify.classList.add('loading');
        ELEMENTS.btnSubmit.disabled = true;
        
        // Limpia el ID de persona anterior
        ELEMENTS.inputPersonaId.value = '';

        // Obtener el token CSRF del meta tag (asumiendo que está en el layout principal)
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fetch a la ruta de verificación
        fetch('{{ route('personas.verificar') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                type: searchType,
                value: searchValue,
                semestre_id: document.querySelector('input[name="id_semestre"]').value
            })
        }).then(response => {
            ELEMENTS.btnVerify.disabled = false;
            ELEMENTS.btnVerify.classList.remove('loading');
            if (!response.ok) throw new Error('Error de red o servidor.');
            return response.json();
        })
        .then(data => {
            
            if (data.found) {
                // ----------------------------------------------------
                // A. USUARIO ENCONTRADO
                // ----------------------------------------------------
                
                // 1. Verificar si ya fue asignado
                if (data.already_assigned) {
                    ELEMENTS.searchResult.innerHTML = `<div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        Usuario encontrado, pero **YA ESTÁ ASIGNADO** a este semestre.
                    </div>`;
                    // Deshabilitar submit y mantener campos en solo lectura
                    setPersonalDataState(true, data.persona); 
                    ELEMENTS.btnSubmit.disabled = true;
                } else {
                    ELEMENTS.searchResult.innerHTML = `<div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Usuario encontrado: <strong>${data.persona.nombres} ${data.persona.apellidos}</strong>. 
                        Proceda a la Asignación (Paso 3).
                    </div>`;

                    // Cargar datos y deshabilitar todos los campos personales
                    setPersonalDataState(true, data.persona);

                    // Guardar el ID de la persona encontrada
                    ELEMENTS.inputPersonaId.value = data.persona.id;
                    ELEMENTS.btnSubmit.disabled = false;
                }


            } else {
                // ----------------------------------------------------
                // B. NUEVO USUARIO
                // ----------------------------------------------------
                ELEMENTS.searchResult.innerHTML = `<div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    No se encontró usuario. Complete los datos personales y asigne el rol.
                </div>`;

                setPersonalDataState(false, null);

                if (searchType === 'dni') {
                    ELEMENTS.inputDni.value = searchValue; 
                    //ELEMENTS.inputDni.disabled = true;
                    ELEMENTS.inputDni.readOnly = true;
                    ELEMENTS.inputCodigo.focus();          
                } else {
                    ELEMENTS.inputCodigo.value = searchValue; 
                    //ELEMENTS.inputCodigo.disabled = true;  
                    ELEMENTS.inputCodigo.readOnly = true;
                    ELEMENTS.inputDni.focus();             
                }
                
                ELEMENTS.inputPersonaId.value = '';
                ELEMENTS.btnSubmit.disabled = false;
            }

            // Mostrar sección de datos personales y asignación
            ELEMENTS.personalDataContainer.style.display = 'block';
        })
        .catch(error => {
            console.error('Error al verificar usuario:', error);
            ELEMENTS.btnVerify.disabled = false;
            ELEMENTS.btnVerify.classList.remove('loading');
            ELEMENTS.searchResult.innerHTML = `<div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Ocurrió un error al verificar el usuario.
            </div>`;
            ELEMENTS.btnSubmit.disabled = true;
        });
    }
    </script>
@endpush
