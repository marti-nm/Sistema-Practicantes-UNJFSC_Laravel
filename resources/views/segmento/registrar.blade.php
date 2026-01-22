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
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" 
    x-data="{
        individualModalOpen: false,
        massModalOpen: false,
        loading: false,
        verified: false,
        userExists: false,
        assigned: false,
        
        formData: {
            rol: '',
            searchValue: '',
            persona_id: null,
            codigo: '',
            correo_inst: '',
            apellidos: '',
            nombres: '',
            sexo: '',
            dni: '',
            celular: '',
            provincia: '',
            distrito: '',
            facultad: '',
            escuela: '',
            seccion: '',
            id_semestre: '{{ session('semestre_actual_id') }}'
        },

        massFormData: {
            rol: '',
            facultad: '',
            escuela: '',
            seccion: '',
            fileName: 'Ningún archivo seleccionado'
        },

        escuelas: [],
        secciones: [],

        openIndividualModal() {
            this.resetForm();
            this.individualModalOpen = true;
        },

        openMassModal() {
            this.massFormData = {
                rol: '',
                facultad: '',
                escuela: '',
                seccion: '',
                fileName: 'Ningún archivo seleccionado'
            };
            this.escuelas = [];
            this.secciones = [];
            this.massModalOpen = true;
        },

        resetForm() {
            this.formData = {
                rol: '', searchValue: '', persona_id: null, codigo: '', correo_inst: '',
                apellidos: '', nombres: '', sexo: '', dni: '', celular: '',
                provincia: '', distrito: '', facultad: '', escuela: '', seccion: '',
                id_semestre: '{{ session('semestre_actual_id') }}'
            };
            this.verified = false; this.userExists = false; this.assigned = false;
            this.escuelas = []; this.secciones = [];
        },

        async verifyUser() {
            if(!this.formData.rol || !this.formData.searchValue) return;
            this.loading = true;
            try {
                const fullEmail = `${this.formData.searchValue}@unjfsc.edu.pe`;
                const response = await fetch(`/api/verificar/${fullEmail}`);
                const data = await response.json();
                
                this.verified = true;
                if(data.persona) {
                    this.userExists = true;
                    this.formData.persona_id = data.persona.id;
                    this.formData.nombres = data.persona.nombres;
                    this.formData.apellidos = data.persona.apellidos;
                    this.formData.sexo = data.persona.sexo;
                    this.formData.dni = data.persona.dni;
                    this.formData.codigo = data.persona.codigo;
                    this.formData.correo_inst = data.persona.correo_inst;
                    
                    if(data.asignacionExistente) {
                        this.assigned = true;
                    }
                } else {
                    this.userExists = false;
                    this.formData.persona_id = null;
                    this.formData.nombres = '';
                    this.formData.apellidos = '';
                    this.formData.sexo = '';
                    this.formData.dni = '';
                    if(/^\d+$/.test(this.formData.searchValue)) this.formData.codigo = this.formData.searchValue;
                    this.formData.correo_inst = fullEmail;
                }
            } catch(e) { 
                console.error('Error en verificación:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo completar la verificación en este momento.'
                });
            } finally {
                this.loading = false;
            }
        },

        async fetchEscuelas() {
            if(!this.formData.facultad) return;
            const res = await fetch(`/api/escuelas/${this.formData.facultad}`);
            this.escuelas = await res.json();
            this.secciones = [];
        },

        async fetchSecciones() {
            if(!this.formData.escuela) return;
            const res = await fetch(`/api/secciones/${this.formData.escuela}/${this.formData.id_semestre}`);
            this.secciones = await res.json();
        },

        async fetchMassEscuelas() {
            if(!this.massFormData.facultad) return;
            const res = await fetch(`/api/escuelas/${this.massFormData.facultad}`);
            this.escuelas = await res.json();
            this.secciones = [];
        },

        async fetchMassSecciones() {
            if(!this.massFormData.escuela) return;
            const res = await fetch(`/api/secciones/${this.massFormData.escuela}/${this.formData.id_semestre}`);
            this.secciones = await res.json();
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if(file) {
                this.massFormData.fileName = file.name;
            } else {
                this.massFormData.fileName = 'Ningún archivo seleccionado';
            }
        }
    }">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto items-stretch">
        <!-- Añadir Usuario Card -->
        <div class="group relative bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-2xl shadow-slate-200/50 dark:shadow-none border-1 border-slate-100 dark:border-slate-800 hover:border-blue-500/50 transition-all duration-500 cursor-pointer overflow-hidden flex flex-col items-center text-center hover:-translate-y-2"
            @click="openIndividualModal">
            
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-600 to-indigo-700 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-700 origin-left"></div>
            
            <div class="w-24 h-24 rounded-[2rem] bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-8 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500 shadow-inner border-1 border-blue-100/50 dark:border-blue-500/10">
                <i class="bi bi-person-plus text-5xl"></i>
            </div>
            
            <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none mb-4 uppercase">Añadir Usuario</h3>
            <p class="text-sm font-bold text-slate-400 dark:text-slate-500 leading-relaxed max-w-[240px]">
                Registra un nuevo usuario de forma individual completando la información personal y académica.
            </p>
            
            <div class="mt-10 flex items-center gap-3 text-blue-600 dark:text-blue-400 font-black text-[11px] uppercase tracking-[0.25em] opacity-0 group-hover:opacity-100 transition-all duration-500 translate-y-4 group-hover:translate-y-0">
                Iniciar Registro <i class="bi bi-arrow-right-short text-xl"></i>
            </div>

            <!-- Background Decoration -->
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors"></div>
        </div>

        <!-- Carga Masiva Card -->
        <div class="group relative bg-white dark:bg-slate-900 rounded-[2.5rem] p-10 shadow-2xl shadow-slate-200/50 dark:shadow-none border-1 border-slate-100 dark:border-slate-800 hover:border-emerald-500/50 transition-all duration-500 cursor-pointer overflow-hidden flex flex-col items-center text-center hover:-translate-y-2"
             @click="openMassModal">
            
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-700 origin-left"></div>
            
            <div class="w-24 h-24 rounded-[2rem] bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-8 group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500 shadow-inner border-1 border-emerald-100/50 dark:border-emerald-500/10">
                <i class="bi bi-people text-5xl"></i>
            </div>
            
            <h3 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight leading-none mb-4 uppercase">Carga Masiva</h3>
            <p class="text-sm font-bold text-slate-400 dark:text-slate-500 leading-relaxed max-w-[240px]">
                Optimiza el tiempo importando múltiples usuarios simultáneamente a través de un archivo CSV estructurado.
            </p>
            
            <div class="mt-10 flex items-center gap-3 text-emerald-600 dark:text-emerald-400 font-black text-[11px] uppercase tracking-[0.25em] opacity-0 group-hover:opacity-100 transition-all duration-500 translate-y-4 group-hover:translate-y-0">
                Subir Archivo <i class="bi bi-cloud-arrow-up text-xl"></i>
            </div>

            <!-- Background Decoration -->
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
        </div>
    </div>

    <div x-show="individualModalOpen"
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4"
        x-cloak>
        <x-backdrop-modal name="individualModalOpen" />
        <div x-show="individualModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden border-1 border-slate-100 dark:border-slate-800 flex flex-col max-h-[90vh]">
            
            <div class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-4 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-clipboard-data-fill text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-base font-black tracking-tight leading-none">Añadir Usuario Individual</h3>
                            <p class="text-blue-100/60 text-[9px] font-bold uppercase tracking-[0.2em] mt-1.5">Registro académico</p>
                        </div>
                    </div>
                    <button @click="individualModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-4 space-y-4 overflow-y-auto custom-scrollbar">
                <!-- PASO 1: VERIFICACIÓN -->
                <div class="bg-white dark:bg-slate-800/50 rounded-xl p-4 border-1 border-slate-100 dark:border-slate-700/50 shadow-sm">
                    <h6 class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                        <span class="w-5 h-5 rounded bg-blue-600 text-white flex items-center justify-center text-[9px]">1</span>
                        Verificar Usuario Existente
                    </h6>
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end text-left">
                        <div class="md:col-span-4 space-y-1">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Tipo de Usuario (Rol)</label>
                            <select x-model="formData.rol" class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none appearance-none">
                                <option value="">Seleccionar rol</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="md:col-span-6 space-y-1">
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Correo Institucional / Código</label>
                            <div class="relative group">
                                <input type="text" x-model="formData.searchValue" placeholder="Eje: 2020112233 o jperez" 
                                    class="w-full bg-white dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg pl-3 pr-28 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase">
                                    @unjfsc.edu.pe
                                </div>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <button @click="verifyUser()" :disabled="loading"
                                class="w-full h-[34px] bg-gradient-to-br from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-lg shadow-lg shadow-blue-500/20 active:scale-95 transition-all flex items-center justify-center disabled:opacity-50">
                                <template x-if="!loading">
                                    <i class="bi bi-search text-sm"></i>
                                </template>
                                <template x-if="loading">
                                    <div class="animate-spin rounded-full h-4 w-4 border-2 border-white/20 border-t-white"></div>
                                </template>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- si existe, mostrar mensaje de ya está asignacod y que verifque bien el usaurio. Caso contrario se comunique con el admin --}}
                <template x-if="userExists && assigned">
                    <div 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="relative overflow-hidden bg-amber-50/50 dark:bg-amber-900/10 border-1 border-amber-200 dark:border-amber-700 rounded-xl p-4 shadow-sm shadow-amber-100/50 group"
                    >
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                                <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-black text-amber-900 uppercase tracking-widest flex items-center gap-2">
                                    Advertencia
                                </h4>
                                <p class="mt-1 text-xs font-medium text-amber-700 leading-relaxed">
                                    El usuario ya existe y se encuentra asignado. Revise el usuario o contacte con el administrador.
                                </p>
                            </div>

                            <button @click="userExists = false" class="text-amber-400 hover:text-amber-600 transition-colors">
                                <i class="bi bi-x-lg text-xs"></i>
                            </button>
                        </div>
                    </div>
                </template>
                <!-- SECCIONES SIGUIENTES -->
                <div x-show="verified" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 text-left">
                    
                    <form action="{{ route('personas.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="persona_id" x-model="formData.persona_id">
                        <input type="hidden" name="rol" x-model="formData.rol">
                        <input type="hidden" name="id_semestre" x-model="formData.id_semestre">

                        <!-- PASO 2: DATOS PERSONALES -->
                        <div class="space-y-3">
                            <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-5 h-5 rounded bg-slate-200 dark:bg-slate-700 text-slate-500 flex items-center justify-center text-[9px]">2</span>
                                Información Personal
                            </h6>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Código</label>
                                    <input type="text" name="codigo" x-model="formData.codigo" :disabled="userExists" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold uppercase text-slate-600 disabled:opacity-60">
                                </div>
                                <div class="md:col-span-2 space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Correo Institucional</label>
                                    <input type="email" name="correo_inst" x-model="formData.correo_inst" :disabled="userExists" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold uppercase text-slate-600 disabled:opacity-60">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Apellidos</label>
                                    <input type="text" name="apellidos" x-model="formData.apellidos" :disabled="userExists" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold uppercase text-slate-600 disabled:opacity-60">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Nombres</label>
                                    <input type="text" name="nombres" x-model="formData.nombres" :disabled="userExists" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold uppercase text-slate-600 disabled:opacity-60">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Género</label>
                                    <select name="sexo" x-model="formData.sexo" :disabled="userExists" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold uppercase text-slate-600 disabled:opacity-60 appearance-none">
                                        <option value="">Seleccionar</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- PASO 3: ASIGNACIÓN -->
                        <div class="pt-4 space-y-3">
                            <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-5 h-5 rounded bg-slate-200 dark:bg-slate-700 text-slate-500 flex items-center justify-center text-[9px]">3</span>
                                Asignación Académica
                            </h6>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 bg-blue-50/30 dark:bg-blue-900/10 p-3 rounded-xl border-1 border-blue-100 dark:border-blue-900/30">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Facultad</label>
                                    <select name="facultad" x-model="formData.facultad" @change="fetchEscuelas()" required class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none">
                                        <option value="">Facultad</option>
                                        @foreach($facultades as $fac)
                                            <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Escuela</label>
                                    <select name="escuela" x-model="formData.escuela" @change="fetchSecciones()" :disabled="!formData.facultad" required class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none disabled:opacity-40">
                                        <option value="">Escuela</option>
                                        <template x-for="item in escuelas" :key="item.id">
                                            <option :value="item.id" x-text="item.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Sección</label>
                                    <select name="seccion" x-model="formData.seccion" :disabled="!formData.escuela" required class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 transition-all appearance-none disabled:opacity-40">
                                        <option value="">Sección</option>
                                        <template x-for="item in secciones" :key="item.id">
                                            <option :value="item.id" x-text="item.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-6 flex items-center justify-end gap-3">
                            <button type="button" @click="individualModalOpen = false" class="px-6 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all flex items-center gap-2">
                                <i class="bi bi-person-plus-fill text-sm"></i>
                                Completar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div x-show="massModalOpen"
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4"
        x-cloak>
        <x-backdrop-modal name="massModalOpen" />
        <div x-show="massModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden border-1 border-slate-100 dark:border-slate-800 flex flex-col max-h-[90vh]">
            
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-people-fill text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-base font-black tracking-tight leading-none">Carga Masiva de Usuarios</h3>
                            <p class="text-emerald-100/60 text-[9px] font-bold uppercase tracking-[0.2em] mt-1.5">Importación CSV</p>
                        </div>
                    </div>
                    <button @click="massModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-4 overflow-y-auto custom-scrollbar">
                <form id="formUsuarioMasivo" enctype="multipart/form-data" action="{{ route('usuarios.masivos.store') }}" method="POST" class="space-y-4">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    @csrf
                    @method('POST')
                    
                    <div class="bg-white dark:bg-slate-800/50 rounded-xl p-4 border-1 border-slate-100 dark:border-slate-700/50 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Tipo de Usuario</label>
                                <select x-model="massFormData.rol" id="rolMasivo" name="rol" required class="w-full bg-white dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all outline-none appearance-none">
                                    <option value="">Seleccionar tipo</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Archivo CSV</label>
                                <div class="relative group cursor-pointer" onclick="document.getElementById('archivo').click()">
                                    <div class="w-full bg-white dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg px-3 py-2 flex items-center justify-between group-hover:border-emerald-500 transition-all">
                                        <div class="flex items-center gap-2 overflow-hidden">
                                            <div class="w-6 h-6 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center shrink-0">
                                                <i class="bi bi-file-earmark-spreadsheet text-xs"></i>
                                            </div>
                                            <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 truncate" x-text="massFormData.fileName"></span>
                                        </div>
                                        <span class="text-[9px] font-black text-emerald-600 uppercase tracking-wider bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded">Examinar</span>
                                    </div>
                                    <input type="file" class="hidden" id="archivo" name="archivo" accept=".csv" required 
                                        @change="handleFileSelect($event)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Imagen del Modelo -->
                    <div x-show="['2','3','4'].includes(massFormData.rol)" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="bg-blue-50 dark:bg-blue-900/10 rounded-xl p-3 border-1 border-blue-100 dark:border-blue-800/30">
                        <p class="text-[9px] font-black uppercase tracking-widest text-blue-400 mb-2 flex items-center gap-2">
                            <i class="bi bi-info-circle-fill"></i> Formato Requerido
                        </p>
                        <img src="{{ asset('img/model-registro.png') }}" alt="Modelo de Registro" class="w-full rounded-lg shadow-sm">
                    </div>
                    
                    <!-- Sección de ASIGNACIÓN -->
                    <div x-show="massFormData.rol !== ''" 
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        
                        <div class="space-y-3">
                            <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-5 h-5 rounded bg-slate-200 dark:bg-slate-700 text-slate-500 flex items-center justify-center text-[9px]">3</span>
                                Asignación Académica
                            </h6>

                            @if(Auth::user()->hasAnyRoles([3]))
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border-1 border-slate-100 dark:border-slate-700">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Facultad</label>
                                        <select class="w-full bg-slate-100 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-500 cursor-not-allowed" name="facultad" readonly>
                                            <option value="{{ $ap->seccion_academica->facultad->id }}">{{ $ap->seccion_academica->facultad->name }}</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Escuela</label>
                                        <select class="w-full bg-slate-100 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-500 cursor-not-allowed" name="escuela" readonly>
                                            <option value="{{ $ap->seccion_academica->escuela->id }}">{{ $ap->seccion_academica->escuela->name }}</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Sección</label>
                                        <select class="w-full bg-slate-100 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-500 cursor-not-allowed" name="seccion" readonly>
                                            <option value="{{ $ap->seccion_academica->id }}">{{ $ap->seccion_academica->seccion }}</option>
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border-1 border-slate-100 dark:border-slate-700">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Facultad</label>
                                        <select x-model="massFormData.facultad" @change="fetchMassEscuelas()" name="facultad" required class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 dark:text-slate-50 outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none">
                                            <option value="">Seleccione una facultad</option>
                                            @foreach($facultades as $fac)
                                                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Escuela</label>
                                        <select x-model="massFormData.escuela" @change="fetchMassSecciones()" :disabled="!massFormData.facultad" name="escuela" required class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 dark:text-slate-50 outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none disabled:opacity-40">
                                            <option value="">Seleccione una escuela</option>
                                            <template x-for="item in escuelas" :key="item.id">
                                                <option :value="item.id" x-text="item.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Sección</label>
                                        <select x-model="massFormData.seccion" :disabled="!massFormData.escuela" name="seccion" required class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 dark:text-slate-50 outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none disabled:opacity-40">
                                            <option value="">Seleccione una sección</option>
                                            <template x-for="item in secciones" :key="item.id">
                                                <option :value="item.id" x-text="item.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="pt-6 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="massModalOpen = false" class="px-6 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-emerald-600 to-teal-700 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-emerald-500/20 active:scale-95 transition-all flex items-center gap-2">
                                <i class="bi bi-cloud-arrow-up-fill text-sm"></i>
                                Importar Usuarios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
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
@endpush
