@extends('template')
@section('title', 'Gestión de Estudiantes por Grupo')
@section('subtitle', 'Asignar y administrar estudiantes en grupos de práctica')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        assignModalOpen: false,
        detailsModalOpen: false,
        deleteModalOpen: false,

        loading: false,
        currentGroup: { id: null, name: '', docente: '', supervisor: '', sa_id: null, escuela: '', semestre: '' },

        estudiantesDisponibles: [],
        selectedIds: [],
        searchAsignar: '',
        
        async fetchDisponibles(saId) {
            this.loading = true;
            this.estudiantesDisponibles = [];
            try {
                const response = await fetch(`/api/asignar_estudiantes/${saId}`);
                this.estudiantesDisponibles = await response.json();
            } finally { this.loading = false; }
        },

        get filteredDisponibles() {
            return this.estudiantesDisponibles.filter(est => 
                `${est.nombres} ${est.apellidos} ${est.codigo}`.toLowerCase().includes(this.searchAsignar.toLowerCase())
            );
        },

        // -- MODALE DETALLES ---
        estudiantesAsignados: [],
        searchDetalle: '',
        
        async fetchAsignados(groupId) {
            this.loading = true;
            this.estudiantesAsignados = [];
            try {
                const response = await fetch(`/api/grupo_estudiantes/${groupId}`);
                this.estudiantesAsignados = await response.json();
            } finally { this.loading = false; }
        },

        get filteredAsignados() {
            return this.estudiantesAsignados.filter(est => 
                `${est.nombres} ${est.apellidos} ${est.codigo}`.toLowerCase().includes(this.searchDetalle.toLowerCase())
            );
        },
        

        toggleAllDisponibles() {
            if (this.selectedIds.length === this.estudiantesDisponibles.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = this.estudiantesDisponibles.map(est => est.id);
            }
        },

        toggleAllAsignados() {
            if (this.selectedIds.length === this.estudiantesAsignados.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = this.estudiantesAsignados.map(est => est.id);
            }
        },

        openAssignModal(groupData) {
            this.currentGroup = groupData;
            this.searchAsignar = '';
            this.selectedIds = [];
            this.assignModalOpen = true;
            this.fetchDisponibles(groupData.sa_id);
        },

        openDetailsModal(groupData) {
            this.currentGroup = groupData;
            this.searchDetalle = '';
            this.selectedIds = [];
            this.detailsModalOpen = true;
            this.fetchAsignados(groupData.id);
        },

        openDeleteModal(text, url) {
            this.deleteText = text;
            this.deleteUrl = url;
            this.deleteModalOpen = true;
        }
    }">

    <x-header-content
        title="Lista de Grupos de Práctica"
        subtitle="Gestionar y validar documentos académicos de grupos de práctica"
        icon="bi-people-fill"
        :enableButton
    />

    @if(Auth::user()->hasAnyRoles([1, 2]))
        <x-data-filter
            route="estudiante_index"
            :facultades="$facultades"
        />
    @endif

    <!-- Table Card -->
    <!-- skeleton loader -->
    @include('components.skeletonLoader-table')
    <div class="overflow-x-auto">
        <table id="tablaGrupos" class="w-full text-left border-collapse table-skeleton-ready rounded-t-2xl overflow-hidden">
            <thead>
                <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">ID</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Escuela</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Seccion</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Nombre de grupo</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Docente</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Supervisor</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Agregar alumno</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gp as $index => $grupo)
                <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->seccion_academica->escuela->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->seccion_academica->semestre->codigo }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->name }}</span>
                    </td>
                    <td class=" px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->docente->persona->nombres }} {{ $grupo->docente->persona->apellidos }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->supervisor->persona->nombres }} {{ $grupo->supervisor->persona->apellidos }}</span>
                    </td>
                    <td>
                        <button type="button" 
                            class="p-2.5 rounded-2xl bg-green-600 hover:bg-teal-700 btn-addge shadow-lg shadow-green-600/20"
                            @click="openAssignModal({
                                id: '{{ $grupo->id }}',
                                name: '{{ addslashes($grupo->name) }}',
                                docente: '{{ addslashes($grupo->docente->persona->nombres . ' ' . $grupo->docente->persona->apellidos) }}',
                                supervisor: '{{ addslashes($grupo->supervisor->persona->nombres . ' ' . $grupo->supervisor->persona->apellidos) }}',
                                sa_id: '{{ $grupo->seccion_academica->id }}'
                            })">
                            <i class="bi bi-person-plus"></i> Asignar alumno
                        </button>
                    </td>
                    <td>
                        <button type="button" 
                            class="p-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/20"
                            @click="openDetailsModal({
                                id: '{{ $grupo->id }}',
                                name: '{{ addslashes($grupo->name) }}',
                                docente: '{{ addslashes($grupo->docente->persona->nombres . ' ' . $grupo->docente->persona->apellidos) }}',
                                supervisor: '{{ addslashes($grupo->supervisor->persona->nombres . ' ' . $grupo->supervisor->persona->apellidos) }}',
                                sa_id: '{{ $grupo->seccion_academica->id }}',
                                escuela: '{{ addslashes($grupo->seccion_academica->escuela->name) }}',
                                semestre: '{{ $grupo->seccion_academica->semestre->codigo }}'
                            })">
                            <i class="bi bi-eye"></i> Ver detalles
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Modal Asignar alumno with Tailwind CSS -->
    <div
        x-show="assignModalOpen" 
        class="fixed inset-0 z-[1060] flex items-center justify-center px-4" 
        x-cloak>
        <x-backdrop-modal name="assignModalOpen"/>
        
        <div x-show="assignModalOpen" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
            class="relative bg-slate-50 dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-4xl overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-blue-950 to-blue-900 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-person-plus-fill text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-base font-black tracking-tight leading-none">Asignar Alumnos al Grupo</h3>
                            <p class="text-blue-200/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-1.5">
                                Grupo: <span class="text-white" x-text="currentGroup.name"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="assignModalOpen = false" class="w-9 h-9 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all text-sm">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('grupos.asignarAlumnos') }}" id="formAssignDynamic" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="grupo_id" id="dynamicGroupIdField" :value="currentGroup.id">
                
                <div class="flex-1 overflow-hidden flex flex-col p-4 space-y-5">
                    
                    {{-- Info Section (Calculated for no scroll) --}}
                    <div class="shrink-0 flex justify-between gap-4">
                        <div class="group w-1/2">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-1.5 ml-1">
                                <i class="bi bi-person-badge mr-1"></i> Docente Titular
                            </label>
                            <div class="relative">
                                <input type="text" 
                                    id="dynamicDocenteTitular"
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border-none text-slate-600 dark:text-slate-300 font-bold text-sm cursor-not-allowed opacity-75 ring-1 ring-slate-100 dark:ring-slate-700" 
                                    :value="currentGroup.docente" 
                                    disabled>
                            </div>
                        </div>
                        <div class="group w-1/2">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-1.5 ml-1">
                                <i class="bi bi-person-badge mr-1"></i> Docente Supervisor
                            </label>
                            <div class="relative">
                                <input type="text" 
                                    id="dynamicDocenteSupervisor"
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border-none text-slate-600 dark:text-slate-300 font-bold text-sm cursor-not-allowed opacity-75 ring-1 ring-slate-100 dark:ring-slate-700" 
                                    :value="currentGroup.supervisor" 
                                    disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Table Container (This will have the internal scroll) -->
                    <div class="flex-1 flex flex-col min-h-0 bg-slate-50 dark:bg-slate-900 rounded-[1.25rem] border-1 border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
                        <!-- Table Header/Search -->
                        <div class="px-5 py-2 bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                <h6 class="text-[10px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-[0.15em] m-0">Alumnos Disponibles</h6>
                            </div>
                            <div class="relative w-full md:w-80">
                                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text"
                                    x-model="searchAsignar"
                                    class="w-full pl-10 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold placeholder:text-slate-400 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none" 
                                    placeholder="Buscar por nombre o código...">
                            </div>
                        </div>

                        <!-- Scrollable Table Body -->
                        <div class="overflow-y-auto flex-1 custom-scrollbar" style="max-height: 380px;">
                            <table class="w-full text-left border-collapse dynamic-tabla-estudiantes">
                                <thead class="sticky top-0 z-20 bg-slate-50 dark:bg-slate-800 shadow-[0_1px_0_0_rgba(0,0,0,0.05)] mb-4">
                                    <tr>
                                        <th class="px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-center w-32 border-none">
                                            <div class="flex items-center justify-center gap-3 bg-slate-50 dark:bg-slate-900 py-1 px-2.5 rounded-lg ring-1 ring-slate-100 dark:ring-slate-800 shadow-sm">
                                                <input type="checkbox" 
                                                    @click="toggleAllDisponibles()"
                                                    :checked="selectedIds.length > 0 && selectedIds.length === filteredStudents.length"
                                                    class="dynamic-check-all w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer transition-all">
                                                <span class="text-[9px]">Todo</span>
                                            </div>
                                        </th>
                                        <th class="px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-none">Nombre Completo</th>
                                        <th class="px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-none">Código</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    <!-- Dynamic content -->
                                    <template x-if="loading">
                                        <tr>
                                            <td colspan="3" class="py-20 text-center">
                                                <i class="bi bi-hourglass-split animate-spin text-3xl text-blue-500"></i>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="!loading && filteredDisponibles.length === 0">
                                        <tr>
                                            <td colspan="3" class="py-20 text-center text-slate-400">No hay estudiantes disponibles.</td>
                                        </tr>
                                    </template>
                                    <template x-for="est in filteredDisponibles" :key="est.id">
                                        <tr class="hover:bg-sky-200 dark:hover:bg-slate-700/50 transition-colors">
                                            <td class="px-4 py-1.5 text-center">
                                                <input type="checkbox" 
                                                    name="estudiantes[]" 
                                                    :value="est.id"
                                                    x-model="selectedIds"
                                                    class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600">
                                            </td>
                                            <td class="px-4 py-1.5 font-bold text-slate-700 dark:text-slate-200 text-xs" x-text="`${est.nombres} ${est.apellidos}`"></td>
                                            <td class="px-4 py-1.5 text-center">
                                                <span class="px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-500 text-blue-600 dark:text-slate-200 text-[9px] font-black" x-text="est.codigo || 'N/A'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0">
                    <div class="flex items-center gap-4 text-slate-400 dark:text-slate-50">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-800 border-1 border-slate-100 dark:border-slate-700 flex items-center justify-center text-[10px] font-black shadow-sm " x-text="selectedIds.length"></div>
                            <span class="text-[9px] font-black uppercase tracking-widest">Seleccionados</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="button" 
                                @click="assignModalOpen = false" 
                                class="flex-1 sm:flex-none px-5 py-2 text-[10px] font-black text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white uppercase tracking-[0.2em] transition-all">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="flex-1 sm:flex-none relative overflow-hidden group px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-blue-500/20 hover:scale-[1.02] active:scale-95 transition-all">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <i class="bi bi-check-circle-fill"></i>
                                Finalizar Asignación
                            </span>
                            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer"></div>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ver detalles with Tailwind CSS -->
    <div
        x-show="detailsModalOpen" 
        class="fixed inset-0 z-[1060] flex items-center justify-center px-4" 
        x-cloak>
        <x-backdrop-modal name="detailsModalOpen" />
            
        <div x-show="detailsModalOpen" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
            class="relative bg-slate-50 dark:bg-slate-800 rounded-[1rem] shadow-2xl w-full max-w-5xl overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-950 to-blue-900 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border border-white/20">
                            <i class="bi bi-info-circle text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-base font-black tracking-tight leading-none">Detalles del Grupo</h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-1.5">
                                Grupo: <span class="text-white" x-text="currentGroup.name"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="detailsModalOpen = false" class="w-9 h-9 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all text-sm">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row h-full max-h-[80vh] overflow-hidden">
                <!-- Info Sidebar -->
                <div class="lg:w-80 bg-slate-100 dark:bg-slate-900/50 p-4 space-y-5 overflow-y-auto border-r border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-3">Información Académica</label>
                        <div class="space-y-4">
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-900 rounded-2xl border-1 border-slate-100 dark:border-slate-700 shadow-sm">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Escuela</p>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-200" x-text="currentGroup.escuela"></p>
                            </div>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-900 rounded-2xl border-1 border-slate-100 dark:border-slate-700 shadow-sm">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Semestre</p>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-200" x-text="currentGroup.semestre"></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-3">Personal a Cargo</label>
                        <div class="space-y-4">
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-900 rounded-2xl border-1 border-slate-100 dark:border-slate-700 shadow-sm flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                                    <i class="bi bi-person-workspace text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Docente</p>
                                    <p class="text-[10px] font-bold text-slate-700 dark:text-slate-200 truncate" x-text="currentGroup.docente"></p>
                                </div>
                            </div>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-900 rounded-2xl border-1 border-slate-100 dark:border-slate-700 shadow-sm flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center text-teal-600">
                                    <i class="bi bi-person-check text-base"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Supervisor</p>
                                    <p class="text-[10px] font-bold text-slate-700 dark:text-slate-200 truncate" x-text="currentGroup.supervisor"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content (Assigned Students Table) -->
                <div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-900">
                    <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span x-text="filteredAsignados.length" 
                                class="px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-wider">
                            </span>
                            <h4 class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest m-0">Estudiantes Asignados</h4>
                        </div>
                        <div class="relative w-64">
                            <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text"
                                x-model="searchDetalle"
                                class="view-buscar-estudiante w-full pl-9 pr-4 py-1.5 bg-slate-100 dark:bg-slate-800/50 border-none rounded-xl text-[11px] font-bold focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                                placeholder="Filtrar por nombre...">
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar">
                        <table class="w-full text-left border-collapse view-tabla-estudiantes">
                            <thead class="bg-gradient-to-r from-primary-dark to-primary-light text-white">
                                <tr>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-[0.2em] border-none w-16">N°</th>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-[0.2em] border-none">Nombre Completo</th>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-[0.2em] border-none">Código</th>
                                    <th class="px-5 py-3 text-[10px] font-black uppercase tracking-[0.2em] border-none text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <template x-if="loading">
                                    <tr><td colspan="4" class="py-20 text-center">Cargando...</td></tr>
                                </template>

                                <template x-for="(reg, index) in filteredAsignados" :key="reg.id">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group/row">
                                        <td class="px-5 py-2.5">
                                            <span x-text="index + 1"
                                                class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-black text-slate-500 dark:text-slate-400 group-hover/row:bg-blue-600 group-hover/row:text-white transition-all shadow-sm">
                                            </span>
                                        </td>
                                        <td class="px-5 py-2.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-800/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-black text-[9px] uppercase" x-text="`${reg.nombres.charAt(0)}${reg.apellidos.charAt(0)}`"></div>
                                                <span x-text="`${reg.nombres} ${reg.apellidos}`" 
                                                    class="text-xs font-bold text-slate-700 dark:text-slate-200 tracking-tight">
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-2.5">
                                            <span x-text="reg.codigo"
                                                class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[9px] font-black uppercase tracking-wider border border-slate-200"></span>
                                        </td>
                                        <td class="px-5 py-2.5 text-right">
                                            <button type="button" 
                                                class="w-8 h-8 rounded-xl bg-red-50 dark:bg-red-900/30 hover:bg-red-600 text-red-600 hover:text-white transition-all shadow-sm" 
                                                @click="openDeleteModal(`${reg.nombres}`, `/grupos/eliminar-asignado/${reg.id}`)">
                                                <i class="bi bi-trash-fill text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-5 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <button @click="detailsModalOpen = false" class="px-6 py-3 bg-slate-900 dark:bg-slate-700 text-white dark:text-slate-900 text-[9px] font-black uppercase tracking-[0.2em] rounded-xl hover:scale-105 active:scale-95 transition-all shadow-xl shadow-slate-900/20 dark:shadow-white/5">
                            Cerrar Vista
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ELIMINAR with Tailwind CSS -->
    <div
        x-show="deleteModalOpen" 
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4" 
        x-cloak>
        <x-backdrop-modal name="deleteModalOpen" />

        <div x-show="deleteModalOpen" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-slate-50 dark:bg-slate-900 rounded-[1.5rem] shadow-2xl w-full max-w-md overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            
            <div class="p-8">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-600 mb-6 group">
                        <i class="bi bi-exclamation-triangle text-4xl group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight mb-2">Confirmar eliminación</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium leading-relaxed px-4" x-text="deleteText"></p>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-4 italic">Esta acción no se puede deshacer</p>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-800/50 p-6 flex flex-col sm:flex-row gap-3">
                <button @click="deleteModalOpen = false" class="flex-1 px-6 py-3.5 rounded-2xl bg-slate-100 dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest hover:text-slate-700 dark:hover:text-white transition-all border-1 border-slate-200 dark:border-slate-700 outline-none">
                    Cancelar
                </button>
                <form :action="deleteUrl" method="POST" class="flex-1 m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-6 py-3.5 rounded-2xl bg-red-600 text-white text-xs font-black uppercase tracking-widest hover:bg-red-700 shadow-lg shadow-red-600/20 transition-all border-1 border-red-600 flex items-center justify-center gap-2">
                        <i class="bi bi-trash-fill"></i>
                        Confirmar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Inicialización de DataTable (Tabla Principal)
    $('#tablaGrupos').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar escuela...",
            "paginate": { "first": "Primero", "last": "Último", "next": "Sig.", "previous": "Ant." },
        },
        pageLength: 10,
        responsive: true,
        dom: '<"flex flex-col md:flex-row md:items-center justify-between gap-4 py-8 px-2"lf>rt<"flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4 pb-2 px-2"ip>',
        initComplete: function() {
            $('#skeletonLoader').addClass('hidden');
            $('#tablaGrupos').addClass('dt-ready');
        }
    });
});
</script>
@endpush