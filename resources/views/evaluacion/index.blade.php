@extends('template')
{{-- Los títulos ahora son estáticos para no depender del controlador --}}
@section('title', 'Gestión de Evaluaciones (Diseño)')
@section('subtitle', 'Maqueta de administración de evaluaciones y entrevistas')

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
        apiEndpoint: "{{ $config["api_endpoint"] }}",

        // UI States for Files
        docViewMode: "info",
        dragOver: false,
        tempFile: null,
        tempFileName: null,
        tempFileSize: null,
        verifying: false,

        // Form fields for Evaluation
        nota: "",
        comentario: "",

        facultadId: "{{ $facultad_id ?? "" }}",
        escuelaId: "{{ $escuela_id ?? "" }}",
        seccionId: "{{ $seccion_id ?? "" }}",
        escuelas: [],
        secciones: [],
        grupoId: "{{ $id_grupo ?? "" }}",
        grupos: [],

        // La lista de usuarios ahora viene del controlador
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
                console.log(this.grupos);
            } finally { this.loading = false; }
        },

        selectModule(moduleId, locked) {
            if (locked) {
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "info",
                        title: "Módulo bloqueado",
                        text: "No puedes avanzar a este módulo hasta que se habilite según la etapa actual.",
                        toast: true,
                        position: "top-end",
                        timer: 2500,
                        showConfirmButton: false,
                    });
                } else {
                    alert("Módulo bloqueado. No puedes seleccionar este módulo.");
                }
                return;
            }
            document.getElementById("selected_modulo").value = moduleId;
            document.getElementById("form-modulo").submit();
        },

        storageKey: "val_nav_evaluacion_{{ auth()->id() }}",

        async init() {
            this.restoreState();
            
            if (this.facultadId) {
                await this.fetchEscuelas(true);
            }
            if (this.escuelaId) {
                await this.fetchSecciones(true);
            }
            if (this.facultadId || this.grupoId) {
                await this.fetchGroups(true);
            }
        },

        saveState() {
            if (this.selectedItem && this.requireData.id) {
                localStorage.setItem(this.storageKey, JSON.stringify({
                    id: this.requireData.id,
                    type: this.activeType,
                    opt: this.selectedOption,
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
                this.selectedOption = keys.opt;
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
            this.clearTempFile();

            try {
                let apiType = type;
                if (type === "anexo7") apiType = "anexo_7";
                if (type === "anexo8") apiType = "anexo_8";

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
                        
                        // Set form fields from ldata
                        this.nota = this.ldata.nota || "";
                        this.comentario = this.ldata.observacion || "";
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
            this.selectedOption = true;
            this.docViewMode = "info";
            this.clearTempFile();
            this.saveState();
        },

        openContentFiles(type) {
            this.activeType = type;
            this.selectedOption = true;
            this.docViewMode = "info";
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

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        // --- File Handling Methods (Similar to Matricula) ---
        handleDrop(e) {
            this.dragOver = false;
            const file = e.dataTransfer.files[0];
            this.processFile(file);
        },

        handleFileSelect(e) {
            const file = e.target.files[0];
            this.processFile(file);
        },

        async processFile(file) {
            if (!file) return;

            this.verifying = true;
            await new Promise(resolve => setTimeout(resolve, 600));

            if (file.type !== "application/pdf") {
                this.verifying = false;
                Swal.fire({
                    title: "Formato Inválido",
                    text: "Solo se aceptan archivos PDF.",
                    icon: "error",
                    confirmButtonColor: "#2563eb"
                });
                return;
            }
            
            if (file.size > 20 * 1024 * 1024) {
                this.verifying = false;
                Swal.fire({
                    title: "Archivo excedido",
                    text: "El peso máximo permitido es de 20MB.",
                    icon: "error",
                    confirmButtonColor: "#2563eb"
                });
                return;
            }

            this.tempFile = file;
            this.tempFileName = file.name;
            this.tempFileSize = (file.size / 1024 / 1024).toFixed(2) + " MB";
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.urlFile = e.target.result;
            };
            reader.readAsDataURL(file);
            
            this.verifying = false;
        },

        clearTempFile() {
            this.tempFile = null;
            this.tempFileName = null;
            this.tempFileSize = null;
            this.urlFile = (this.ldata && this.ldata.archivos && this.ldata.archivos.length > 0) ? this.ldata.archivos[0].ruta : null;
            if (this.$refs.fileInput) this.$refs.fileInput.value = "";
        },

        async uploadEvaluation() {
            if (!this.tempFile && !(this.ldata && [2,3,4].includes(this.ldata.state))) {
                Swal.fire("Falta archivo", "Debe seleccionar un archivo para subir.", "warning");
                return;
            }
            if (!this.nota) {
                Swal.fire("Falta nota", "Debe ingresar una nota válida.", "warning");
                return;
            }

            this.loading = true;
            const formData = new FormData();
            if (this.tempFile) {
                formData.append("anexo", this.tempFile);
            } else if (this.ldata && this.ldata.archivos && this.ldata.archivos.length > 0) {
                formData.append("rutaAnexo", this.ldata.archivos[0].ruta);
            }

            formData.append("ap_id", this.requireData.id_ap);
            formData.append("modulo", this.requireData.id_modulo);
            formData.append("nota", this.nota);
            formData.append("number", this.activeType === "anexo7" ? 7 : 8);
            formData.append("_token", "{{ csrf_token() }}");

            try {
                const r = await fetch("{{ route("subir.anexo") }}", {
                    method: "POST",
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                });

                if (r.ok) {
                    Swal.fire({
                        title: "¡Calificación Registrada!",
                        text: "El anexo y la nota han sido guardados correctamente.",
                        icon: "success",
                        confirmButtonColor: "#2563eb"
                    }).then(() => {
                        this.fetchGetFiles(this.requireData.id, this.activeType);
                    });
                } else {
                    const err = await r.json();
                    Swal.fire("Error", err.message || "No se pudo subir la evaluación", "error");
                }
            } catch (e) {
                Swal.fire("Error", "Error de conexión al servidor", "error");
            } finally {
                this.loading = false;
            }
        }
    }' x-init="init()">

<div class="px-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl p-3 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

            <div class="flex-1 flex items-center gap-3 ml-2">
                <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 leading-tight">Mis Grupos Asignados</h2>
                    <p class="text-[11px] text-slate-400 uppercase tracking-tighter">Docente: David Admin</p>
                </div>
            </div>
            <div class="flex items-center gap-3 min-w-[300px]">
                <label for="grupo-docente" class="hidden md:block text-xs font-semibold text-slate-500 whitespace-nowrap">
                    Filtrar:
                </label>
                <div class="flex gap-2 relative w-full">
                    <select name="facultad" x-model="facultadId" @change="fetchEscuelas()"
                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900 bg-slate-50/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all cursor-pointer text-slate-600">
                        <option value="">-- Todas --</option>
                        @foreach($facultades as $fac)
                            <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                        @endforeach
                    </select>

                    <select name="escuela" x-model="escuelaId" @change="fetchSecciones()" :disabled="!facultadId || loading"
                        :class="!facultadId ? 'bg-slate-100 dark:bg-slate-800/40 opacity-60 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800/50'"
                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900 bg-slate-50/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all cursor-pointer text-slate-600">
                        <option value="">-- Todas --</option>
                        <template x-for="escuela in escuelas" :key="escuela.id">
                            <option :value="escuela.id" x-text="escuela.name" :selected="escuela.id == escuelaId"></option>
                        </template>
                    </select>

                    <select name="seccion" x-model="seccionId" :disabled="!escuelaId || loading"
                        :class="!escuelaId ? 'bg-slate-100 dark:bg-slate-800/40 opacity-60 cursor-not-allowed' : 'bg-slate-50 dark:bg-slate-800/50'"
                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900 bg-slate-50/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all cursor-pointer text-slate-600">
                        <option value="">-- Todas --</option>
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

            <div class="flex items-center gap-3 min-w-[300px]">
                <label for="grupo-docente" class="hidden md:block text-xs font-semibold text-slate-500 whitespace-nowrap">
                    Cambiar grupo:
                </label>
                <div class="relative w-full">
                    <form method="GET" action="{{ route('seguimiento.evaluar') }}" class="relative z-10">
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
    <form id="form-modulo" method="GET" action="{{ route('seguimiento.evaluar') }}" class="w-full">
        <input type="hidden" name="grupo" value="{{ $id_grupo }}">
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
                $locked = is_null($id_grupo) || is_null($currentModulo) || ($m > $currentModulo);
                @endphp
                <div class="relative">
                    <div
                        class="module-selector-cell group relative w-full p-3 rounded-xl border-1 transition-all duration-200 flex items-center justify-center gap-3 cursor-pointer
                        {{ $isActive
                            ? 'bg-gradient-to-br from-blue-600 to-indigo-600 border-transparent text-white shadow-lg shadow-blue-500/30 transform scale-[1.02]'
                            : ($locked
                                ? 'bg-slate-50 dark:bg-slate-800/50 border-slate-100 dark:border-slate-800 text-slate-300 dark:text-slate-600 cursor-not-allowed'
                                : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:border-blue-300 hover:shadow-md')
                        }}"
                        role="button"
                        tabindex="{{ $locked ? '-1' : '0' }}"
                        aria-disabled="{{ $locked ? 'true' : 'false' }}"
                        @click="selectModule({{ $m }}, {{ $locked ? 'true' : 'false' }})"
                        @keydown.enter.prevent="selectModule({{ $m }}, {{ $locked ? 'true' : 'false' }})"
                        @keydown.space.prevent="selectModule({{ $m }}, {{ $locked ? 'true' : 'false' }})">

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
                <div class="flex gap-1 relative">
                    <button @click="showFilter = !showFilter"
                            class="p-1 rounded transition-colors relative"
                            :class="showFilter ? 'bg-indigo-50 text-indigo-600' : 'hover:bg-slate-200 text-slate-500'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4.5h18m-18 5h18m-18 5h18m-18 5h18"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Panel de Filtros Expandible -->
            <div x-show="showFilter" x-transition.origin.top.duration.300ms class="px-1 py-2 space-y-2 bg-slate-50/50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700">
                <div class="space-y-2">
                    <select x-model="facultadId" @change="fetchEscuelas()" class="w-full text-xs p-2 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-1 focus:ring-indigo-500 outline-none">
                        <option value="">Todas las Facultades</option>
                        @foreach($facultades as $fac)
                            <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                        @endforeach
                    </select>

                    <select x-model="escuelaId" @change="fetchSecciones()" :disabled="!facultadId" class="w-full text-xs p-2 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-1 focus:ring-indigo-500 outline-none disabled:opacity-50">
                        <option value="">Todas las Escuelas</option>
                        <template x-for="esc in escuelas" :key="esc.id">
                            <option :value="esc.id" x-text="esc.name"></option>
                        </template>
                    </select>

                    <select x-model="seccionId" @change="fetchGroups()" :disabled="!escuelaId" class="w-full text-xs p-2 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-1 focus:ring-indigo-500 outline-none disabled:opacity-50">
                        <option value="">Todas las Secciones</option>
                        <template x-for="sec in secciones" :key="sec.id">
                            <option :value="sec.id" x-text="sec.name"></option>
                        </template>
                    </select>

                    <div class="flex gap-2 pt-1">
                        <button @click="facultadId=''; escuelaId=''; seccionId=''; fetchGroups(); showFilter=false" class="flex-1 py-1.5 text-[10px] font-bold uppercase text-slate-500 hover:bg-slate-200 rounded-lg transition-colors text-center">Limpiar</button>
                        <button @click="fetchGroups(); showFilter=false" class="flex-1 py-1.5 text-[10px] font-bold uppercase text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm shadow-indigo-500/30">Aplicar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-800 custom-scrollbar">
            <template x-for="user in pagedUsers" :key="user.id">
                <button @click="openItem(user)" class="w-full p-4 flex items-start gap-3 hover:bg-indigo-50/30 transition-all text-left relative"
                    :class="selectedItem && requireData.id === user.id ? 'bg-indigo-50/50 dark:bg-slate-800' : ''">
                    <div x-show="selectedItem && requireData.id === user.id" class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600"></div>

                    <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 flex items-center justify-center font-bold text-slate-500 text-xs" x-text="user.avatar"></div>

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
        <!-- Hidden File Input for Drag & Drop / Click -->
        <input type="file" x-ref="fileInput" class="hidden" @change="handleFileSelect" accept="application/pdf">

        <div class="flex items-center justify-between w-full mb-2">
            <div class="flex items-center gap-4">
                <button @click="clearState()" class="xl:hidden p-2 hover:bg-slate-100 rounded-lg text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </button>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 flex flex-wrap items-center gap-2">
                        <span x-text="requireData.people" class="truncate max-w-[200px] sm:max-w-none"></span>
                        <span class="text-[10px] font-normal text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded" x-text="'ID: ' + requireData.id"></span>
                    </h2>
                    <div class="flex flex-wrap gap-2 sm:gap-3 text-[10px] sm:text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-tight mt-1">
                        <span x-text="requireData.escuela" class="truncate max-w-[120px] sm:max-w-none"></span>
                        <span class="text-slate-300 hidden sm:inline">|</span>
                        <span class="text-slate-500" x-text="'SECC. ' + requireData.seccion"></span>
                        <span class="text-slate-300 hidden sm:inline">|</span>
                        <span class="text-slate-500" x-text="'SEM. ' + requireData.semestre"></span>
                    </div>
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

        <!-- Content no send user -->
        <template x-if="!loading && activeType !== 'init' && !ldata && !tempFile">
            <div class="flex-1 flex flex-col items-center justify-center p-12 text-center animate-fadeIn cursor-pointer relative"
                 @click="$refs.fileInput.click()"
                 @dragover.prevent="dragOver = true"
                 @dragleave.prevent="dragOver = false"
                 @drop.prevent="handleDrop">
                
                <div x-show="dragOver" class="absolute inset-0 z-40 bg-indigo-600/10 border-4 border-dashed border-indigo-500/50 m-4 rounded-xl flex items-center justify-center backdrop-blur-[2px]">
                    <div class="text-center">
                        <i class="bi bi-cloud-arrow-up-fill text-5xl text-indigo-600 mb-4 block"></i>
                        <p class="text-sm font-black text-indigo-600 uppercase tracking-widest">¡Suelta para cargar!</p>
                    </div>
                </div>

                <div class="relative mb-6">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center group-hover:border-indigo-400 transition-colors">
                        <i class="bi bi-file-earmark-arrow-up text-4xl text-slate-300 group-hover:text-indigo-400"></i>
                    </div>
                </div>
                <h3 class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-2">Subir Evaluación</h3>
                <p class="text-[10px] font-medium text-slate-400 leading-relaxed max-w-[200px]">Arrastra el PDF calificado aquí o haz clic para seleccionarlo.</p>
            </div>
        </template>

        <!-- Content send user or previewing temp -->
        <template x-if="activeType !== 'init' && (ldata || tempFile)">
            <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden dark:bg-slate-900 relative">
                
                <!-- DROPZONE INDICATOR -->
                <div x-show="dragOver" x-transition.opacity
                    class="absolute inset-0 z-50 bg-indigo-600/10 pointer-events-none border-4 border-dashed border-indigo-500/50 m-4 rounded-xl flex items-center justify-center backdrop-blur-[2px]">
                    <div class="text-center">
                        <i class="bi bi-cloud-arrow-up-fill text-5xl text-indigo-600 mb-4 block animate-bounce"></i>
                        <p class="text-sm font-black text-indigo-600 uppercase tracking-widest">¡Suelta para actualizar!</p>
                    </div>
                </div>

                <!-- Visor -->
                <div class="flex-1 flex flex-col p-4 order-2 lg:order-1 h-[500px] lg:h-full shrink-0"
                     @dragover.prevent="dragOver = true"
                     @dragleave.prevent="dragOver = false"
                     @drop.prevent="handleDrop">
                    
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex-1 flex flex-col overflow-hidden">
                        <div class="h-12 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between px-5 text-[10px] font-bold text-slate-500 uppercase">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center shrink-0">
                                    <i class="bi bi-eye-fill text-indigo-500"></i>
                                </div>
                                <span class="truncate max-w-[150px] sm:max-w-[250px]" x-text="tempFile ? 'Vista Previa: ' + tempFileName : 'Archivo: ' + ldata?.archivos[0]?.ruta.split('/').pop()"></span>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <!-- Cambiar solo si es nuevo o requiere corrección de archivo/ambos -->
                                <template x-if="(!ldata || [2,4].includes(ldata.state)) && !tempFile">
                                    <button @click="$refs.fileInput.click()" class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 text-[10px] font-black rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all flex items-center gap-2">
                                        <i class="bi-arrow-repeat"></i> Cambiar
                                    </button>
                                </template>
                                <a :href="tempFile ? urlFile : (urlFile?.startsWith('/') ? urlFile : '/' + urlFile)" target="_blank" class="px-4 py-1.5 bg-indigo-600 text-white border-0 text-[10px] font-black rounded-xl hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-sm">
                                    <i class="bi-box-arrow-up-right"></i> Expandir
                                </a>
                            </div>
                        </div>
                        <div class="flex-1 bg-slate-100 dark:bg-slate-950 flex justify-center overflow-hidden">
                            <iframe :src="tempFile ? urlFile : (urlFile?.startsWith('/') ? urlFile : '/' + urlFile)" class="w-full h-full border-none shadow-inner" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>

                <!-- Right Control Panel -->
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

                        <!-- Panel Content -->
                        <div class="flex-1 overflow-y-auto custom-scrollbar">                            
                            <!-- TAB: INFO & EVALUATION FORM -->
                            <div x-show="docViewMode === 'info'" class="animate-fadeIn flex flex-col gap-3 p-4">
                                <!-- Compact Status Badge -->
                                <template x-if="ldata && !tempFile">
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

                                <!-- Compact Correction Box -->
                                <template x-if="ldata && [2,3,4].includes(ldata.state) && !tempFile">
                                    <div class="p-3 bg-rose-50 dark:bg-rose-900/10 border border-rose-100 rounded-xl space-y-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-[9px] font-black uppercase text-rose-600">Motivo de Corrección</p>
                                            <span class="text-[8px] font-black uppercase text-rose-500 bg-rose-100 px-1.5 py-0.5 rounded"
                                                  x-text="ldata.state == 2 ? 'Archivo' : (ldata.state == 3 ? 'Nota' : 'Ambos')"></span>
                                        </div>
                                        <p class="text-[10px] font-bold text-rose-800 dark:text-rose-300 italic leading-tight" x-text="ldata.observacion || 'Sin detalle disponible.'"></p>
                                    </div>
                                </template>

                                <!-- EVALUATION FORM (Hidden if Approved (5) or Sent (1)) -->
                                <template x-if="(!ldata || [2,3,4].includes(ldata.state))">
                                    <div class="space-y-4">
                                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                                            <div class="mb-3">
                                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Nota Final (0-20)</label>
                                                <div class="relative group">
                                                    <input type="number" x-model="nota" min="0" max="20" 
                                                           :readonly="ldata && ldata.state == 2"
                                                           class="w-full bg-white dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-base font-black text-indigo-600 dark:text-indigo-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                                                           :class="ldata && ldata.state == 2 ? 'opacity-60 cursor-not-allowed' : ''"
                                                           placeholder="00">
                                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs font-bold">/ 20</div>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Observaciones</label>
                                                <textarea x-model="comentario" rows="2" 
                                                          class="w-full bg-white dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs font-bold text-slate-600 dark:text-slate-300 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all resize-none"
                                                          placeholder="Opcional..."></textarea>
                                            </div>

                                            <!-- Final Action Button -->
                                            <button @click="uploadEvaluation()" 
                                                    class="w-full py-3 rounded-xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-indigo-700 hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-2"
                                                    :disabled="loading">
                                                <template x-if="!loading">
                                                    <div class="flex items-center gap-2">
                                                        <i class="bi bi-cloud-arrow-up-fill text-base"></i>
                                                        <span x-text="tempFile ? 'Subir Calificación' : (ldata ? 'Guardar Cambios' : 'Subir Calificación')"></span>
                                                    </div>
                                                </template>
                                                <template x-if="loading">
                                                    <div class="flex items-center gap-2">
                                                        <i class="bi bi-arrow-repeat animate-spin"></i>
                                                        <span>Procesando...</span>
                                                    </div>
                                                </template>
                                            </button>
                                            
                                            <template x-if="tempFile">
                                                <button @click="clearTempFile()" class="w-full mt-2 py-1.5 text-[8px] font-black text-slate-400 hover:text-rose-500 uppercase tracking-widest transition-colors">
                                                    Cancelar Subida
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Approved/Sent State Message (Compact) -->
                                <template x-if="ldata && (ldata.state == 5 || ldata.state == 1)">
                                    <div class="text-center py-8 px-4 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-3xl space-y-3">
                                        <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 flex items-center justify-center rounded-2xl mx-auto">
                                            <i class="bi text-2xl" :class="ldata.state == 5 ? 'bi-shield-lock-fill text-emerald-400' : 'bi-clock-history text-amber-400'"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider" x-text="ldata.state == 5 ? 'Lectura Protegida' : 'En Espera'"></h4>
                                            <p class="text-[10px] font-bold text-slate-400 mt-1 leading-tight" x-text="ldata.state == 5 ? 'Esta evaluación ya ha sido validada por el administrador.' : 'El documento ha sido enviado y está en cola para revisión académica.'"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- TAB: HISTORY -->
                            <div x-show="docViewMode === 'history'" class="animate-fadeIn flex flex-col gap-3 p-4">
                                <template x-if="hdata && hdata.evaluacion_archivo && hdata.evaluacion_archivo.length > 0">
                                    <div class="">
                                        <template x-for="(item, index) in hdata.evaluacion_archivo" :key="index">
                                            <div @click="urlFile = item.archivos[0]?.ruta; ldata = item; tempFile = null" 
                                                 class="group p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-1 transition-all cursor-pointer"
                                                 :class="ldata?.id === item.id ? 'border-indigo-500 bg-indigo-50/10 ring-4 ring-indigo-500/5' : 'border-slate-100 dark:border-slate-800 hover:border-slate-300'">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-lg bg-white dark:bg-slate-900 flex items-center justify-center shadow-sm border border-slate-100 dark:border-slate-700">
                                                            <i class="bi bi-clipboard-check text-[10px] text-slate-400 group-hover:text-indigo-500 transition-colors"></i>
                                                        </div>
                                                        <span class="text-[11px] font-black text-slate-700 dark:text-slate-200">Nota: <span class="text-indigo-600" x-text="item.nota"></span></span>
                                                    </div>
                                                    <div class="flex flex-col items-end gap-1">
                                                        <span class="text-[8px] font-black px-2 py-0.5 rounded-full uppercase"
                                                            :class="{
                                                                'bg-green-100 text-green-700': item.state == 5,
                                                                'bg-red-100 text-red-700': [2,3,4].includes(item.state),
                                                                'bg-blue-100 text-blue-700': item.state == 1
                                                            }"
                                                            x-text="item.state == 5 ? 'Aprobado' : ([2,3,4].includes(item.state) ? 'Observado' : 'Revision')"></span>
                                                        <template x-if="[2,3,4].includes(item.state)">
                                                            <span class="text-[7px] font-bold text-rose-500 uppercase tracking-tighter" x-text="item.state == 2 ? 'Corregir Archivo' : (item.state == 3 ? 'Corregir Nota' : 'Corregir Ambos')"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <div class="text-[9px] text-slate-400 font-bold flex items-center gap-1">
                                                        <i class="bi bi-calendar3"></i>
                                                        <span x-text="item.archivos && item.archivos.length > 0 ? new Date(item.archivos[0].created_at).toLocaleDateString() : 'Sin fecha'"></span>
                                                    </div>
                                                    <i class="bi bi-chevron-right text-slate-300 group-hover:translate-x-1 transition-transform"></i>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!hdata || !hdata.evaluacion_archivo || hdata.evaluacion_archivo.length === 0">
                                    <div class="py-12 text-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4 grayscale opacity-40">
                                            <i class="bi bi-clock-history text-3xl text-slate-300"></i>
                                        </div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sin registros previos</p>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </template>
    </x-slot:bContent>
</x-body-container>

@endsection