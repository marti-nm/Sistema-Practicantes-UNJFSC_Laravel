@extends('template')
@section('title', 'Validación de Matrícula')
@section('subtitle', 'Gestionar académica oficial')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        validateModalOpen: false,
        showHistory: false,
        validarModalOpen: false,
        validarData: null,
        loading: false,
        requireData: { id_ap: null, type: null, state: null, people: null, tipo: null },
        ldata: null,
        hdata: null,
        currentData: [],
        historyData: [],

        async fetchMatricula(id_ap, type) {
            this.loading = true;
            this.showHistory = false;
            this.currentData = [];
            this.historyData = [];
            this.ldata = null;
            this.hdata = null;
            try {
                const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));
                await sleep(1000);
                const response = await fetch(`/api/matricula/${id_ap}/${type}`);
                const data = await response.json();
                console.log('Data alpine: ', data);

                if(data && data.length > 0) {
                    this.hdata = data;
                    this.ldata = data[0];
                }

                if(data.length === 0){
                    this.currentData = [];
                    this.historyData = [];
                }else{
                    this.currentData = data[0];
                    if(data.length > 1){
                        this.historyData = data[1];
                    }
                }
                
                console.log('Holaa', this.hdata);
            } finally { this.loading = false; }
        },

        openMatriculaModal(data) {
            this.requireData = data;
            this.validateModalOpen = true;
            this.fetchMatricula(data.id_ap, data.type);
        },
    }">
    <x-header-content
        title="Lista de Estudiantes para Matrícula"
        subtitle="Gestionar y validar documentos académicos de estudiantes"
        icon="bi-patch-check-fill"
        :enableButton="false"
    />

    @if(auth()->user()->getRolId() == 1)
        <x-data-filter
        route="Validacion.Matricula"
        :facultades="$facultades"
        />
    @endif

    @include('components.skeletonLoader-table')
    <div class="overflow-x-auto">
        <table id="tablaMatricula" class="w-full text-left border-separate border-spacing-0 table-skeleton-ready rounded-t-2xl overflow-hidden">
            <thead>
                <tr class="bg-gradient-to-r from-blue-800 to-blue-600 text-white">
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">ID</th>
                        <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-[0.15em] border-none">Escuela</th>
                        <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-[0.15em] border-none">Estudiante</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">F Matrícula</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">R Académico</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900/50">
                @foreach ($estudiantes as $index => $item)
                @php
                    $matricula = $item->asignacion_persona->matricula->first();
                    $archivosPorTipo = $matricula ? $matricula->archivos->groupBy('tipo') : collect();

                    $getLatest = function ($tipo) use ($archivosPorTipo) {
                        $history = $archivosPorTipo->get($tipo);
                        return $history ? $history->first() : null;
                    };

                    $getTailwindClass = function ($estado) {
                        switch ($estado) {
                            case 'Aprobado':
                                return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800';
                            case 'Enviado':
                                return 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800';
                            case 'Corregir':
                                return 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200 dark:border-rose-800';
                            default:
                                return 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700 opacity-70';
                        }
                    };

                    $latestFicha = $getLatest('ficha');
                    $estadoFicha = $latestFicha ? $latestFicha->estado_archivo : 'Falta';
                    $class_ficha = $getTailwindClass($estadoFicha);
                    $icon_ficha = $estadoFicha == 'Aprobado' ? 'bi-check-circle-fill' : ($estadoFicha == 'Corregir' ? 'bi-x-circle-fill' : ($estadoFicha == 'Enviado' ? 'bi-file-earmark-text-fill' : 'bi-dash-circle'));

                    $latestRecord = $getLatest('record');
                    $estadoRecord = $latestRecord ? $latestRecord->estado_archivo : 'Falta';
                    $class_record = $getTailwindClass($estadoRecord);
                    $icon_record = $estadoRecord == 'Aprobado' ? 'bi-check-circle-fill' : ($estadoRecord == 'Corregir' ? 'bi-x-circle-fill' : ($estadoRecord == 'Enviado' ? 'bi-file-earmark-text-fill' : 'bi-dash-circle'));
                @endphp
                <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800 shadow-sm transition-transform group-hover:scale-110">
                                <i class="bi bi-building text-base"></i>
                            </div>
                            <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $item->asignacion_persona->seccion_academica->escuela->name ?? 'Sin escuela' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-black text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $item->apellidos ?? 'Sin estudiante' }} {{ $item->nombres ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl border {{ $class_ficha }} font-bold text-xs transition-all hover:scale-105 active:scale-95"
                            @click="openMatriculaModal({
                                id_ap: {{ $item->asignacion_persona->id }},
                                type: 'ficha',
                                state: '{{ $estadoFicha }}',
                                people: '{{ $item->apellidos }} {{ $item->nombres }}',
                                tipo: 'Ficha'
                            })">
                            <i class="bi {{ $icon_ficha }}"></i>
                            <span class="hidden xl:inline">Ficha Matrícula</span>
                        </button>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl border {{ $class_record }} font-bold text-xs transition-all hover:scale-105 active:scale-95" 
                            @click="openMatriculaModal({
                                id_ap: {{ $item->asignacion_persona->id }},
                                type: 'record',
                                state: '{{ $estadoRecord }}',
                                people: '{{ $item->apellidos }} {{ $item->nombres }}',
                                tipo: 'Récord'
                            })">
                            <i class="bi {{ $icon_record }}"></i>
                            <span class="hidden xl:inline">Récord Académico</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- MODAL VALIDACIÓN TAILWIND (Nuevo Diseño) -->
    <div x-show="validateModalOpen"
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4"
        x-cloak>
        <x-backdrop-modal name="validateModalOpen" />
        <div x-show="validateModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border border-white/20 dark:border-slate-700">
                            <i class="bi bi-clipboard-data-fill text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none" x-text="'Validación de ' + requireData.tipo"></h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2" x-text="requireData.people">VELE</p>
                        </div>
                    </div>
                    <button @click="validateModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <template x-if="loading">
                    <div class="py-12 flex flex-col items-center justify-center gap-4 text-blue-500">
                        <div class="w-12 h-12 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] animate-pulse">Consultando Registros...</p>
                    </div>
                </template>
                <template x-if="!loading && !ldata">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 text-center">
                        <div class="text-slate-300 mb-2">
                            <i class="bi bi-file-earmark-x text-3xl"></i>
                        </div>
                        <h5 class="text-base font-bold text-slate-600 dark:text-slate-300 tracking-tight">Sin envío</h5>
                        <p class="text-xs text-slate-400 font-medium">El docente no ha enviado este archivo todavía.</p>
                    </div>
                </template>
                <template x-if="ldata">
                    <div>
                        <div class="dark:bg-slate-800 border dark:border-slate-800 rounded-xl p-4 text-center mb-4 shadow-sm"
                            :class="{ 
                                'bg-green-50 border-green-100': ldata.estado_archivo === 'Aprobado',
                                'bg-red-50 border-red-100': ldata.estado_archivo === 'Corregir',
                                'bg-yellow-50 border-yellow-100': ldata.estado_archivo === 'Enviado'
                            }">
                            <div class="mb-2"
                                :class="{
                                    'text-green-500': ldata.estado_archivo === 'Aprobado',
                                    'text-red-500': ldata.estado_archivo === 'Corregir',
                                    'text-yellow-500': ldata.estado_archivo === 'Enviado'
                                }">
                                <i class="text-3xl" :class="{
                                    'bi bi-check-circle-fill': ldata.estado_archivo === 'Aprobado',
                                    'bi bi-x-circle-fill': ldata.estado_archivo === 'Corregir',
                                    'bi bi-exclamation-circle-fill': ldata.estado_archivo === 'Enviado'
                                }"></i>
                            </div>
                            <h5 class="text-base font-bold tracking-tight"
                                :class="{
                                    'text-green-500': ldata.estado_archivo === 'Aprobado',
                                    'text-red-500': ldata.estado_archivo === 'Corregir',
                                    'text-yellow-500': ldata.estado_archivo === 'Enviado'
                                }"
                                x-text="ldata.estado_archivo">
                            </h5>
                            <p class="text-sm font-medium"
                                :class="{
                                    'text-green-600/80': ldata.estado_archivo === 'Aprobado',
                                    'text-red-600/80': ldata.estado_archivo === 'Corregir',
                                    'text-yellow-600/80': ldata.estado_archivo === 'Enviado'
                                }"
                                x-text="ldata.estado_archivo == 'Aprobado' ? 'Documento validado correctamente.' : (ldata.estado_archivo == 'Enviado' ? 'El estudiante espera su calificación.' : 'Se solicitaron correcciones.')"></p>
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-2">
                            <div class="md:col-span-12 flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                                    <i class="bi bi-paperclip"></i> Archivo
                                </label>
                                <div class="bg-white dark:bg-slate-800 border dark:border-slate-800 border-slate-200 p-2.5 rounded-xl flex justify-between items-center shadow-sm">
                                    <div class="flex items-center min-w-0 pr-4">
                                        <i class="bi bi-file-earmark-pdf text-xl me-2" :class="{
                                            'text-green-500': ldata.estado_archivo === 'Aprobado',
                                            'text-red-500': ldata.estado_archivo === 'Corregir',
                                            'text-yellow-500': ldata.estado_archivo === 'Enviado'
                                        }"></i>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">Archivo.pdf</span>
                                    </div>
                                    <a :href="ldata.ruta" target="_blank" class="px-3 py-1 border text-[10px] font-bold rounded-lg hover:text-white transition-all flex items-center gap-2 shrink-0 uppercase"
                                        :class="{
                                            'border-green-600 text-green-600 hover:bg-green-600': ldata.estado_archivo === 'Aprobado',
                                            'border-red-600 text-red-600 hover:bg-red-600': ldata.estado_archivo === 'Corregir',
                                            'border-yellow-600 text-yellow-600 hover:bg-yellow-600': ldata.estado_archivo === 'Enviado'
                                        }">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template x-if="ldata && ldata.estado_archivo === 'Enviado'">
                    <form id="formValidacionDocente" action="{{ route('actualizar.estado.archivo.mat') }}" method="POST" class="animate-fade-in">
                        @csrf
                        <input type="hidden" name="id" id="id" :value="ldata.id">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2 ml-1">
                                <i class="bi bi-gear-fill"></i> Dictamen
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="estado" value="Aprobado" class="hidden peer" checked>
                                    <div class="flex items-center justify-center gap-2 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs transition-all peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-md group-hover:border-emerald-300">
                                        <i class="bi bi-check-lg"></i> Aprobar
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="estado" value="Corregir" class="hidden peer">
                                    <div class="flex items-center justify-center gap-2 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs transition-all peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-md group-hover:border-rose-300">
                                        <i class="bi bi-exclamation-triangle"></i> Corregir
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-2 mt-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2 ml-1">
                                <i class="bi bi-chat-dots-fill "></i> Observaciones (Requerido para Corregir)
                            </label>
                            <textarea name="comentario" rows="3"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm font-medium text-slate-600 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-300"
                                placeholder="Detalle los motivos..."></textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="validateModalOpen = false"
                                class="px-5 py-1.5 bg-gray-400 text-slate-500 text-xs font-bold hover:text-slate-700 rounded-xl transition-colors uppercase tracking-widest">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-5 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs font-black rounded-xl hover:from-blue-700 hover:to-indigo-800 shadow-lg shadow-blue-500/30 transition-all active:scale-95 uppercase tracking-widest flex items-center gap-2">
                                <i class="bi bi-check-lg text-base"></i> Guardar
                            </button>
                        </div>
                    </form>
                </template>
                        <template x-if="!loading && hdata.length > 1">
                    <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-3">
                        <button @click="showHistory = !showHistory" type="button" class="flex items-center justify-between w-full text-left text-xs font-bold text-slate-500 uppercase tracking-wider hover:text-blue-600 transition-colors focus:outline-none">
                            <span class="flex items-center gap-2"><i class="bi bi-clock-history"></i> Historial de Envíos</span>
                            <i class="bi transition-transform duration-300" :class="showHistory ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                        </button>
                        <div x-show="showHistory" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-3 space-y-2 max-h-60 overflow-y-auto pr-1 custom-scrollbar">
                            <template x-for="(item, index) in hdata" :key="index">
                                <div x-show="index > 0" class="bg-slate-50 dark:bg-slate-800 p-2 rounded-xl border border-slate-100 dark:border-slate-700 flex justify-between items-center hover:bg-slate-50 transition-colors">
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
                                        <a :href="'/' + item.ruta" target="_blank" class="text-blue-600 hover:text-blue-800 text-[10px] font-bold bg-blue-50 px-2 py-1 rounded-lg transition-colors uppercase">
                                            <i class="bi bi-file-earmark-pdf"></i> Ver
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div 
        x-show="validarModalOpen" 
        class="fixed inset-0 z-[1060] flex items-center justify-center px-4 overflow-hidden" 
        x-cloak
        @keydown.escape.window="validarModalOpen = false">
        
        <div x-show="validarModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
            class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="validarModalOpen = false"></div>

        <!-- Modal Content -->
        <div 
            x-show="validarModalOpen" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6 shrink-0 shadow-lg relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border border-white/20">
                            <i class="bi bi-shield-check text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none" x-text="requireData.type">Validar Matrícula</h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Sistema de Gestión Académica</p>
                        </div>
                    </div>
                    <button @click="validarModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="p-8 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <!-- Loading State Placeholder -->
                <template x-if="loading">
                    <div class="py-12 flex flex-col items-center justify-center gap-4 text-blue-500">
                        <div class="w-12 h-12 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] animate-pulse">Consultando Registros...</p>
                    </div>
                </template>

                <!-- Not Available State Card -->
                <template x-if="!loading && currentData.length === 0">
                    <div class="p-8 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 flex flex-col items-center text-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-amber-800 dark:text-amber-400 font-black uppercase tracking-widest text-xs mb-2">Archivo No Disponible</h4>
                            <p class="text-amber-800/60 dark:text-amber-400/60 text-xs font-medium leading-relaxed px-4">El estudiante aún no ha cargado este documento o se encuentra pendiente de envío.</p>
                        </div>
                    </div>
                </template>

                <!-- Content when Loaded -->
                <template x-if="!loading">
                    <div class="space-y-6">
                        <!-- Approved State Card -->
                        <div x-show="currentData.estado_archivo === 'Aprobado'" class="p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 flex flex-col items-center text-center gap-4 transition-all">
                            <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                <i class="bi bi-check-lg text-3xl"></i>
                            </div>
                            <div>
                                <h4 class="text-emerald-800 dark:text-emerald-400 font-black uppercase tracking-widest text-xs mb-2">Documento Aprobado</h4>
                                <p class="text-emerald-800/60 dark:text-emerald-400/60 text-xs font-medium leading-relaxed">Este documento ya ha sido revisado y validado correctamente por el sistema.</p>
                            </div>
                            <a :href="currentData.ruta" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold text-xs hover:bg-emerald-500 hover:text-white transition-all group">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                Visualizar Archivo PDF
                                <i class="bi bi-arrow-up-right text-[10px] opacity-0 group-hover:opacity-100 transition-all"></i>
                            </a>
                        </div>

                        <!-- Form State (When pending or correcting) -->
                        <div x-show="currentData.estado_archivo === 'Enviado' || currentData.estado_archivo === 'Corregir'" class="space-y-6">
                            <!-- File Link -->
                            <div class="group">
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-3 ml-1">Documento Recibido</label>
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 flex items-center justify-between group-hover:border-blue-500/30 transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/20 text-red-600 flex items-center justify-center">
                                            <i class="bi bi-file-earmark-pdf text-xl"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate pr-2">Anexo_Estudiante_Matricula.pdf</p>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Documento PDF Oficial</p>
                                        </div>
                                    </div>
                                    <a :href="currentData.ruta" target="_blank" class="px-4 py-2 rounded-lg bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all">
                                        Ver
                                    </a>
                                </div>
                            </div>

                            <!-- Validation Form -->
                            <form action="{{ route('actualizar.estado.archivo.mat') }}" method="POST" class="space-y-6">
                                @csrf
                                <input type="hidden" name="id" id="id" :value="currentData.id">
                                
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-3 ml-1">Dictamen de Revisión</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="estado" value="Aprobado" class="peer sr-only">
                                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 dark:peer-checked:bg-emerald-500/10 transition-all flex flex-col items-center gap-2">
                                                <i class="bi bi-check-circle-fill text-slate-300 dark:text-slate-700 peer-checked:text-emerald-500 text-xl transition-colors"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-400">Aprobado</span>
                                            </div>
                                        </label>
                                        <label class="relative cursor-pointer group">
                                            <input type="radio" name="estado" value="Corregir" class="peer sr-only">
                                            <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 peer-checked:border-rose-500 peer-checked:bg-rose-50/50 dark:peer-checked:bg-rose-500/10 transition-all flex flex-col items-center gap-2">
                                                <i class="bi bi-x-circle-fill text-slate-300 dark:text-slate-700 peer-checked:text-rose-500 text-xl transition-colors"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 peer-checked:text-rose-700 dark:peer-checked:text-rose-400">Corregir</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div x-show="$el.closest('form').querySelector('input[name=estado]:checked')?.value === 'Corregir'">
                                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-3 ml-1">Observaciones</label>
                                    <textarea 
                                        name="comentario" 
                                        rows="4" 
                                        class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border-none text-slate-600 dark:text-slate-300 font-bold text-sm focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-600 outline-none"
                                        placeholder="Escriba aquí los detalles que el estudiante debe corregir..."></textarea>
                                </div>

                                <!-- Footer Actions within Form -->
                                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" @click="validarModalOpen = false" class="flex-1 px-6 py-4 rounded-2xl bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 font-black text-[11px] uppercase tracking-widest hover:text-slate-700 dark:hover:text-white transition-all border border-slate-200 dark:border-slate-700">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="flex-1 px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-black text-[11px] uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-500/20 hover:scale-[1.02] active:scale-95 transition-all">
                                        Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $('#tablaMatricula').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar...",
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
            $('#tablaMatricula').addClass('dt-ready');
        }
    });
</script>
@endpush
