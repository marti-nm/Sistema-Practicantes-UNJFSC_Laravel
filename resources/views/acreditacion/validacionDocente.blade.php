@extends('template')

@section('title', 'Acreditación del Docente')
@section('subtitle', 'Gestionar acreditación del oficial')

@section('content')
<div class="h-[calc(100vh-120px)] flex flex-col overflow-hidden" 
     x-data="{ 
        viewMode: 'list', 
        selectedItem: false,
        requireData: { id:null, type:null, people:null, escuela:null, seccion:null, semestre:null, tipo:null},
        ldata: null,
        hdata: null,
        loading: false,
        selectedOption: true,
        urlFile: null,
        currentPage: 1,
        itemsPerPage: 10,
        searchQuery: '',
        showFilter: {{ request('facultad') || request('escuela') || request('seccion') ? 'true' : 'false' }},
        activeType: 'init',
        // Inyectamos la data fresca de Laravel para reconstrucción
        userList: [
            @foreach($usuarios as $item)
            @php $acr = $item->asignacion_persona->acreditacion->first(); @endphp
            {
                id: {{ $acr->id ?? 'null' }},
                estado: '{{ $acr->estado_acreditacion ?? 'Pendiente' }}',
                people: '{{ $item->apellidos.', '.$item->nombres }}',
                escuela: '{{ $item->asignacion_persona->seccion_academica->escuela->name }}',
                facultad: '{{ $item->asignacion_persona->seccion_academica->escuela->facultad->name }}',
                seccion: '{{ $item->asignacion_persona->seccion_academica->seccion }}',
                semestre: '{{ $item->asignacion_persona->semestre->codigo }}'
            },
            @endforeach
        ],

        init() {
            this.restoreState();
        },

        saveState() {
            if (this.selectedItem && this.requireData.id) {
                localStorage.setItem('val_nav_keys', JSON.stringify({
                    id: this.requireData.id,
                    type: this.activeType,
                    opt: this.selectedOption
                }));
            }
        },

        restoreState() {
            const saved = localStorage.getItem('val_nav_keys');
            if (!saved) return;

            const keys = JSON.parse(saved);
            // Buscamos al usuario en la lista fresca enviada por el servidor
            const freshUser = this.userList.find(u => u.id === keys.id);

            if (freshUser) {
                // Reconstruimos con Nombres/Datos actualizados del servidor
                this.requireData = { ...freshUser };
                this.selectedItem = true;
                this.viewMode = 'evaluate';
                this.activeType = keys.type;
                this.selectedOption = keys.opt;

                if (this.activeType !== 'init') {
                    this.fetchAccredit(this.requireData.id, this.activeType);
                }
            } else {
                // Si ya no existe en la lista (borrado/procesado), limpiamos rastro
                this.clearState();
            }
        },

        clearState() {
            localStorage.removeItem('val_nav_keys');
            this.selectedItem = false;
            this.viewMode = 'list';
            this.requireData = { id:null, type:null, people:null, escuela:null, seccion:null, semestre:null, tipo:null};
            this.activeType = 'init';
        },

        async fetchAccredit(id, type) {
            if (!id || !type || type === 'init') return;
            this.loading = true;
            this.ldata = null;
            this.hdata = null;

            try {
                const r = await fetch(`/api/acreditacion/archivos/${id}/${type}`);
                const result = await r.json();
                if(result && result.length > 0) {
                    this.hdata = result;
                    this.ldata = result[0];
                    this.urlFile = result[0].ruta;
                }
            } finally { 
                this.loading = false;
                this.saveState();
            }
        },

        openItem(data) {
            this.ldata = null;
            this.hdata = null;
            this.selectedItem = true;
            this.requireData = data;
            this.viewMode = 'evaluate';
            this.activeType = 'init';
            this.selectedOption = true;
            this.saveState();
        },

        openContentFiles(type) {
            this.activeType = type;
            this.selectedOption = true;
            this.fetchAccredit(this.requireData.id, type);
        },

        // LÓGICA DE FILTRADO Y PAGINACIÓN
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
        }
     }" x-init="init()">
    <div class="flex flex-1 overflow-hidden gap-4 p-2">
        
        <!-- ASIDE: LISTA DE ESTUDIANTES -->
        <aside class="flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm transition-all duration-300 h-full overflow-hidden"
               :class="viewMode === 'evaluate' ? 'w-80 hidden xl:flex' : 'w-full lg:w-1/3 flex'">
            
            <div class="p-4 space-y-3 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Buscar usuario..." class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50 rounded-lg focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                </div>
                <div class="flex justify-between items-center px-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Usuarios ({{$usuarios->count()}}+)</span>
                    <div class="flex gap-1 relative">
                        <button @click="showFilter = !showFilter" 
                                class="p-1 rounded transition-colors relative"
                                :class="showFilter ? 'bg-indigo-50 text-indigo-600' : 'hover:bg-slate-200 text-slate-500'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4.5h18m-18 5h18m-18 5h18m-18 5h18"></path></svg>
                            @if(request('facultad') || request('escuela') || request('seccion'))
                            <div class="absolute top-0 right-0 w-2 h-2 bg-indigo-500 rounded-full ring-1 ring-white"></div>
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Panel de Filtros Expandible -->
                <div x-show="showFilter" x-transition.origin.top.duration.300ms class="px-1 py-2 space-y-2 bg-slate-50/50 dark:bg-slate-800/50 rounded-lg border border-slate-100 dark:border-slate-700">
                    <form action="{{ url()->current() }}" method="GET" id="filterForm">
                        <div class="space-y-2">
                            <select name="facultad" id="facultadSelect" class="w-full text-xs p-2 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-1 focus:ring-indigo-500 outline-none">
                                <option value="">Todas las Facultades</option>
                                @foreach($facultades as $fac)
                                    <option value="{{ $fac->id }}" {{ request('facultad') == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                                @endforeach
                            </select>
                            
                            <select name="escuela" id="escuelaSelect" class="w-full text-xs p-2 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-1 focus:ring-indigo-500 outline-none">
                                <option value="">Todas las Escuelas</option>
                            </select>
                            
                            <select name="seccion" id="seccionSelect" class="w-full text-xs p-2 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-1 focus:ring-indigo-500 outline-none">
                                <option value="">Todas las Secciones</option>
                            </select>

                            <div class="flex gap-2 pt-1">
                                <a href="{{ url()->current() }}" class="flex-1 py-1.5 text-[10px] font-bold uppercase text-slate-500 hover:bg-slate-200 rounded-lg transition-colors text-center">Limpiar</a>
                                <button type="submit" class="flex-1 py-1.5 text-[10px] font-bold uppercase text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm shadow-indigo-500/30">Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Listado -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-800 custom-scrollbar">
                {{-- usar userList con Template para la lista --}}
                <template x-for="user in pagedUsers" :key="user.id">
                    <button @click="openItem({
                        id: user.id,
                        type: 'carga_lectiva',
                        people: user.people,
                        escuela: user.escuela,
                        seccion: user.seccion,
                        semestre: user.semestre,
                        tipo: 'Carga Lectiva'
                    })" class="w-full p-4 flex items-start gap-3 hover:bg-indigo-50/30 transition-all text-left relative"
                        :class="selectedItem && requireData.id === user.id ? 'bg-indigo-50/50 dark:bg-slate-800' : ''">
                        <div x-show="selectedItem && requireData.id === user.id" class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600"></div>
                        <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 flex items-center justify-center font-bold text-slate-500 text-xs">AP</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-400 truncate" x-text="user.people"></p>
                                <span class="text-[9px] px-1.5 py-0.5 rounded font-black uppercase"
                                    :class="{
                                        'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-200': user.estado === 'Aprobado',
                                        'bg-amber-100 dark:bg-amber-800 text-amber-600 dark:text-amber-200': user.estado === 'Pendiente'
                                    }"
                                    x-text="user.estado"></span>
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium" x-text="user.escuela"></p>
                            <div class="mt-2 flex gap-2">
                                <span class="text-[9px] bg-slate-100 dark:bg-slate-800 text-slate-500 px-1 rounded" x-text="'Semestre '+user.semestre"></span>
                                <span class="text-[9px] bg-slate-100 text-slate-500 px-1 rounded" x-text="'Sección '+user.seccion"></span>
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
        </aside>

        <!-- MAIN CONTENT: EVALUACIÓN -->
        <section class="flex-1 flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden" x-show="selectedItem" x-cloak>
            <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 pt-4 px-4 flex flex-col shrink-0">
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
                    
                    {{-- icono para cerrar X --}}
                    <button @click="clearState()" class="hidden xl:block p-2 hover:bg-red-50 hover:text-red-500 rounded-xl transition-all text-slate-400 group relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span class="absolute right-full mr-2 top-1/2 -translate-y-1/2 px-2 py-1 bg-slate-800 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-all">Cerrar</span>
                    </button>
                </div>

                <!-- Tabs Modernas al ras del borde, scroll horizontal -->
                <div class="flex gap-1 mt-auto overflow-x-auto overflow-y-hidden no-scrollbar w-full items-end pb-[1px]">
                    <!-- Tab Inicial / Aviso -->
                    <button @click="activeType = 'init'; ldata = null; hdata = null" 
                            class="group relative px-6 py-2.5 text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2 rounded-t-xl"
                            :class="activeType === 'init' ? 'bg-gray-500 text-white shadow-[0_-4px_12px_rgba(245,158,11,0.25)] translate-y-0' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 translate-y-1 hover:translate-y-0'">
                        <i class="bi-info-circle text-sm" :class="activeType === 'init' ? 'text-gray-200' : 'text-slate-300'"></i>
                        Pendiente
                    </button>

                    <button @click="openContentFiles('carga_lectiva')" 
                            class="group relative px-6 py-2.5 text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2 rounded-t-xl"
                            :class="activeType === 'carga_lectiva' ? 'bg-lime-500 text-white shadow-[0_-4px_12px_rgba(79,70,229,0.25)] translate-y-0' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 translate-y-1 hover:translate-y-0'">
                        <i class="bi-file-earmark-text text-sm" :class="activeType === 'carga_lectiva' ? 'text-lime-200' : 'text-slate-300'"></i>
                        Carga Lectiva
                    </button>
                    <button @click="openContentFiles('horario')" 
                            class="group relative px-6 py-2.5 text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2 rounded-t-xl"
                            :class="activeType === 'horario' ? 'bg-indigo-600 text-white shadow-[0_-4px_12px_rgba(79,70,229,0.25)] translate-y-0' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 translate-y-1 hover:translate-y-0'">
                        <i class="bi-calendar3 text-sm" :class="activeType === 'horario' ? 'text-indigo-200' : 'text-slate-300'"></i>
                        Horario
                    </button>
                    <button @click="openContentFiles('resolucion')" 
                            class="group relative px-6 py-2.5 text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center gap-2 rounded-t-xl"
                            :class="activeType === 'resolucion' ? 'bg-pink-600 text-white shadow-[0_-4px_12px_rgba(79,70,229,0.25)] translate-y-0' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800/50 translate-y-1 hover:translate-y-0'">
                        <i class="bi-calendar3 text-sm" :class="activeType === 'resolucion' ? 'text-pink-200' : 'text-slate-300'"></i>
                        Resolución
                    </button>
                </div>
            </div>
            <!-- Content init option -->
            <template x-if="activeType === 'init'">
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center animate-fadeIn dark:bg-slate-900">
                    <div class="mb-4 p-4 bg-slate-50 dark:bg-slate-800 rounded-full border border-dashed border-slate-200">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>

                    <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">
                        Visor de Documentos
                    </h3>
                    
                    <p class="text-[10px] font-medium text-slate-400/80 max-w-[180px] leading-relaxed">
                        Selecciona un archivo de la lista superior para visualizar su contenido
                    </p>

                    <div class="mt-6 flex gap-1">
                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                    </div>
                </div>
            </template>
            <!-- Content no send user -->
            <template x-if="!loading && activeType !== 'init' && !ldata">
                <div class="flex-1 flex flex-col items-center justify-center p-12 text-center animate-fadeIn">
                    <div class="relative mb-6">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-center">
                            <i class="bi bi-folder2-open text-3xl text-slate-300"></i>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100">
                            <i class="bi bi-search text-[10px] text-slate-400"></i>
                        </div>
                    </div>

                    <h3 class="text-[11px] font-black uppercase tracking-widest text-slate-500 mb-2">
                        Sin archivos enviados
                    </h3>
                    
                    <div class="space-y-1">
                        <p class="text-[10px] font-medium text-slate-400 leading-relaxed">
                            El usuario aún no ha cargado el documento 
                        </p>
                        <p class="text-[9px] font-bold text-indigo-400/70 uppercase">
                            Esperando acción del usuario
                        </p>
                    </div>

                    <div class="mt-8 w-12 h-0.5 bg-slate-100 rounded-full mx-auto"></div>
                </div>
            </template>

            <!-- Content send user -->
            <template x-if="activeType !== 'init'">
                <div class="flex-1 flex flex-col lg:flex-row overflow-y-auto lg:overflow-hidden dark:bg-slate-900 relative">
                    <!-- Visor -->
                    <div class="flex-1 flex flex-col p-4 order-2 lg:order-1 h-[500px] lg:h-full shrink-0">
                        <template x-if="loading">
                            <div class="flex flex-1 animate-pulse">
                                <div class="flex-1 bg-slate-100/50 flex justify-center overflow-hidden">
                                    <div class="w-full max-w-xl bg-white shadow-sm rounded-lg p-10 flex flex-col gap-6">
                                        <div class="h-4 bg-slate-100 w-1/3 rounded"></div>
                                        <div class="space-y-3">
                                            <div class="h-3 bg-slate-50 w-full rounded"></div>
                                            <div class="h-3 bg-slate-50 w-full rounded"></div>
                                            <div class="h-3 bg-slate-50 w-2/3 rounded"></div>
                                        </div>
                                        <div class="flex-1 bg-slate-50/50 border-2 border-dashed border-slate-100 rounded-xl flex items-center justify-center">
                                            <span class="text-[10px] font-bold text-slate-200 uppercase tracking-widest">Vista Previa</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="ldata && !loading">
                            <!-- Content viewer -->
                            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex-1 flex flex-col overflow-hidden">
                                <div class="h-10 bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between px-4 text-[10px] font-bold text-slate-500 uppercase">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span x-text="'Vista Previa: ' + ldata?.ruta.split('/').pop()"></span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a :href="ldata.ruta" target="_blank" class="px-3 py-1.5 border text-[10px] font-bold rounded-lg hover:text-white transition-all flex items-center gap-2 shrink-0 uppercase bg-slate-50 dark:bg-slate-800"
                                            :class="{
                                                'border-green-600 text-green-600 hover:bg-green-600': ldata.estado_archivo === 'Aprobado',
                                                'border-red-600 text-red-600 hover:bg-red-600': ldata.estado_archivo === 'Corregir',
                                                'border-yellow-600 text-yellow-600 hover:bg-yellow-600': ldata.estado_archivo === 'Enviado'
                                            }">
                                            <i class="bi-box-arrow-up-right"></i>
                                            <span class="hidden sm:inline">Abrir</span>
                                            <span class="sm:hidden">PDF</span>
                                        </a>
                                        <div class="flex gap-2">
                                            <button class="hover:text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 bg-slate-500/10 flex justify-center overflow-y-auto custom-scrollbar">
                                    <iframe 
                                        :src="`${urlFile}#view=FitH`" 
                                        class="w-full h-full min-h-[300px] border-none" 
                                        frameborder="0">
                                    </iframe>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="w-full lg:w-72 bg-white dark:bg-slate-900/50 border-b lg:border-b-0 lg:border-l border-slate-200 dark:border-slate-800 flex flex-col shrink-0 order-1 lg:order-2 h-auto lg:h-auto overflow-hidden">
                        <template x-if="loading">
                            <div class="p-5 flex-1 space-y-6 overflow-y-auto animate-pulse">
                                <div>
                                    <div class="h-3 w-24 bg-slate-200 rounded mb-3"></div> <div class="grid grid-cols-2 gap-2">
                                        <div class="h-10 bg-slate-100 rounded-lg border-2 border-slate-50"></div>
                                        <div class="h-10 bg-slate-100 rounded-lg border-2 border-slate-50"></div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div class="h-3 w-28 bg-slate-200 rounded"></div> <div class="w-full h-32 bg-slate-50 border border-slate-100 rounded-xl"></div>
                                </div>

                                <div class="p-4 bg-slate-50/50 rounded-xl border border-slate-100 space-y-3">
                                    <div class="h-3 w-20 bg-indigo-100 rounded"></div>
                                    <div class="space-y-2">
                                        <div class="h-2 w-full bg-slate-100 rounded"></div>
                                        <div class="h-2 w-3/4 bg-slate-100 rounded"></div>
                                        <div class="h-2 w-1/2 bg-slate-100 rounded"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50 border-t border-slate-100 animate-pulse">
                                <div class="w-full h-9 bg-red-50/50 rounded-lg"></div>
                            </div>
                        </template>
                        <template x-if="!loading && ldata && hdata">
                            <div class="flex-1 flex flex-col overflow-hidden">
                                    <div class="mt-3.5">
                                        <div class="flex items-center border-b border-slate-200">
                                            <button @click="selectedOption = true, urlFile = ldata.ruta" 
                                                class="flex-1 py-2 text-[10px] font-black uppercase tracking-wider transition-all relative"
                                                :class="selectedOption === true ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'">
                                                Archivo Actual
                                                <div x-show="selectedOption === true" 
                                                    class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 rounded-t-full">
                                                </div>
                                            </button>

                                            <button @click="selectedOption = false, urlFile = null" 
                                                class="flex-1 py-2 text-[10px] font-black uppercase tracking-wider transition-all relative"
                                                :class="selectedOption === false ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600'">
                                                Historial
                                                <div x-show="selectedOption === false" 
                                                    class="absolute bottom-0 left-0 w-full h-0.5 bg-indigo-600 rounded-t-full">
                                                </div>
                                            </button>
                                        </div>
                                    </div>                
                                <div x-show="selectedOption" class="flex-1 overflow-y-auto custom-scrollbar">
                                    <template x-if="!loading && ldata">
                                        <div class="flex flex-col gap-3 p-4">
                                            <!-- State file -->
                                            <div class="flex items-center gap-3 p-3 rounded-xl border shadow-sm dark:bg-slate-800 dark:border-slate-800"
                                                :class="{ 
                                                    'bg-green-50 border-green-100': ldata.estado_archivo === 'Aprobado',
                                                    'bg-red-50 border-red-100': ldata.estado_archivo === 'Corregir',
                                                    'bg-yellow-50 border-yellow-100': ldata.estado_archivo === 'Enviado'
                                                }">
                                                
                                                <div class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg"
                                                    :class="{
                                                        'bg-green-500/10 text-green-500': ldata.estado_archivo === 'Aprobado',
                                                        'bg-red-500/10 text-red-500': ldata.estado_archivo === 'Corregir',
                                                        'bg-yellow-500/10 text-yellow-500': ldata.estado_archivo === 'Enviado'
                                                    }">
                                                    <i :class="{
                                                        'bi bi-check-lg': ldata.estado_archivo === 'Aprobado',
                                                        'bi bi-x-lg': ldata.estado_archivo === 'Corregir',
                                                        'bi bi-exclamation-triangle-fill': ldata.estado_archivo === 'Enviado'
                                                    }"></i>
                                                </div>

                                                <div class="flex-1 leading-tight">
                                                    <h5 class="text-[11px] font-black uppercase tracking-wider"
                                                        :class="{
                                                            'text-green-700': ldata.estado_archivo === 'Aprobado',
                                                            'text-red-700': ldata.estado_archivo === 'Corregir',
                                                            'text-yellow-700': ldata.estado_archivo === 'Enviado'
                                                        }"
                                                        x-text="ldata.estado_archivo">
                                                    </h5>
                                                    <p class="text-[10px] font-medium opacity-80"
                                                        :class="{
                                                            'text-green-600': ldata.estado_archivo === 'Aprobado',
                                                            'text-red-600': ldata.estado_archivo === 'Corregir',
                                                            'text-yellow-600': ldata.estado_archivo === 'Enviado'
                                                        }"
                                                        x-text="ldata.estado_archivo == 'Aprobado' ? 'Validado.' : (ldata.estado_archivo == 'Enviado' ? 'Espera calificación.' : 'Con correcciones.')">
                                                    </p>
                                                </div>
                                            </div>
                                            <template x-if="ldata.estado_archivo === 'Enviado'">
                                                <form id="formValidacionDocente" action="{{ route('actualizar.estado.archivo') }}" method="POST" class="animate-fade-in">
                                                    @csrf
                                                    <input type="hidden" name="id" id="id" :value="ldata.id">
                                                    <input type="hidden" name="tipo" id="tipo" :value="ldata.tipo">
                                                    <input type="hidden" name="acreditacion" id="acreditacion" :value="ldata.archivo_id">
                                                    <div class="space-y-2">
                                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Dictamen por Archivo</h4>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <label class="cursor-pointer group">
                                                                <input type="radio" name="estado" value="Aprobado" class="hidden peer" checked>
                                                                <div class="flex items-center justify-center gap-2 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs transition-all peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-md group-hover:border-emerald-300">
                                                                    <i class="bi bi-check-lg"></i> Aprobar
                                                                </div>
                                                            </label>
                                                            <label class="cursor-pointer group">
                                                                <input type="radio" name="estado" value="Corregir" class="hidden peer">
                                                                <div class="flex items-center justify-center gap-2 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs transition-all peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-md group-hover:border-rose-300">
                                                                    <i class="bi bi-exclamation-triangle"></i> Corregir
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                                                            Observaciones
                                                        </h4>
                                                        <textarea name="comentario" placeholder="Motivos de observación..." class="w-full h-20 p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all resize-none"></textarea>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <button type="submit"
                                                            class="px-5 py-1.5 w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs font-black rounded-xl hover:from-blue-700 hover:to-indigo-800 shadow-lg shadow-blue-500/30 transition-all active:scale-95 uppercase tracking-widest flex items-center gap-2">
                                                            <i class="bi bi-check-lg text-base"></i> Guardar
                                                        </button>
                                                    </div>
                                                </form>
                                            </template>
                                            <div class="p-4 bg-indigo-50/50 rounded-xl border border-indigo-100 text-[10px]">
                                                <p class="font-bold text-indigo-400 uppercase mb-2">Detalles del Archivo</p>
                                                <div class="space-y-1 text-slate-500">
                                                    <div class="flex justify-between"><span>Peso:</span><span class="font-bold text-slate-700" x-text="ldata.peso"></span></div>
                                                    <div class="flex justify-between"><span>Formato:</span><span class="font-bold text-slate-700" x-text="ldata.extension"></span></div>
                                                    <div class="flex justify-between"><span>Intento:</span><span class="font-bold text-slate-700" x-text="'#'+hdata.length"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="!selectedOption" class="flex-1 overflow-y-auto custom-scrollbar">
                                    <template x-if="!loading && hdata.length > 1">
                                        <div class="p-4 space-y-4">
                                            <template x-for="(item, index) in hdata" :key="index">
                                                <div x-show="index > 0" @click="urlFile = item.ruta" class="bg-slate-50 dark:bg-slate-800 cursor-pointer p-2 rounded-xl border-1 border-slate-100 dark:border-slate-700 flex justify-between items-center hover:bg-slate-50 transition-colors">
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase"
                                                                    :class="{
                                                                    'bg-green-100 text-green-700': item.estado_archivo == 'Aprobado',
                                                                    'bg-red-100 text-red-700': item.estado_archivo == 'Corregir',
                                                                    'bg-blue-100 text-blue-700': item.estado_archivo == 'Enviado'
                                                                    }"
                                                                    x-text="item.estado_archivo == 'Aprobado' ? 'Aprobado' : (item.estado_archivo == 'Corregir' ? 'Observado' : 'Enviado')">
                                                            </span>
                                                            <div class="text-[10px] text-slate-400 flex items-center gap-1">
                                                                <i class="bi bi-calendar3"></i>
                                                                <span x-text="item ? new Date(item.created_at).toLocaleString() : 'Sin fecha'"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <template x-if="item">
                                                        <button @click="urlFile = item.ruta" class="text-blue-600 hover:text-blue-800 text-[10px] font-bold bg-blue-50 px-2 py-1 rounded-lg transition-colors uppercase">
                                                            <i class="bi bi-file-earmark-pdf"></i> Ver
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>                            
                            </div>
                        </template>
                </div>
            </template>
        </section>

        <!-- Estado vacío (Oculto en móvil si no hay selección para dar prioridad a la lista) -->
        <div class="hidden lg:flex flex-1 flex-col items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-10 text-center" x-show="!selectedItem">
            <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800/50 rounded-3xl shadow-sm flex items-center justify-center mb-4 border border-slate-100 dark:border-slate-800">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-sm font-black text-slate-700 dark:text-white mb-1 uppercase tracking-wider">Sin selección</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-[200px] dark:text-slate-300">Selecciona un usuario de la lista para gestionar sus acreditaciones.</p>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection

@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const facultadSelect = document.getElementById('facultadSelect');
        const escuelaSelect = document.getElementById('escuelaSelect');
        const seccionSelect = document.getElementById('seccionSelect');
        const semestreActivoId = "{{ session('semestre_actual_id') }}";

        // Valores iniciales (si hay reload)
        const initFacultad = "{{ request('facultad') }}";
        const initEscuela = "{{ request('escuela') }}";
        const initSeccion = "{{ request('seccion') }}";

        async function cargarEscuelas(facultadId, selectedEscuela = null) {
            if (!facultadId) {
                escuelaSelect.innerHTML = '<option value="">Todas las Escuelas</option>';
                seccionSelect.innerHTML = '<option value="">Todas las Secciones</option>';
                return;
            }
            escuelaSelect.innerHTML = '<option value="">Cargando...</option>';
            try {
                const res = await fetch(`/api/escuelas/${facultadId}`);
                const data = await res.json();
                let options = '<option value="">Todas las Escuelas</option>';
                data.forEach(e => {
                    const selected = e.id == selectedEscuela ? 'selected' : '';
                    options += `<option value="${e.id}" ${selected}>${e.name}</option>`;
                });
                escuelaSelect.innerHTML = options;
                
                // Si había escuela seleccionada, cargar sus secciones
                if (selectedEscuela) {
                    cargarSecciones(selectedEscuela, initSeccion);
                }
            } catch (e) {
                escuelaSelect.innerHTML = '<option value="">Error</option>';
            }
        }

        async function cargarSecciones(escuelaId, selectedSeccion = null) {
            if (!escuelaId) {
                seccionSelect.innerHTML = '<option value="">Todas las Secciones</option>';
                return;
            }
            seccionSelect.innerHTML = '<option value="">Cargando...</option>';
            try {
                const res = await fetch(`/api/secciones/${escuelaId}/${semestreActivoId}`);
                const data = await res.json();
                let options = '<option value="">Todas las Secciones</option>';
                data.forEach(s => {
                    const selected = s.id == selectedSeccion ? 'selected' : '';
                    options += `<option value="${s.id}" ${selected}>${s.name}</option>`;
                });
                seccionSelect.innerHTML = options;
            } catch (e) {
                seccionSelect.innerHTML = '<option value="">Error</option>';
            }
        }

        // Listeners
        facultadSelect.addEventListener('change', function() {
            cargarEscuelas(this.value);
            seccionSelect.innerHTML = '<option value="">Todas las Secciones</option>'; // Reset dependiente
        });

        escuelaSelect.addEventListener('change', function() {
            cargarSecciones(this.value);
        });

        // Inicialización
        if (initFacultad) {
            cargarEscuelas(initFacultad, initEscuela);
        }
    });
</script>
@endpush