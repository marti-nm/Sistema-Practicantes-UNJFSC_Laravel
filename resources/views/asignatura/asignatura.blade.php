@extends('template')
@section('title', 'Gestión de Grupos de Asignatura')
@section('subtitle', 'Administrar grupos de práctica por asignaturas y docentes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        newModalOpen: false,
        editModalOpen: false,
        deleteModalOpen: false,
        facultadId: '',
        escuelaId: '',
        seccionId: '',
        dtitularId: '',
        dsupervisorId: '',
        loading: false,
        escuelas: [],
        secciones: [],
        dtitulares: [],
        dsupervisores: [],
        dgrupo: null,

        async fetchEscuelas() {
            if(!this.facultadId) {
                this.escuelas = [];
                this.escuelaId = '';
                return;
            }
            this.loading = true;
            try {
                const r = await fetch(`/api/escuelas/${this.facultadId}`);
                const data = await r.json();
                this.escuelas = data;
                this.escuelaId = '';
                this.secciones = [];
            } finally { this.loading = false; }
        },

        async fetchSecciones() {
            if(!this.escuelaId) {
                this.secciones = [];
                this.seccionId = '';
                return;
            }
            this.loading = true;
            try {
                const id_sem = {{ session('semestre_actual_id') ?? 'null' }};
                const r = await fetch(`/api/secciones/${this.escuelaId}/${id_sem}`);
                const data = await r.json();
                this.secciones = data;
                this.seccionId = '';
            } finally { this.loading = false; }
        },

        async fetchDTitulares() {
            if(!this.seccionId) {
                this.dtitulares = [];
                this.dtitularId = '';
                return;
            }
            try {
                const r = await fetch(`/api/docentes-titulares/${this.seccionId}`);
                this.dtitulares = await r.json();
                @if(Auth::user()->getRolId() == 3)
                    this.dtitularId = '{{ $ap->id }}';
                @else
                    this.dtitularId = '';
                @endif
            } catch (e) { console.error(e); }
        },

        async fetchDSupervisores() {
            if(!this.seccionId) {
                this.dsupervisores = [];
                this.dsupervisorId = '';
                return;
            }
            try {
                const r = await fetch(`/api/docentes-supervisores/${this.seccionId}`);
                this.dsupervisores = await r.json();
                this.dsupervisorId = '';
                console.log(this.dsupervisores);
            } catch (e) { console.error(e); }
        },

        async fetchDocentes() {
            this.loading = true;
            try {
                await Promise.all([
                    this.fetchDTitulares(),
                    this.fetchDSupervisores()
                ]);
            } finally { this.loading = false; }
        },

        async fetchGrupo(id) {
            this.loading = true;
            try {
                const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
                await sleep(1000);
                const r = await fetch(`/api/grupo/${id}`);
                this.dgrupo = await r.json();
            } finally { this.loading = false; }
        },

        openNewModal(){
            this.newModalOpen = true;
            /*@if(Auth::user()->getRolId() == 3)
                this.facultadId = '{{ $ap->seccion_academica->id_facultad }}';
                this.escuelaId = '{{ $ap->seccion_academica->id_escuela }}';
                this.seccionId = '{{ $ap->id_sa }}';
                this.fetchDocentes();
            @else
                this.facultadId = '';
                this.escuelaId = '';
                this.seccionId = '';
                this.dtitulares = [];
                this.dsupervisores = [];
            @endif*/
        },

        openEditModal(id, id_sa){
            this.editModalOpen = true;
            this.fetchGrupo(id);
            this.seccionId = id_sa;
            this.fetchDSupervisores();
        },

        requireDeleteData: { id: null, grupo: null },

        openDeleteModal(data){
            this.deleteModalOpen = true;
            this.requireDeleteData = data;
        },
    }">

    <x-header-content
        title="Lista de Grupos de Práctica"
        subtitle="Gestión académica oficial de grupos de práctica"
        icon="bi-people-fill"
        enableButton="true"
        :typeButton=2
        msj="Registrar Grupo"
        icon_msj="bi-mortarboard-fill"
        route="grupo.practica"
        function="openNewModal()"
    />
    @if(Auth::user()->hasAnyRoles([1, 2]))
    <x-data-filter
        route="grupo.practica"
        :facultades="$facultades"
    />
    @endif

    @include('components.skeletonLoader-table')
    <div class="overflow-x-auto">
        <table id="tablaGruposPractica" class="w-full text-left border-collapse table-skeleton-ready rounded-t-2xl overflow-hidden">
            <thead>
                <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">ID</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Facultad</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Escuela</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Sección</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Nombre de grupo</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Docente</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Supervisor</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grupos_practica as $index => $grupo)
                <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->seccion_academica->facultad->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->seccion_academica->escuela->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->seccion_academica->seccion }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-teal-700 dark:text-orange-500 tracking-tight">{{ $grupo->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->docente->persona->nombres }} {{ $grupo->docente->persona->apellidos }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $grupo->supervisor->persona->nombres }} {{ $grupo->supervisor->persona->apellidos }}</span>
                    </td>

                    <td class="px-6 py-4">
                        <!-- Editar -->
                        <div class="flex items-center justify-center gap-2">
                            <button @click="openEditModal({{ $grupo->id }}, {{ $grupo->id_sa }})"
                                class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40 border-1 border-amber-100 dark:border-amber-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button @click="openDeleteModal({ id: {{ $grupo->id }}, grupo: '{{ $grupo->name }}' })"
                                class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 border-1 border-rose-100 dark:border-rose-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="newModalOpen"
        class="fixed inset-0 z-[1050] flex items-center justify-center px-4"
        x-cloak>
        <x-backdrop-modal name="newModalOpen" />
        <div x-show="newModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-clipboard-data-fill text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none">Registrar Grupo de Práctica</h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Grupo de Práctica</p>
                        </div>
                    </div>
                    <button @click="newModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <form action="{{ route('grupos.store') }}" method="POST" class="flex-1 overflow-y-auto">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Facultad</label>
                            @if (Auth::user()->getRolId() == 3)
                                <select name="facultad" class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800/50 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed" readonly>
                                    <option value="{{ $ap->seccion_academica->id_facultad }}" selected>{{ $ap->seccion_academica->facultad->name ?? 'N/A' }}</option>
                                </select>
                            @else
                            <select name="facultad" x-model="facultadId" @change="fetchEscuelas()"
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200 text-sm" required>
                                <option value="">Seleccione una facultad</option>
                                @foreach ($facultades as $facultad)
                                    <option value="{{ $facultad->id }}">{{ $facultad->name }}</option>
                                @endforeach
                            </select>
                            @endif
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Escuela</label>
                            @if (Auth::user()->getRolId() == 3)
                                <select name="escuela" class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800/50 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed" readonly>
                                    <option value="{{ $ap->seccion_academica->id_escuela }}" selected>{{ $ap->seccion_academica->escuela->name ?? 'N/A' }}</option>
                                </select>
                            @else
                            <select name="escuela" x-model="escuelaId" @change="fetchSecciones()" :disabled="!facultadId || loading"
                                :class="!facultadId ? 'bg-slate-100 dark:bg-slate-800/40 opacity-60 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800/50'"
                                class="w-full px-4 py-3 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200 text-sm" required>
                                <option value="">Seleccione una escuela</option>
                                <template x-for="escuela in escuelas" :key="escuela.id">
                                    <option :value="escuela.id" x-text="escuela.name"></option>
                                </template>
                            </select>
                            @endif
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Sección</label>
                            @if (Auth::user()->getRolId() == 3)
                                <select name="seccion" x-model="seccionId" @change="fetchDSupervisores()" class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800/50 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed" readonly>
                                    <option value="{{ $ap->id_sa }}" selected>{{ $ap->seccion_academica->seccion ?? 'N/A' }}</option>
                                </select>
                            @else
                                <select name="seccion" x-model="seccionId" @change="fetchDocentes()" :disabled="!escuelaId || loading"
                                    :class="!escuelaId ? 'bg-slate-100 dark:bg-slate-800/40 opacity-60 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800/50'"
                                    class="w-full px-4 py-3 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200 text-sm" required>
                                    <option value="">Seleccione una sección</option>
                                    <template x-for="seccion in secciones" :key="seccion.id">
                                        <option :value="seccion.id" x-text="seccion.name"></option>
                                    </template>
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Docente Titular</label>
                            @if (Auth::user()->getRolId() == 3)
                                <select name="dtitular" x-model="dtitularId"
                                    class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800/50 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none font-bold text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed" readonly>
                                    <option value="{{ $ap->id }}" selected>{{ $ap->persona->nombres }} {{ $ap->persona->apellidos }}</option>
                                </select>
                            @else
                            <select name="dtitular" x-model="dtitularId" :disabled="!seccionId || loading"
                                :class="!seccionId ? 'bg-slate-100 dark:bg-slate-800/40 opacity-60 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800/50'"
                                class="w-full px-4 py-3 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200 text-sm" required>
                                <option value="">Seleccione un docente titular</option>
                                <template x-for="docente in dtitulares" :key="docente.id">
                                    <option :value="docente.id" x-text="docente.apellidos + ', ' + docente.nombres"></option>
                                </template>
                            </select>
                            @endif
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Docente Supervisor</label>
                            <select name="dsupervisor" x-model="dsupervisorId" :disabled="!seccionId || loading"
                                :class="!seccionId ? 'bg-slate-100 dark:bg-slate-800/40 opacity-60 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800/50'"
                                class="w-full px-4 py-3 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200 text-sm" required>
                                <option value="">Seleccione un docente supervisor</option>
                                <template x-for="docente in dsupervisores" :key="docente.id">
                                    <option :value="docente.id" x-text="docente.apellidos + ', ' + docente.nombres"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <!-- Nombre del Grupo -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Nombre del Grupo</label>
                        <div class="relative group">
                            <i class="bi bi-collection absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-600 transition-colors group-focus-within:text-blue-500"></i>
                            <input type="text" name="nombre_grupo" placeholder="Ej: Grupo A - Prácticas 2024"
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200" required>
                        </div>
                    </div>
                    <div class="flex justify-between gap-3 mt-10 shrink-0">
                        <button type="button" @click="newModalOpen = false" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-3 bg-[#111c44] text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-800 shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                            Guardar Grupo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="editModalOpen"
        class="fixed inset-0 z-[1060] flex items-center justify-center px-4"
        x-cloak>
        <x-backdrop-modal name="editModalOpen" />
        <div x-show="editModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-clipboard-data-fill text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none">Editar Grupo de Práctica</h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Grupo de Práctica</p>
                        </div>
                    </div>
                    <button @click="editModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="space-y-6 p-4">
                <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border-1 border-blue-100 dark:border-blue-800/50">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-info-circle-fill text-blue-500 mt-0.5"></i>
                        <p class="text-[11px] text-blue-800 dark:text-blue-300 font-medium leading-relaxed">
                            Solo se permite editar el <strong>nombre del grupo</strong> y el <strong>supervisor</strong>. Para cambios estructurales (Escuela, Sección), elimine y cree un nuevo grupo.
                        </p>
                    </div>
                </div>
                <template x-if="loading">
                    <div class="py-12 flex flex-col items-center justify-center gap-4 text-blue-500">
                        <div class="w-12 h-12 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] animate-pulse">Consultando Registros...</p>
                    </div>
                </template>
                <template x-if="!loading && dgrupo">
                    <form method="POST" :action="'/grupos/' + dgrupo.id">
                        @csrf
                        <div class="space-y-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5 opacity-60">
                                    <label class="text-[10px] font-black uppercase text-slate-400">Escuela</label>
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-3 py-2 rounded-lg" x-text="dgrupo.seccion_academica.escuela.name"></div>
                                </div>
                                <div class="space-y-1.5 opacity-60">
                                    <label class="text-[10px] font-black uppercase text-slate-400">Sección</label>
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-3 py-2 rounded-lg" x-text="dgrupo.seccion_academica.seccion"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5 opacity-60">
                                    <label class="text-[10px] font-black uppercase text-slate-400">D. Titular *(Actual)</label>
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-3 py-2 rounded-lg" x-text="dgrupo.docente.persona.apellidos + ', ' + dgrupo.docente.persona.nombres"></div>
                                </div>
                                <div class="space-y-1.5 opacity-60">
                                    <label class="text-[10px] font-black uppercase text-slate-400">D. Supervisor *(Actual)</label>
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-3 py-2 rounded-lg" x-text="dgrupo.supervisor.persona.apellidos + ', ' + dgrupo.supervisor.persona.nombres"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Nombre del Grupo</label>
                                <input type="text" name="nombre_grupo" :value="dgrupo.name" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-slate-700 dark:text-slate-200 text-sm" required>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Supervisor</label>
                                <select name="dsupervisor" :value="dgrupo.supervisor.id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-1 border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-slate-700 dark:text-slate-200 text-sm">
                                    <option value="">Seleccione un docente supervisor</option>
                                    <template x-for="docente in dsupervisores" :key="docente.id">
                                        <option :value="docente.id" x-text="docente.apellidos + ', ' + docente.nombres"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex justify-between space-y-2 gap-3 shrink-0">
                                <button type="button" @click="editModalOpen = false" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-6 py-3 bg-amber-500 text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-amber-800 shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <div x-show="deleteModalOpen"
        class="fixed inset-0 z-[1060] flex items-center justify-center px-4"
        x-cloak>
        <x-backdrop-modal name="deleteModalOpen" />
        <div x-show="deleteModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            <div class="p-10 text-center">
                <div class="w-20 h-20 bg-rose-50 dark:bg-rose-900/20 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500 dark:text-rose-400 border border-rose-100 dark:border-rose-800 shadow-inner">
                    <i class="bi bi-trash-fill text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2 tracking-tight">¿Eliminar este grupo?</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed">
                    Estás por eliminar el grupo <strong class="text-rose-600 dark:text-rose-400 font-bold" x-text="requireDeleteData.grupo"></strong>. Esta acción no se puede deshacer.
                </p>
                <div class="flex gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        Cancelar
                    </button>
                    <form :action="'/grupos_delete/' + requireDeleteData.id" method="POST" class="flex-[1.5]">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 bg-rose-500 text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-rose-600 shadow-lg shadow-rose-500/20 transition-all active:scale-95">
                            Sí, eliminar
                        </button>
                    </form>
                </div>
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
    $('#tablaGruposPractica').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar grupo...",
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
            $('#tablaGruposPractica').addClass('dt-ready');
        }
    });
});
</script>
@endpush
