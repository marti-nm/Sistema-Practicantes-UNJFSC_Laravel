@extends('template')
@section('title', 'Administración de Prácticas')
@section('subtitle', 'Gestionar prácticas pre-profesionales')

@section('content')
@php
    // Procesamiento de datos para la vista (Mapeo de $personas a estructura JSON para Alpine)
    $studentsData = $personas->map(function($persona, $index) {
        $practica = optional($persona->asignacion_persona)->practicas->last();
        $stage = 0;
        $tipo = 'Sin asignar';
        $area = 'Sin asignar';
        $practicaId = null;

        if ($practica) {
            $stage = $practica->state;
            $tipo = $practica->tipo_practica == 'desarrollo' ? 'Desarrollo' : ($practica->tipo_practica == 'convalidacion' ? 'Convalidación' : $practica->tipo_practica);
            $practicaId = $practica->id;
            
            if ($practica->jefeInmediato) {
                $area = $practica->jefeInmediato->area ?: 'Área no reg.';
            }
        }

        // Definición de labels y colores para el estado/etapa
        $estadoLabel = 'Sin iniciar';
        $estadoColor = 'slate'; 
        
        switch ($stage) {
            case 0: $estadoLabel = 'Sin iniciar'; $estadoColor = 'slate'; break;
            case 1: $estadoLabel = 'Inicio'; $estadoColor = 'blue'; break;
            case 2: $estadoLabel = 'Desarrollo'; $estadoColor = 'indigo'; break;
            case 3: $estadoLabel = 'Seguimiento'; $estadoColor = 'indigo'; break;
            case 4: $estadoLabel = 'Finalización'; $estadoColor = 'purple'; break;
            case 5: $estadoLabel = 'Evaluación'; $estadoColor = 'emerald'; break;
            case 6: $estadoLabel = 'Calificado'; $estadoColor = 'green'; break;
            case 7: $estadoLabel = 'Edición Notas'; $estadoColor = 'amber'; break;
            default: $estadoLabel = 'Etapa '.$stage; $estadoColor = 'gray';
        }

        return [
            'id' => $index + 1,
            'id_ap' => $persona->asignacion_persona->id,
            'practica_id' => $practicaId,
            'escuela' => $persona->asignacion_persona->seccion_academica->escuela->name ?? '---',
            'seccion' => $persona->asignacion_persona->seccion_academica->seccion ?? '-',
            'tipo' => $tipo,
            'alumno' => strtoupper($persona->apellidos . ' ' . $persona->nombres),
            'area' => strtoupper($area),
            'avatar' => strtoupper(substr($persona->nombres, 0, 1) . substr($persona->apellidos, 0, 1)),
            'stage' => $stage,
            'estado_label' => $estadoLabel,
            'estado_color' => $estadoColor
        ];
    });
@endphp

<div class="h-[calc(100vh-120px)] flex flex-col overflow-hidden" 
     x-data="{ 
        viewMode: 'list', 
        selectedItem: null,
        searchQuery: '',
        showFilter: false,
        activeTab: 'sup', // Default a supervisión si tiene práctica
        
        // Supervisión state
        supStage: 1,
        supSubTab: null, // Sub-tab actual dentro de la etapa (ej. 'empresa', 'fut', etc.),
        docViewMode: 'file', // 'file' o 'history',

        //
        loading: false,
        stateStage: 0,
        optionStage: 0,
        requireFormStage: { option: null, state: null, id: null },
        dataEmpresa: null,
        dataJefe: null,
        ldata: null,
        hdata: null,
        urlFile: null,
        calificacion: null,
        storageKey: 'prac_admin_nav',

        init() {
            this.restoreState();
        },

        saveState() {
            if (this.selectedItem) {
                localStorage.setItem(this.storageKey, JSON.stringify({
                    id_ap: this.selectedItem.id_ap,
                    activeTab: this.activeTab,
                    supStage: this.supStage,
                    supSubTab: this.supSubTab,
                    docViewMode: this.docViewMode
                }));
            }
        },

        restoreState() {
            const saved = localStorage.getItem(this.storageKey);
            if (!saved) return;

            try {
                const keys = JSON.parse(saved);
                const freshItem = this.items.find(i => i.id_ap === keys.id_ap);

                if (freshItem) {
                    this.selectedItem = freshItem;
                    this.viewMode = 'evaluate';
                    this.activeTab = keys.activeTab || 'sup';
                    this.supStage = keys.supStage || 1;
                    this.docViewMode = keys.docViewMode || 'file';
                    
                    if (keys.supSubTab && keys.supSubTab !== 'init') {
                        this.setSubTab(keys.supSubTab);
                    } else {
                        this.supSubTab = 'init';
                    }
                } else {
                    this.clearSelection();
                }
            } catch (e) {
                console.error('Error restoring state', e);
                this.clearSelection();
            }
        },

        clearState() {
            localStorage.removeItem(this.storageKey);
        },
        
        // Mock Data de Doc Types por Etapa
        stageTabs: {
            1: [
                { id: 'empresa', label: 'Empresa', icon: 'bi-building' },
                { id: 'jefe', label: 'Jefe Inmediato', icon: 'bi-person-badge' }
            ],
            2: [
                { id: 'fut', label: 'FUT', icon: 'bi-file-earmark-text' },
                { id: 'carta_presentacion', label: 'Carta Present.', icon: 'bi-envelope-paper' },
                { id: 'carta_aceptacion', label: 'Carta Acept.', icon: 'bi-check2-circle' }
            ],
            3: [
                { id: 'plan_actividades_ppp', label: 'Plan Actividades', icon: 'bi-calendar-week' },
                { id: 'registro_actividades', label: 'Informes Mensuales', icon: 'bi-journal-text' }
            ],
            4: [
                { id: 'constancia_cumplimiento', label: 'Constancia', icon: 'bi-award' },
                { id: 'informe_final_ppp', label: 'Informe Final', icon: 'bi-journal-check' }
            ],
            5: [
                { id: 'evaluacion', label: 'Evaluación Final', icon: 'bi-star' }
            ]
        },

        // Inyectamos la data real mapeada desde PHP
        items: {{ json_encode($studentsData) }},

        get filteredItems() {
            if (!this.searchQuery) return this.items;
            const query = this.searchQuery.toLowerCase();
            return this.items.filter(item => 
                item.alumno.toLowerCase().includes(query) || 
                item.escuela.toLowerCase().includes(query) ||
                item.area.toLowerCase().includes(query)
            );
        },

        selectItem(item) {
            console.log('Selected Item:', item);
            this.selectedItem = item;
            this.viewMode = 'evaluate';
            // Reset supervision state
            this.supStage = (item.stage >= 5) ? 5 : (item.stage > 0 ? item.stage : 1);
            this.setSubTabDefault(this.supStage);
            this.saveState();
        },
        
        setStage(stage) {
            console.log('Set Stage:', stage);
            this.supStage = stage;
            this.setSubTabDefault(stage);
            this.saveState();
        },

        setSubTabDefault(stage) {
            this.supSubTab = 'init';
            console.log('Default SubTab set to:', this.supSubTab);
        },

        setSubTab(id) {
            this.supSubTab = id;
            this.docViewMode = 'file';
            console.log('Selected SubTab:', id, 'Current Stage:', this.supStage);
            this.urlFile = null;
            if(this.supSubTab === 'empresa') {
                this.fetchEmpresa();
            } else if(this.supSubTab === 'jefe') {
                this.fetchJefe();
            } else if(this.supSubTab !== 'init' && this.supSubTab !== 'evaluacion') {
                // Generic fetch for any document type (FUT, Carta, etc.)
                if (this.selectedItem && this.selectedItem.id_ap) {
                    this.fetchDocument(this.selectedItem.id_ap, this.supSubTab);
                }
            } else if(this.supSubTab === 'evaluacion') {
                this.fetchCalificacion();
            }
            this.saveState();
        },

        async fetchEmpresa() {
            this.loading = true;
            this.dataEmpresa = null;
            try {
                const response = await fetch(`/api/empresa/${this.selectedItem.practica_id}`);
                if (!response.ok) {
                    console.error('Error en la respuesta del servidor.');
                    return;
                }
                const empresa = await response.json();
                this.dataEmpresa = empresa;
                this.stateStage = empresa.state;
                this.optionStage = 1;
                this.requireFormStage = { option: 1, state: empresa.state, id: empresa.id };
                console.log('requireFormStage: ', this.requireFormStage);
                console.log('empresa: ', empresa);
            } finally {
                this.loading = false;
            }
        },

        async fetchJefe() {
            this.loading = true;
            this.dataJefe = null;
            try {
                const response = await fetch(`/api/jefeinmediato/${this.selectedItem.practica_id}`);
                if (!response.ok) {
                    console.error('Error en la respuesta del servidor.');
                    return;
                }
                const jefe = await response.json();
                this.dataJefe = jefe;
                this.stateStage = jefe.state;
                this.optionStage = 2;
                this.requireFormStage = { option: 2, state: jefe.state, id: jefe.id };
                console.log('requireFormStage: ', this.requireFormStage);
                console.log('jefe: ', jefe);
            } finally {
                this.loading = false;
            }
        },

        async fetchDocument(id_ap, type) {
            this.loading = true;
            this.ldata = null;
            this.hdata = null;
            try {
                const r = await fetch(`/api/documento/${id_ap}/${type}`);
                const result = await r.json();
                console.log('Dara rd: ', result);
                if(result && result.length > 0) {
                    this.hdata = result;
                    this.ldata = result[0];
                    this.urlFile = result[0].ruta;
                    console.log('ldata: ', this.ldata);
                    console.log('urlFile: ', this.urlFile);
                }
            } finally {
                this.loading = false;
            }
        },

        async fetchCalificacion() {
            this.loading = true;
            this.calificacion = null;
            try {
                const r = await fetch(`/api/practica/getCalificacion/${this.selectedItem.practica_id}`);
                const result = await r.json();
                this.calificacion = result;
                console.log('Dara rd: ', result);
            } finally {
                this.loading = false;
            }
        },

        clearSelection() {
            this.selectedItem = null;
            this.viewMode = 'list';
            this.clearState();
        }
     }" x-init="init()">
    
    <div class="flex flex-1 overflow-hidden gap-4 p-2">
        
        <!-- SIDEBAR: LISTADO -->
        <aside class="flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm transition-all duration-300 h-full overflow-hidden"
               :class="selectedItem ? 'w-80 hidden xl:flex' : 'w-full lg:w-1/3 flex'">
            
            <!-- Buscador y Header -->
            <div class="p-4 space-y-3 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" x-model="searchQuery" placeholder="Buscar estudiante..." 
                           class="w-full pl-10 pr-4 py-2.5 text-xs font-semibold border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50 rounded-xl focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                </div>
                
                <div class="flex justify-between items-center px-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Registros ({{$studentsData->count()}}+)</span>
                    <div class="flex gap-1 relative">
                        <button
                                class="p-1 rounded transition-colors relative"
                                :class="showFilter ? 'bg-indigo-50 text-indigo-600' : 'hover:bg-slate-200 text-slate-500'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4.5h18m-18 5h18m-18 5h18m-18 5h18"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Lista de Ítems -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-800 custom-scrollbar">
                <template x-for="item in filteredItems" :key="item.id">
                    <button @click="selectItem(item)" 
                            class="w-full p-4 flex items-start gap-3 hover:bg-indigo-50/40 transition-all text-left group relative border-l-[3px] border-transparent"
                            :class="selectedItem && selectedItem.id === item.id ? 'bg-indigo-50/60 dark:bg-slate-800/50 border-indigo-600' : 'hover:border-indigo-200'">
                        
                        <!-- Avatar / # -->
                        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center font-bold text-xs shadow-sm transition-colors"
                             :class="selectedItem && selectedItem.id === item.id ? 'bg-indigo-600 text-white' : 'bg-white border border-slate-200 text-slate-500 group-hover:border-indigo-200 group-hover:text-indigo-600'">
                            <span x-text="item.avatar"></span>
                        </div>
                        
                        <div class="flex-1 min-w-0 space-y-1">
                            <!-- Nombre -->
                            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate pr-2 group-hover:text-indigo-700 transition-colors" x-text="item.alumno"></h3>

                            <!-- Escuela y Sección -->
                            <div class="flex items-center gap-2 text-[10px] text-slate-400 font-medium">
                                <span x-text="item.escuela" class="truncate"></span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span x-text="'Sección ' + item.seccion" class="whitespace-nowrap"></span>
                            </div>

                            <!-- Tags: Tipo y Área -->
                            <div class="pt-1 flex flex-wrap gap-1.5" x-show="item.stage > 0">
                                <span class="px-2 py-0.5 rounded-md bg-white border border-slate-100 text-[9px] font-bold text-slate-500 uppercase tracking-wide group-hover:border-indigo-100 transition-colors" x-text="item.tipo"></span>
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wide"
                                      :class="{
                                          'bg-blue-50 text-blue-600 border border-blue-100': item.estado_color === 'blue',
                                          'bg-indigo-50 text-indigo-600 border border-indigo-100': item.estado_color === 'indigo',
                                          'bg-purple-50 text-purple-600 border border-purple-100': item.estado_color === 'purple',
                                          'bg-emerald-50 text-emerald-600 border border-emerald-100': item.estado_color === 'emerald',
                                          'bg-amber-50 text-amber-600 border border-amber-100': item.estado_color === 'amber',
                                          'bg-slate-50 text-slate-600 border border-slate-100': item.estado_color === 'slate'
                                      }"
                                      x-text="item.estado_label"></span>
                            </div>
                            
                            <!-- Barra de Progreso Mini -->
                            <div class="mt-2 w-full h-1 bg-slate-100 rounded-full overflow-hidden flex" x-show="item.stage > 0">
                                <template x-for="i in 5">
                                    <div class="h-full flex-1 border-r border-white last:border-0"
                                         :class="i <= (item.stage > 5 ? 5 : item.stage) ? 'bg-indigo-500' : 'bg-transparent'"></div>
                                </template>
                            </div>
                        </div>
                    </button>
                </template>
            </div>
        </aside>

        <!-- MAIN CONTENT: DETALLES -->
        <section class="flex-1 flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden relative"
                 x-show="selectedItem" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
            
            <!-- Header Detalle -->
            <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 pt-5 px-6 pb-0 flex flex-col shrink-0">
                <div class="flex items-start justify-between w-full mb-6">
                    <div class="flex gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-lg font-bold shadow-lg shadow-indigo-200 shrink-0"
                             x-text="selectedItem?.avatar">
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-800 dark:text-white leading-tight" x-text="selectedItem?.alumno"></h2>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span class="text-xs font-semibold text-slate-500" x-text="selectedItem?.escuela"></span>
                                <span class="w-1 h-3 bg-slate-200 rounded-full"></span>
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase rounded tracking-wider" x-text="selectedItem?.area"></span>
                            </div>
                        </div>
                    </div>
                    
                    <button @click="clearSelection()" class="p-2 hover:bg-slate-100 rounded-xl text-slate-400 hover:text-red-500 transition-colors" title="Cerrar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex gap-6 border-b border-slate-50 dark:border-slate-800">
                    <button @click="activeTab = 'info'; saveState()" 
                            class="pb-3 text-xs font-black uppercase tracking-widest border-b-2 transition-all"
                            :class="activeTab === 'info' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'">
                        Información General
                    </button>
                    <!-- Tab Supervisión solo si tiene práctica -->
                    <template x-if="selectedItem?.stage > 0">
                        <button @click="activeTab = 'sup'; saveState()" 
                                class="pb-3 text-xs font-black uppercase tracking-widest border-b-2 transition-all"
                                :class="activeTab === 'sup' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'">
                            Supervisión
                        </button>
                    </template>
                </div>
            </div>

            <!-- Content Body -->
            <div class="flex-1 overflow-hidden bg-slate-50/50 dark:bg-slate-900/50 flex flex-col h-full">
                
                <!-- Tab Información -->
                <div x-show="activeTab === 'info'" class="space-y-6 p-6 overflow-y-auto animate-fade-in custom-scrollbar">
                     <!-- Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <h4 class="text-[10px] font-black uppercase text-slate-400 mb-2">Tipo de Práctica</h4>
                            <p class="text-sm font-bold text-slate-700" x-text="selectedItem?.tipo"></p>
                        </div>
                         <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                            <h4 class="text-[10px] font-black uppercase text-slate-400 mb-2">Sección Académica</h4>
                            <p class="text-sm font-bold text-slate-700" x-text="selectedItem?.seccion"></p>
                        </div>
                    </div>
                </div>

                <!-- Tab Supervisión (New Design) -->
                <div x-show="activeTab === 'sup'" class="flex flex-1 h-full overflow-hidden animate-fade-in" x-cloak>
                    
                    <!-- Vertical Stepper (Left) -->
                    <div class="w-16 bg-white dark:bg-slate-900 h-full flex flex-col items-center py-6 gap-4 border-r border-slate-100 dark:border-slate-800 shrink-0">
                        <!-- uno por defecto -->
                        <template x-for="step in 5" :key="step">
                            <button @click="setStage(step)"
                                    class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all relative group"
                                    :class="{
                                        'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30 scale-110': supStage === step,
                                        'bg-blue-100 text-blue-600': supStage > step,
                                        'bg-slate-100 text-slate-400 hover:bg-slate-200': supStage < step
                                    }">
                                <span x-text="step"></span>
                                <!-- Tooltip -->
                                <div class="absolute left-full ml-3 px-2 py-1 bg-slate-800 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50">
                                    <span x-text="['Inicio', 'Desarrollo', 'Seguimiento', 'Finalización', 'Evaluación'][step-1]"></span>
                                </div>
                            </button>
                        </template>
                    </div>

                    <!-- Stage Content (Middle/Right) -->
                    <div class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50 dark:bg-slate-900">
                        
                        <!-- Top Tabs for Sub-Items -->
                        <div class="h-12 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 flex items-center px-4 gap-2 whitespace-nowrap overflow-x-auto no-scrollbar shrink-0">
                             <!-- Tab Inicial / Aviso -->
                             <button @click="setSubTab('init')" 
                                     class="px-4 py-1.5 rounded-t-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2"
                                     :class="supSubTab === 'init' ? 'bg-slate-500 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600'">
                                 <i class="bi bi-info-circle"></i>
                                 <span>Pendiente</span>
                             </button>

                             <template x-for="tab in stageTabs[supStage] || []" :key="tab.id">
                                 <button @click="setSubTab(tab.id)"
                                         class="px-4 py-1.5 rounded-t-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2"
                                         :class="supSubTab === tab.id ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-50' : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600'">
                                     <i class="bi" :class="tab.icon"></i>
                                     <span x-text="tab.label"></span>
                                 </button>
                             </template>
                        </div>

                        <!-- Content Area: Split View -->
                        <div class="flex-1 flex flex-col lg:flex-row h-full overflow-hidden">
                            <!-- Viewer (Left) -->
                            <div class="flex-1 bg-slate-100/50 dark:bg-slate-800/20 p-4 flex flex-col h-full overflow-hidden relative">
                                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex-1 flex flex-col overflow-hidden">
                                     <!-- CONTENIDO DINÁMICO SEGÚN LA ETAPA -->

                                     <!-- STATE: INIT (SIN SELECCIÓN) -->
                                     <template x-if="supSubTab === 'init'">
                                        <div class="flex-1 bg-white dark:bg-slate-900 flex flex-col items-center justify-center p-8 text-center animate-fadeIn">
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
                                     
                                     <!-- ETAPA 1: DATOS EMPRESA Y JEFE -->
                                     <template x-if="supStage === 1 && supSubTab === 'empresa'">
                                        <div class="flex-1 bg-slate-50 flex items-center justify-center p-8">
                                            <div class="text-center text-slate-400">
                                                <i class="bi bi-building text-4xl mb-2 block opacity-50"></i>
                                                <p class="text-xs font-medium uppercase">Información de Empresa y Jefe</p>
                                                <p class="text-[10px] opacity-70 mt-1" x-text="'Viendo: ' + (supSubTab === 'empresa' ? 'Datos de Empresa' : 'Datos del Jefe')"></p>
                                                <!-- Aquí cargaríamos el formulario/info real -->
                                                <template x-if="supSubTab === 'empresa' && dataEmpresa">
                                                    <div class="space-y-4 text-sm mt-4 text-left bg-white p-4 rounded-xl shadow-sm max-w-sm mx-auto">
                                                        <div>
                                                            <label class="text-xs font-bold text-slate-400 uppercase">Razón Social</label>
                                                            <span class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="dataEmpresa.razon_social || 'N/A'"></span>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs font-bold text-slate-400 uppercase">RUC</label>
                                                            <span class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="dataEmpresa.ruc || 'N/A'"></span>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs font-bold text-slate-400 uppercase">Dirección</label>
                                                            <span class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="dataEmpresa.direccion || 'N/A'"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                             </div>
                                         </div>
                                     </template>
                                    <template x-if="supStage === 1 && supSubTab === 'jefe'">
                                        <div class="flex-1 bg-slate-50 flex items-center justify-center p-8">
                                            <div class="text-center text-slate-400">
                                                <i class="bi bi-person-badge text-4xl mb-2 block opacity-50"></i>
                                                <p class="text-xs font-medium uppercase">Información del Jefe Inmediato</p>
                                                
                                                <template x-if="!dataJefe">
                                                     <div class="mt-4"><i class="bi bi-arrow-repeat animate-spin text-2xl"></i></div>
                                                </template>

                                                <template x-if="dataJefe">
                                                    <div class="space-y-4 text-sm mt-4 text-left bg-white p-4 rounded-xl shadow-sm max-w-sm mx-auto w-full">
                                                        <div>
                                                            <label class="text-xs font-bold text-slate-400 uppercase">Nombre Completo</label>
                                                            <div class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="dataJefe.nombres + ' ' + (dataJefe.apellidos || '')"></div>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs font-bold text-slate-400 uppercase">Cargo / Área</label>
                                                            <div class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="(dataJefe.cargo || '-') + ' / ' + (dataJefe.area || '-')"></div>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs font-bold text-slate-400 uppercase">Contacto</label>
                                                            <div class="font-semibold text-slate-700 dark:text-slate-200 mt-1 flex flex-col">
                                                                <span x-text="dataJefe.telefono"></span>
                                                                <span x-text="dataJefe.correo" class="text-blue-500"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- ETAPAS 2, 3, 4: VISOR DE DOCUMENTOS PDF -->
                                    <template x-if="supStage >= 2 && supStage <= 4 && supSubTab !== 'init'">
                                        <!-- Viewer -->
                                        <div class="flex-1 flex flex-col h-full">
                                            <!-- Toolbar -->
                                            <div class="h-10 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between px-4 bg-white dark:bg-slate-900">
                                                <span class="text-[10px] font-bold text-slate-400 uppercase">Vista Previa</span>
                                                <a :href="urlFile" target="_blank" class="text-[10px] text-indigo-600 font-bold hover:underline flex items-center gap-1" x-show="urlFile">
                                                    <i class="bi bi-box-arrow-up-right"></i> Abrir en ventana
                                                </a>
                                            </div>
                                            
                                            <!-- PDF Viewer -->
                                            <div x-show="loading" class="flex-1 bg-slate-400/10 flex items-center justify-center p-8">
                                                <i class="bi bi-file-earmark-pdf text-4xl mb-2 block opacity-50 animate-pulse"></i>
                                                <p class="text-xs font-medium text-slate-400">Cargando PDF...</p>
                                            </div>

                                            <div x-show="!loading && urlFile" class="flex-1 bg-slate-500/10 flex justify-center overflow-hidden h-full">
                                                <iframe 
                                                    :src="'/' + urlFile" 
                                                    class="w-full h-full min-h-[500px] border-none" 
                                                    @load="loading = false"
                                                    frameborder="0">
                                                </iframe>
                                            </div>

                                            <div x-show="!loading && !urlFile" class="flex-1 bg-slate-50 flex items-center justify-center p-8">
                                                <p class="text-xs font-bold text-slate-300 uppercase">Archivo no encontrado</p>
                                            </div>

                                        </div>
                                    </template>
                                     
                                     <!-- ETAPA 5: EVALUACIÓN -->
                                     <template x-if="supStage === 5 && supSubTab !== 'init'">
                                        <div class="flex flex-col items-center justify-center p-8">
                                            <template x-if="calificacion && calificacion.calificacion != null">
                                                <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
                                                    <i class="bi bi-trophy-fill"></i>
                                                </div>
                                                <h4 class="text-xl font-black text-slate-800 dark:text-white mb-2">¡Práctica Aprobada!</h4>
                                                <p class="text-slate-500 mb-6">El estudiante ha completado satisfactoriamente el proceso.</p>
                                                
                                                <div class="inline-block bg-white dark:bg-slate-900 px-8 py-4 rounded-xl shadow-lg mb-8">
                                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Calificación Final</span>
                                                    <span class="text-4xl font-black text-blue-600" x-text="calificacion.calificacion || 'NA'"></span>
                                                </div>
                                                <div>
                                            </template>
                                            <template x-if="!calificacion || calificacion.calificacion == null">
                                                <div class="text-center text-slate-400">
                                                    <i class="bi bi-trophy text-4xl mb-2 block opacity-50"></i>
                                                    <p class="text-xs font-medium uppercase">Registro de Calificación Final</p>
                                                </div>
                                            </template>
                                        </div>
                                     </template>
                                </div>
                            </div>

                            <!-- Sidebar Info/History (Right) -->
                            <div class="w-full lg:w-72 bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 flex flex-col h-full shrink-0">
                                <template x-if="loading">
                                    <div class="flex-1 bg-slate-400/10 flex items-center justify-center p-8">
                                        <i class="bi bi-file-earmark-pdf text-4xl mb-2 block opacity-50 animate-pulse"></i>
                                        <p class="text-xs font-medium text-slate-400">Cargando PDF...</p>
                                    </div>
                                </template>
                                <template x-if="!loading && supStage === 1">
                                    <div class="flex flex-col gap-3 p-4">
                                        <div class="flex items-center gap-3 p-3 rounded-xl border shadow-sm dark:bg-slate-800 dark:border-slate-800"
                                            :class="{ 
                                                'bg-green-50 border-green-100': requireFormStage.state === 1,
                                                'bg-yellow-50 border-yellow-100': requireFormStage.state === 2,
                                                'bg-red-50 border-red-100': requireFormStage.state === 3
                                            }">
                                            
                                            <div class="shrink-0 flex items-center justify-center w-8 h-8 rounded-lg"
                                                :class="{
                                                    'bg-green-500/10 text-green-500': requireFormStage.state === 1,
                                                    'bg-red-500/10 text-yellow-500': requireFormStage.state === 2,
                                                    'bg-yellow-500/10 text-red-500': requireFormStage.state === 3
                                                }">
                                                <i :class="{
                                                    'bi bi-check-lg': requireFormStage.state === 1,
                                                    'bi bi-exclamation-triangle-fill': requireFormStage.state === 2,
                                                    'bi bi-x-lg': requireFormStage.state === 3
                                                }"></i>
                                            </div>

                                            <div class="flex-1 leading-tight">
                                                <h5 class="text-[11px] font-black uppercase tracking-wider"
                                                    :class="{
                                                        'text-green-700': requireFormStage.state === 1,
                                                        'text-yellow-700': requireFormStage.state === 2,
                                                        'text-red-700': requireFormStage.state === 3
                                                    }"
                                                    x-text="requireFormStage.state == 1 ? 'Aprobado' : (requireFormStage.state == 2 ? 'Enviado' : 'Corregir')">
                                                </h5>
                                                <p class="text-[10px] font-medium opacity-80"
                                                    :class="{
                                                        'text-green-600': requireFormStage.state === 1,
                                                        'text-yellow-600': requireFormStage.state === 2,
                                                        'text-red-600': requireFormStage.state === 3
                                                    }"
                                                    x-text="requireFormStage.state == 1 ? 'Validado los datos.' : (requireFormStage.state == 2 ? 'Espera calificación.' : 'Con correcciones.')">
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <template x-if="requireFormStage.state === 2">
                                            <form action="{{ route('jefe_inmediato.actualizar.estado') }}" method="POST" class="animate-fade-in">
                                                @csrf
                                                <input type="hidden" name="id" id="id" :value="requireFormStage.id">
                                                <input type="hidden" name="option" id="option" :value="requireFormStage.option">
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
                                                    <textarea name="comentario" placeholder="Motivos de observación..." class="w-full h-20 p-3 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all resize-none"></textarea>
                                                </div>
                                                <div class="space-y-2">
                                                    <button type="submit"
                                                        class="px-5 py-1.5 w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs font-black rounded-xl hover:from-blue-700 hover:to-indigo-800 shadow-lg shadow-blue-500/30 transition-all active:scale-95 uppercase tracking-widest flex items-center gap-2">
                                                        <i class="bi bi-check-lg text-base"></i> Guardar
                                                    </button>
                                                </div>
                                            </form>
                                        </template>
                                    </div>
                                </template>
                                <!-- Toggle File/History -->
                                <template x-if="!loading && supStage >= 2 && supStage <= 4 && supSubTab !== 'init'">
                                    <div>
                                        <div class="flex border-b border-slate-100 dark:border-slate-800">
                                            <button @click="docViewMode = 'file', urlFile = ldata.ruta; saveState()" 
                                                    class="flex-1 py-3 text-[10px] font-black uppercase tracking-wider transition-colors border-b-2"
                                                    :class="docViewMode === 'file' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                                Archivo
                                            </button>
                                            <button @click="docViewMode = 'history', urlFile = null; saveState()" 
                                                    class="flex-1 py-3 text-[10px] font-black uppercase tracking-wider transition-colors border-b-2"
                                                    :class="docViewMode === 'history' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-400 hover:text-slate-600'">
                                                Historial
                                            </button>
                                        </div>
                                        <div class="flex-1 overflow-y-auto custom-scrollbar">
                                            <template x-if="docViewMode === 'file'">               
                                                <template x-if="ldata">
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
                                                            <form id="formValidacionDocente" action="{{ route('actualizar.archivo') }}" method="POST" class="animate-fade-in">
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
                                                                    <textarea name="comentario" placeholder="Motivos de observación..." class="w-full h-20 p-3 text-xs bg-slate-50 border dark:border-slate-700 border-slate-200 dark:bg-slate-800 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all resize-none"></textarea>
                                                                </div>
                                                                <div class="space-y-2">
                                                                    <button type="submit"
                                                                        class="px-5 py-1.5 w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs font-black rounded-xl hover:from-blue-700 hover:to-indigo-800 shadow-lg shadow-blue-500/30 transition-all active:scale-95 uppercase tracking-widest flex items-center gap-2">
                                                                        <i class="bi bi-check-lg text-base"></i> Guardar
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </template>
                                                        <div class="p-4 bg-indigo-50/50 dark:bg-indigo-950/50 rounded-xl border border-indigo-100 dark:border-indigo-700 text-[10px]">
                                                            <p class="font-bold text-indigo-400 uppercase mb-2">Detalles del Archivo</p>
                                                            <div class="space-y-1 text-slate-500">
                                                                <div class="flex justify-between"><span>Peso:</span><span class="font-bold text-slate-700" x-text="ldata.peso"></span></div>
                                                                <div class="flex justify-between"><span>Formato:</span><span class="font-bold text-slate-700" x-text="ldata.extension"></span></div>
                                                                <div class="flex justify-between"><span>Intento:</span><span class="font-bold text-slate-700" x-text="'#'+hdata.length"></span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </template>

                                            <template x-if="docViewMode === 'history'">
                                                <template x-if="hdata.length > 1">
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
                                            </template>
                                        </div>
                                        
                                    </div>
                                </template>

                                <template x-if="supStage === 5 && supSubTab !== 'init'">
                                    <div class="flex flex-col p-4">
                                        <div class="mb-8">
                                            <h3 class="text-lg font-black text-slate-800 dark:text-white">Evaluación Final</h3>
                                            <p class="text-sm text-slate-500 mt-1">Calificación y cierre del proceso.</p>
                                        </div>
                                        <template x-if="calificacion">
                                            <div>
                                                <template x-if="calificacion.state > 5">
                                                    <div class="flex flex-col gap-4">
                                                        

                                                        <!-- Actions for Graded State -->
                                                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                                                            <!-- Button for Student/Supervisor to Request Edit -->
                                                            <button type="button"
                                                                class="px-6 py-3 rounded-xl bg-orange-50 text-orange-600 font-bold text-xs uppercase tracking-widest hover:bg-orange-100 transition shadow-sm border border-orange-100">
                                                                <i class="bi bi-pencil-square mr-2"></i> Solicitar Edición
                                                            </button>

                                                            <!-- Button for Admin to Review Request (Only if Role 1 or 2, usually checked by blade or JS) -->
                                                            @if(Auth::user()->hasAnyRoles([1, 2]))
                                                                <div class="relative">
                                                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                                                    </span>
                                                                    <button type="button"
                                                                        class="px-6 py-3 rounded-xl bg-blue-600 text-white font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                                                                        <i class="bi bi-shield-check mr-2"></i> Revisar Solicitud
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="calificacion.state === 5">
                                                    <form action="{{ route('practica.calificar') }}" method="POST" class="max-w-md mx-auto">
                                                        @csrf
                                                        <input type="hidden" name="practica_id" :value="calificacion.id">
                                                        
                                                        <div class="mb-6">
                                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Asignar Calificación (0 - 20)</label>
                                                            <input type="number" name="calificacion" min="0" max="20" step="0.01" required
                                                                class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-center text-xl font-bold focus:ring-2 focus:ring-blue-500 outline-none">
                                                        </div>
                                                        
                                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-widest py-4 rounded-xl shadow-lg shadow-blue-600/20 transition-all">
                                                            Finalizar y Calificar
                                                        </button>
                                                    </form>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- EMPTY STATE: SIN SELECCIÓN -->
        <div class="hidden lg:flex flex-1 flex-col items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-10 text-center" 
             x-show="!selectedItem">
            <div class="relative mb-6 group">
                <div class="absolute inset-0 bg-indigo-100 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative w-24 h-24 bg-slate-50 dark:bg-slate-800 rounded-3xl border-2 border-slate-100 dark:border-slate-700 flex items-center justify-center shadow-sm -rotate-3 group-hover:rotate-0 transition-transform duration-300">
                    <svg class="w-10 h-10 text-slate-300 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <h3 class="text-base font-black text-slate-700 dark:text-white mb-2 uppercase tracking-widest">
                Gestión de Prácticas
            </h3>
            <p class="text-xs text-slate-400 max-w-[280px] leading-relaxed">
                Selecciona un estudiante del listado para ver su información detallada, documentos y estado actual.
            </p>
        </div>

    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection