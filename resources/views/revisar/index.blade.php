@extends('template')
@section('title', 'Revisión de Evaluaciones')

@section('content')
<div class="h-[calc(100vh-120px)] flex flex-col gap-4 overflow-hidden"
     x-data='{
        viewMode: "list",
        selectedItem: false,
        requireData: { id:null, people:null, escuela:null, seccion:null, semestre:null, archivos:{}, avatar:"", id_ap:null, id_modulo:null},
        ldata: null,
        hdata: null,
        loading: false,
        selectedOption: true,
        urlFile: null,
        currentPage: 1,
        itemsPerPage: 10,
        searchQuery: "",
        showFilter: false,
        activeType: "init",

        // Review Action States
        docViewMode: "info",
        reviewState: null, // "approve" or "observe"
        correccionTipo: "2",
        comentario: "",

        facultadId: "{{ $facultad_id ?? "" }}",
        escuelaId: "{{ $escuela_id ?? "" }}",
        seccionId: "{{ $seccion_id ?? "" }}",
        escuelas: [],
        secciones: [],
        grupoId: "{{ $selected_grupo_id ?? "" }}",
        grupos: [],

        // La lista de usuarios viene del controlador en la variable $rows
        userList: {{ json_encode($rows) }},

        async fetchEscuelas(keep = false) {
            if(!this.facultadId) {
                this.escuelas = [];
                if(!keep) this.escuelaId = "";
                return;
            }
            this.loading = true;
            try {
                const r = await fetch(`/api/escuelas/${this.facultadId}`);
                const data = await r.json();
                this.escuelas = data;
                if(!keep) {
                    this.escuelaId = "";
                    this.secciones = [];
                }
            } finally { this.loading = false; }
        },

        async fetchSecciones(keep = false) {
            if(!this.escuelaId) {
                this.secciones = [];
                if(!keep) this.seccionId = "";
                return;
            }
            this.loading = true;
            try {
                const id_sem = {{ session("semestre_actual_id") ?? "null" }};
                const r = await fetch(`/api/secciones/${this.escuelaId}/${id_sem}`);
                const data = await r.json();
                this.secciones = data;
                if(!keep) this.seccionId = "";
            } finally { this.loading = false; }
        },

        async fetchGroups(keep = false) {
            this.loading = true;
            try {
                const fac = this.facultadId || "0";
                const esc = this.escuelaId || "0";
                const sec = this.seccionId || "0";
                const r = await fetch(`/api/grupos_practicas/${fac}/${esc}/${sec}`, { 
                    headers: { "Accept": "application/json" } 
                });
                const data = await r.json();
                this.grupos = data;
                if(!keep) this.grupoId = "";
            } finally { this.loading = false; }
        },

        selectModule(moduleId, locked) {
            if (locked) {
                Swal.fire({
                    icon: "info",
                    title: "Módulo bloqueado",
                    text: "Este módulo aún no está habilitado para el grupo seleccionado.",
                    toast: true,
                    position: "top-end",
                    timer: 2500,
                    showConfirmButton: false,
                });
                return;
            }
            document.getElementById("selected_modulo").value = moduleId;
            document.getElementById("form-modulo").submit();
        },

        storageKey: "val_nav_revisar_{{ auth()->id() }}",

        async init() {
            this.restoreState();
            if (this.facultadId) await this.fetchEscuelas(true);
            if (this.escuelaId) await this.fetchSecciones(true);
            if (this.facultadId || this.grupoId) await this.fetchGroups(true);
        },

        saveState() {
            if (this.selectedItem && this.requireData.id) {
                localStorage.setItem(this.storageKey, JSON.stringify({
                    id: this.requireData.id,
                    type: this.activeType,
                    docViewMode: this.docViewMode
                }));
            }
        },

        restoreState() {
            const saved = localStorage.getItem(this.storageKey);
            if (!saved) return;
            const keys = JSON.parse(saved);
            const freshUser = this.userList.find(u => u.id === keys.id);

            if (freshUser) {
                this.requireData = { ...freshUser };
                this.selectedItem = true;
                this.viewMode = "evaluate";
                this.activeType = keys.type;
                this.docViewMode = keys.docViewMode || "info";

                if (this.activeType !== "init") {
                    this.fetchGetFiles(this.requireData.id, this.activeType);
                }
            } else {
                this.clearState();
            }
        },

        clearState() {
            localStorage.removeItem(this.storageKey);
            this.selectedItem = false;
            this.viewMode = "list";
            this.requireData = { id:null, people:null, escuela:null, seccion:null, semestre:null, archivos:{}, avatar:"", id_ap:null, id_modulo:null};
            this.activeType = "init";
        },

        async fetchGetFiles(id, type) {
            if (!id || !type || type === "init") return;
            this.loading = true;
            this.ldata = null;
            this.hdata = null;
            this.reviewState = null;

            try {
                let apiType = type === "anexo7" ? "anexo_7" : "anexo_8";
                const r = await fetch(`/api/evaluacion_practica/${this.requireData.id_ap}/${this.requireData.id_modulo}/${apiType}`);
                const result = await r.json();
                const data = result.length > 0 ? result[0] : null;

                if (data) {
                    this.hdata = data;
                    if (data.evaluacion_archivo && data.evaluacion_archivo.length > 0) {
                        this.ldata = data.evaluacion_archivo[0];
                        if (this.ldata.archivos && this.ldata.archivos.length > 0) {
                            this.urlFile = this.ldata.archivos[0].ruta;
                        }
                    }
                }
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        openItem(data) {
            this.ldata = null;
            this.hdata = null;
            this.selectedItem = true;
            this.requireData = data;
            this.viewMode = "evaluate";
            this.activeType = "init";
            this.docViewMode = "info";
            this.reviewState = null;
            this.saveState();
        },

        openContentFiles(type) {
            this.activeType = type;
            this.docViewMode = "info";
            this.reviewState = null;
            this.fetchGetFiles(this.requireData.id, type);
        },

        get filteredUsers() {
            if (!this.searchQuery) return this.userList;
            const query = this.searchQuery.toLowerCase();
            return this.userList.filter(user =>
                user.people.toLowerCase().includes(query) ||
                user.escuela.toLowerCase().includes(query)
            );
        },

        get pagedUsers() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.filteredUsers.slice(start, start + this.itemsPerPage);
        },

        get totalPages() {
            return Math.ceil(this.filteredUsers.length / this.itemsPerPage) || 1;
        },

        async submitReview() {
            if (!this.reviewState) {
                Swal.fire("Acción requerida", "Debe elegir Aprobar u Observar.", "warning");
                return;
            }
            if (this.reviewState === "observe" && !this.comentario) {
                Swal.fire("Comentario requerido", "Debe indicar el motivo de la observación.", "warning");
                return;
            }

            this.loading = true;
            const formData = new FormData();
            formData.append("_token", "{{ csrf_token() }}");
            formData.append("evaluacion", this.ldata.id);
            formData.append("archivo", this.ldata.archivos[0].id);
            formData.append("estado", this.reviewState === "approve" ? "Aprobado" : "Corregir");
            if (this.reviewState === "observe") {
                formData.append("correccionTipo", this.correccionTipo);
                formData.append("comentario", this.comentario);
            }

            try {
                const r = await fetch("{{ route("actualizar.anexo") }}", {
                    method: "POST",
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                });

                if (r.ok) {
                    Swal.fire({
                        title: "¡Revisión Completada!",
                        text: "El estado ha sido actualizado correctamente.",
                        icon: "success",
                        confirmButtonColor: "#2563eb"
                    }).then(() => {
                        this.fetchGetFiles(this.requireData.id, this.activeType);
                        let userIdx = this.userList.findIndex(u => u.id === this.requireData.id);
                        if (userIdx !== -1) {
                            if (!this.userList[userIdx].archivos[this.activeType]) {
                                this.userList[userIdx].archivos[this.activeType] = { estado: "" };
                            }
                            this.userList[userIdx].archivos[this.activeType].estado = this.reviewState === "approve" ? "Aprobado" : "Corregir";
                        }
                    });
                } else {
                    const err = await r.json();
                    Swal.fire("Error", err.message || "No se pudo procesar la revisión", "error");
                }
            } catch (e) {
                Swal.fire("Error", "Error de conexión al servidor", "error");
            } finally {
                this.loading = false;
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        }
    }' x-init="init()">

<div class="px-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl p-3 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <div class="flex-1 flex items-center gap-3 ml-2">
                <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg">
                    <i class="bi bi-shield-check text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 leading-tight">Revisión de Prácticas</h2>
                    <p class="text-[11px] text-slate-400 uppercase tracking-tighter">Administrador: {{ auth()->user()->persona->nombres }}</p>
                </div>
            </div>

            @if(Auth::user()->hasAnyRoles([1, 2]))
            <div class="flex items-center gap-3 min-w-[300px]">
                <label class="hidden md:block text-xs font-semibold text-slate-500 whitespace-nowrap">Filtrar:</label>
                <div class="flex gap-2 relative w-full">
                    <select name="facultad" x-model="facultadId" @change="fetchEscuelas()"
                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900 bg-slate-50/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all cursor-pointer text-slate-600">
                        <option value="">-- Facultad --</option>
                        @foreach($facultades as $fac)
                            <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                        @endforeach
                    </select>

                    <select name="escuela" x-model="escuelaId" @change="fetchSecciones()" :disabled="!facultadId || loading"
                        :class="!facultadId ? 'bg-slate-100 dark:bg-slate-800/40 opacity-60 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800/50'"
                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900 bg-slate-50/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all cursor-pointer text-slate-600">
                        <option value="">-- Escuela --</option>
                        <template x-for="escuela in escuelas" :key="escuela.id">
                            <option :value="escuela.id" x-text="escuela.name" :selected="escuela.id == escuelaId"></option>
                        </template>
                    </select>

                    <select name="seccion" x-model="seccionId" :disabled="!escuelaId || loading"
                        :class="!escuelaId ? 'bg-slate-100 dark:bg-slate-800/40 opacity-60 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800/50'"
                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900 bg-slate-50/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all cursor-pointer text-slate-600">
                        <option value="">-- Sección --</option>
                        <template x-for="seccion in secciones" :key="seccion.id">
                            <option :value="seccion.id" x-text="seccion.name" :selected="seccion.id == seccionId"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <button type="button" @click="fetchGroups()"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-500 rounded-lg hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                        Buscar
                    </button>
                </div>
            </div>
            @endif

            <div class="flex items-center gap-3 min-w-[300px]">
                <label for="grupo" class="hidden md:block text-xs font-semibold text-slate-500 whitespace-nowrap">Grupo:</label>
                <div class="relative w-full">
                    <form method="GET" action="{{ route('seguimiento.revisar') }}" class="relative z-10">
                        <select id="grupo" name="grupo" x-model="grupoId" onchange="this.form.submit()"
                            class="appearance-none w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 py-2 pl-4 pr-10 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all cursor-pointer font-medium">
                            <option value="">-- Seleccione un grupo --</option>
                            <template x-for="grupo in grupos" :key="grupo.id">
                                <option :value="grupo.id" x-text="grupo.name" :selected="grupo.id == grupoId"></option>
                            </template>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="px-4">
    <form id="form-modulo" method="GET" action="{{ route('seguimiento.revisar') }}" class="w-full">
        <input type="hidden" name="grupo" value="{{ $selected_grupo_id }}">
        <input type="hidden" name="modulo" id="selected_modulo" value="{{ $id_modulo ?? 1 }}">
        
        <div class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="shrink-0">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2 md:mb-0 block md:inline-block">
                    <i class="bi bi-layers-fill mr-1 text-indigo-500"></i> Módulos:
                </label>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full">
                @php
                $modules = [1 => 'Módulo I', 2 => 'Módulo II', 3 => 'Módulo III', 4 => 'Módulo IV'];
                $currentModulo = isset($id_modulo_now) ? (int)$id_modulo_now : null;
                $selectedModuloRequest = (int) ($id_modulo ?? 1);
                @endphp
                @foreach($modules as $m => $label)
                @php
                $isActive = ($selectedModuloRequest === $m);
                $locked = is_null($selected_grupo_id) || is_null($currentModulo) || ($m > $currentModulo);
                @endphp
                <div class="relative">
                    <div
                        class="module-selector-cell group relative w-full p-3 rounded-xl border transition-all duration-200 flex items-center justify-center gap-3 cursor-pointer
                        {{ $isActive
                            ? 'bg-gradient-to-br from-blue-600 to-indigo-600 border-transparent text-white shadow-lg shadow-blue-500/30 transform scale-[1.02]'
                            : ($locked
                                ? 'bg-slate-50 dark:bg-slate-800/50 border-slate-100 dark:border-slate-800 text-slate-300 dark:text-slate-600 cursor-not-allowed'
                                : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-blue-300 hover:shadow-md')
                        }}"
                        role="button"
                        tabindex="{{ $locked ? '-1' : '0' }}"
                        aria-disabled="{{ $locked ? 'true' : 'false' }}"
                        @click="selectModule({{ $m }}, {{ $locked ? 'true' : 'false' }})">

                        <div class="flex flex-col items-center">
                            <span class="text-[10px] font-black uppercase tracking-widest opacity-70">Módulo</span>
                            <span class="text-xl font-black">{{ $m }}</span>
                        </div>

                        @if($locked)
                            <div class="absolute top-2 right-2">
                                <i class="bi bi-lock-fill text-xs opacity-50"></i>
                            </div>
                        @elseif($isActive)
                            <div class="absolute top-2 right-2">
                                <i class="bi bi-check-circle-fill text-xs text-white/50"></i>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </form>
</div>

<x-body-container>
    <x-slot:lista>
        <div class="p-4 space-y-3 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Buscar usuario..." class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50 rounded-lg focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
            </div>
            <div class="flex justify-between items-center px-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest" x-text="'Registros (' + userList.length + ')'"></span>
            </div>
        </div>

        <!-- Listado -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-800 custom-scrollbar">
            <template x-for="user in pagedUsers" :key="user.id">
                <button @click="openItem(user)" class="w-full p-4 flex items-start gap-3 hover:bg-indigo-50/30 transition-all text-left relative"
                    :class="selectedItem && requireData.id === user.id ? 'bg-indigo-50/50 dark:bg-slate-800' : ''">
                    <div x-show="selectedItem && requireData.id === user.id" class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600"></div>

                    <!-- Avatar / # -->
                    <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center font-bold text-xs shadow-sm transition-colors"
                        :class="selectedItem && requireData.id === user.id ? 'bg-indigo-600 text-white' : 'bg-white border dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 group-hover:border-indigo-200 group-hover:text-indigo-600'">
                        <span x-text="user.avatar"></span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-400 truncate" x-text="user.people"></p>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium" x-text="user.escuela"></p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="text-[9px] bg-slate-100 dark:bg-slate-800 text-slate-500 px-1 rounded" x-text="'Semestre '+user.semestre"></span>
                            <span class="text-[9px] bg-slate-100 text-slate-500 px-1 rounded" x-text="'Sección '+user.seccion"></span>
                        </div>

                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach($config['columns'] as $col)
                                <span class="w-2 h-2 rounded-full"
                                      :class="{
                                          'bg-green-500': user.archivos['{{ $col['key'] }}'] && user.archivos['{{ $col['key'] }}'].estado === 'Aprobado',
                                          'bg-red-500': user.archivos['{{ $col['key'] }}'] && user.archivos['{{ $col['key'] }}'].estado === 'Corregir',
                                          'bg-yellow-500': user.archivos['{{ $col['key'] }}'] && user.archivos['{{ $col['key'] }}'].estado === 'Enviado',
                                          'bg-slate-200': !user.archivos['{{ $col['key'] }}'] || user.archivos['{{ $col['key'] }}'].estado === 'Falta'
                                      }"
                                      title="{{ $col['label'] }}"></span>
                            @endforeach
                        </div>
                    </div>
                </button>
            </template>
        </div>

        <!-- Paginación -->
        <div class="p-3 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
            <button @click="prevPage()"
                    :disabled="currentPage === 1"
                    class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                Ant.
            </button>
            <div class="flex items-center gap-2">
                <span class="text-indigo-600 dark:text-indigo-400" x-text="currentPage"></span>
                <span class="text-slate-300">/</span>
                <span x-text="totalPages"></span>
            </div>
            <button @click="nextPage()"
                    :disabled="currentPage === totalPages"
                    class="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                Sig.
            </button>
        </div>
    </x-slot:lista>

    <x-slot:hContent>
        <div class="flex items-center justify-between w-full">
            <div>
                <h2 class="text-lg font-black text-slate-800 dark:text-slate-100" x-text="requireData.people"></h2>
                <div class="flex gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                    <span x-text="requireData.escuela"></span>
                    <span>|</span>
                    <span x-text="'SECC. ' + requireData.seccion"></span>
                </div>
            </div>
            <button @click="clearState()" class="hidden xl:block p-2 hover:bg-red-50 hover:text-red-500 rounded-xl transition-all text-slate-400 group relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-2 py-1 bg-slate-800 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-all">Cerrar</span>
            </button>
        </div>
        <!-- Tabs Modernas -->
        <div class="flex gap-1 mt-auto overflow-x-auto overflow-y-hidden no-scrollbar w-full items-end pb-[1px]">
            <button @click="activeType = 'init'; ldata = null; hdata = null"
                    class="group relative px-6 py-2.5 text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2 rounded-t-xl"
                    :class="activeType === 'init' ? 'bg-gray-500 text-white shadow-[0_-4px_12px_rgba(245,158,11,0.25)] translate-y-0' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 translate-y-1 hover:translate-y-0'">
                <i class="bi-info-circle text-sm" :class="activeType === 'init' ? 'text-gray-200' : 'text-slate-300'"></i>
                Pendiente
            </button>

            @foreach($config['columns'] as $col)
            <button @click="openContentFiles('{{ $col['key'] }}')"
                    class="group relative px-6 py-2.5 text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2 rounded-t-xl"
                    :class="activeType === '{{ $col['key'] }}' ? 'bg-indigo-600 text-white shadow-[0_-4px_12px_rgba(79,70,229,0.25)] translate-y-0' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 translate-y-1 hover:translate-y-0'">
                <i class="{{ $col['icon'] }} text-sm" :class="activeType === '{{ $col['key'] }}' ? 'text-indigo-200' : 'text-slate-300'"></i>
                {{ $col['label'] }}

                {{-- Indicador de estado en la tab --}}
                <template x-if="requireData.archivos && requireData.archivos['{{ $col['key'] }}']">
                     <span class="w-1.5 h-1.5 rounded-full ml-1"
                           :class="{
                               'bg-green-400': requireData.archivos['{{ $col['key'] }}'].estado === 'Aprobado',
                               'bg-red-400': requireData.archivos['{{ $col['key'] }}'].estado === 'Corregir',
                               'bg-yellow-400': requireData.archivos['{{ $col['key'] }}'].estado === 'Enviado'
                           }"></span>
                </template>
            </button>
            @endforeach
        </div>
    </x-slot:hContent>

    <x-slot:bContent>
        <!-- Content init option -->
        <template x-if="activeType === 'init'">
            <div class="flex-1 flex flex-col items-center justify-center p-8 text-center animate-fadeIn dark:bg-slate-900">
                <div class="mb-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-full border border-dashed border-slate-200">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Visor de Documentos</h3>
                <p class="text-[10px] font-medium text-slate-400/80 max-w-[180px] leading-relaxed">Selecciona un archivo de la lista superior para visualizar su contenido</p>
            </div>
        </template>
        <template x-if="activeType !== 'init' && !ldata">
             <div class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-cloud-slash text-2xl text-slate-300"></i>
                </div>
                <h3 class="text-xs font-black uppercase text-slate-400">Sin Documento</h3>
                <p class="text-[10px] font-medium text-slate-400 mt-1">El docente aún no ha subido el archivo correspondiente.</p>
            </div>
        </template>
        <template x-if="activeType !== 'init' && ldata">
            <div class="flex-1 flex flex-col lg:flex-row dark:bg-slate-900 overflow-hidden">
                <div class="flex-1 flex flex-col p-4">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex-1 flex flex-col overflow-hidden">
                        <div class="h-10 bg-slate-50 dark:bg-slate-800/50 border-b flex items-center justify-between px-4 text-[9px] font-black text-slate-500 uppercase tracking-widest">
                            <span class="truncate max-w-[200px]" x-text="'Documento: ' + ldata.archivos[0]?.ruta.split('/').pop()"></span>
                            <div class="flex gap-2 shrink-0">
                                <a :href="'/' + urlFile" target="_blank" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    <i class="bi bi-box-arrow-up-right"></i> Expandir
                                </a>
                            </div>
                        </div>
                        <div class="flex-1 bg-slate-100 dark:bg-slate-950">
                            <iframe :src="'/' + urlFile" class="w-full h-full border-none" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-80 bg-white dark:bg-slate-900/50 border-l border-slate-200 dark:border-slate-800 flex flex-col shrink-0 order-1 lg:order-2 h-full">
                    <div class="flex-1 flex flex-col overflow-hidden h-full">
                        <!-- Navigation Tabs -->
                        <div class="flex border-b border-slate-200 dark:border-slate-800">
                            <button @click="docViewMode = 'info'" class="flex-1 py-4 text-[10px] font-black uppercase tracking-[0.2em] relative transition-colors" :class="docViewMode === 'info' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'">
                                Archivo Actual
                                <div x-show="docViewMode === 'info'" class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600"></div>
                            </button>
                            <button @click="docViewMode = 'history'" class="flex-1 py-4 text-[10px] font-black uppercase tracking-[0.2em] relative transition-colors" :class="docViewMode === 'history' ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'">
                                Historial
                                <div x-show="docViewMode === 'history'" class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600"></div>
                            </button>
                        </div>

                        <div x-show="docViewMode === 'info'" class="animate-fadeIn flex flex-col gap-3 p-4">
                            <!-- Compact Status Badge -->
                            <template x-if="ldata">
                                <!-- State file -->
                                <div class="flex items-center gap-3 p-3 rounded-xl border shadow-sm dark:bg-slate-800 dark:border-slate-800"
                                    :class="{ 
                                        'bg-green-50 border-green-100': ldata.state === 5,
                                        'bg-red-50 border-red-100': ldata.state > 1 && ldata.state < 5,
                                        'bg-yellow-50 border-yellow-100': ldata.state === 1
                                    }">
                                    
                                    <div class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg"
                                        :class="{
                                            'bg-green-500/10 text-green-500': ldata.state === 5,
                                            'bg-red-500/10 text-red-500': ldata.state > 1 && ldata.state < 5,
                                            'bg-yellow-500/10 text-yellow-500': ldata.state === 1
                                        }">
                                        <i :class="{
                                            'bi bi-check-lg': ldata.state === 5,
                                            'bi bi-x-lg': ldata.state > 1 && ldata.state < 5,
                                            'bi bi-exclamation-triangle-fill': ldata.state === 1
                                        }"></i>
                                    </div>

                                    <div class="flex-1 leading-tight">
                                        <h5 class="text-[11px] font-black uppercase tracking-wider"
                                            :class="{
                                                'text-green-700': ldata.state === 5,
                                                'text-red-700': ldata.state > 1 && ldata.state < 5,
                                                'text-yellow-700': ldata.state === 1
                                            }"
                                            x-text="ldata.state === 5 ? 'Aprobado' : (ldata.state > 1 && ldata.state < 5 ? 'Corregir' : 'Enviado')">
                                        </h5>
                                        <p class="text-[10px] font-medium opacity-80"
                                            :class="{
                                                'text-green-600': ldata.state === 5,
                                                'text-red-600': ldata.state > 1 && ldata.state < 5,
                                                'text-yellow-600': ldata.state === 1
                                            }"
                                            x-text="ldata.state === 5 ? 'Validado.' : (ldata.state > 1 && ldata.state < 5 ? 'Con correcciones.' : 'Espera calificación.')">
                                        </p>
                                    </div>
                                </div>
                                <div class="p-3 rounded-xl border flex items-center justify-between dark:bg-slate-800 dark:border-slate-800" 
                                        :class="{
                                        'bg-green-50 border-green-100 text-emerald-700 dark:text-emerald-500': ldata.state == 5,
                                        'bg-amber-50 border-amber-100 text-amber-700 dark:text-amber-500': ldata.state == 1,
                                        'bg-red-50 border-red-100 text-red-700 dark:text-red-500': [2,3,4].includes(ldata.state)
                                        }">
                                    <div class="flex items-center gap-2">
                                        <i class="bi text-sm" :class="{
                                            'bi-check-circle-fill text-emerald-500': ldata.state == 5,
                                            'bi-hourglass-split text-amber-500': ldata.state == 1,
                                            'bi-exclamation-triangle-fill text-rose-500': [2,3,4].includes(ldata.state)
                                        }"></i>
                                        <span class="text-[10px] font-black uppercase" x-text="ldata.state == 5 ? 'Aprobado' : (ldata.state == 1 ? 'Enviado' : 'Pendiente')"></span>
                                    </div>
                                    <span class="text-[10px] font-bold" x-text="'Nota: ' + ldata.nota"></span>
                                </div>
                            </template>

                            <template x-if="ldata.state == 1">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-2">
                                        <button @click="reviewState = 'approve'" :class="reviewState === 'approve' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-slate-50 text-slate-500'" class="py-3 rounded-xl text-[10px] font-black uppercase transition-all">
                                            <i class="bi bi-check2-circle mr-1"></i> Aprobar
                                        </button>
                                        <button @click="reviewState = 'observe'" :class="reviewState === 'observe' ? 'bg-rose-600 text-white shadow-lg' : 'bg-slate-50 text-slate-500'" class="py-3 rounded-xl text-[10px] font-black uppercase transition-all">
                                            <i class="bi bi-eye mr-1"></i> Observar
                                        </button>
                                    </div>

                                    <div x-show="reviewState === 'observe'" class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-3 animate-slideDown">
                                        <div>
                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">¿Qué debe corregir?</label>
                                            <select x-model="correccionTipo" class="w-full bg-white dark:bg-slate-900 border text-xs font-bold rounded-lg p-2 outline-none">
                                                <option value="2">Solo el Archivo (PDF)</option>
                                                <option value="3">Solo la Nota</option>
                                                <option value="4">Ambos (Archivo y Nota)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Comentarios</label>
                                            <textarea x-model="comentario" rows="3" class="w-full bg-white dark:bg-slate-900 border text-xs font-bold rounded-lg p-3 outline-none resize-none" placeholder="Indique el motivo de la observación..."></textarea>
                                        </div>
                                    </div>

                                    <button @click="submitReview()" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 transition-all disabled:opacity-50" :disabled="loading || !reviewState">
                                        <span x-show="!loading" x-text="reviewState === 'approve' ? 'Validar Documento' : 'Enviar Observación'"></span>
                                        <i x-show="loading" class="bi bi-arrow-repeat animate-spin"></i>
                                    </button>
                                </div>
                            </template>

                            <template x-if="ldata.state == 5">
                                <div class="text-center py-8 space-y-2 border-2 border-dashed border-emerald-100 rounded-3xl opacity-80 bg-emerald-50/20">
                                    <i class="bi bi-shield-check text-4xl text-emerald-500"></i>
                                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Documento Validado</p>
                                </div>
                            </template>
                        </div>

                        <div x-show="docViewMode === 'history'" class="animate-fadeIn flex flex-col gap-3 p-4">
                             <template x-for="item in hdata?.evaluacion_archivo" :key="item.id">
                                <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-[10px] font-black text-indigo-600" x-text="'Nota: ' + item.nota"></span>
                                        <span class="text-[8px] font-bold px-1.5 py-0.5 rounded uppercase" :class="item.state == 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="item.state == 5 ? 'Aprobado' : 'Observado'"></span>
                                    </div>
                                    <p class="text-[9px] text-slate-400 italic" x-text="item.observacion || 'Sin observaciones'"></p>
                                    <p class="text-[8px] text-slate-300 mt-2 text-right" x-text="new Date(item.created_at).toLocaleDateString()"></p>
                                </div>
                             </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </x-slot:bContent>
</x-body-container>
</div>
@endsection
