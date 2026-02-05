@extends('template')
@section('title', 'Revisión de Supervisión de Prácticas')
@section('subtitle', 'Panel de supervisión y seguimiento de estudiantes')

@section('content')
<div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        reviewModalOpen: false,
        showHistory: false,
        loading: false,
        requireData: { id: null, anexoNum: null, student: null },
        ldata: null, // last evaluation_archivo
        hdata: null, // full data from API

        // Form fields for revision
        selectedEstado: 'Aprobado',
        correccionTipo: '2',
        comentario: '',
        evalArchivoId: null,
        archivoId: null,
        anexoName: '',

        async fetchRevision(id, anexoNum) {
            this.loading = true;
            this.showHistory = false;
            this.ldata = null;
            this.hdata = null;
            this.selectedEstado = 'Aprobado';
            this.correccionTipo = '2';
            this.comentario = '';

            try {
                const ANEXO = 'anexo_' + anexoNum;
                const ID_MODULO = {{ $id_modulo }};
                const r = await fetch(`/api/evaluacion_practica/${id}/${ID_MODULO}/${ANEXO}`);
                const result = await r.json();
                const data = result.length > 0 ? result[0] : null;

                if (data) {
                    this.hdata = data;
                    if (data.evaluacion_archivo && data.evaluacion_archivo.length > 0) {
                        this.ldata = data.evaluacion_archivo[0];
                        this.evalArchivoId = this.ldata.id;
                        if (this.ldata.archivos && this.ldata.archivos.length > 0) {
                            this.archivoId = this.ldata.archivos[0].id;
                        }
                    }
                }
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        openReviewModal(id, anexoNum, student) {
            this.requireData.id = id;
            this.requireData.anexoNum = anexoNum;
            this.requireData.student = student;
            this.anexoName = 'anexo_' + anexoNum;
            this.reviewModalOpen = true;
            this.fetchRevision(id, anexoNum);
        },

        selectModule(moduleId, locked) {
            if (locked) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Módulo bloqueado',
                        text: 'No puedes avanzar a este módulo hasta que se habilite según la etapa actual.',
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false,
                    });
                } else {
                    alert('Módulo bloqueado. No puedes seleccionar este módulo.');
                }
                return;
            }
            document.getElementById('selected_modulo').value = moduleId;
            document.getElementById('form-modulo').submit();
        }
    }">

    <x-header-content
        title="Panel de Revisión de Evaluaciones"
        subtitle="Gestión de Supervisión y Seguimiento"
        icon="bi-clipboard-check-fill"
        :enableButton="false"
    />
    <div class="flex gap-4">
        <div class="md:col-span-5">
            <label for="grupo" class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">
                Seleccionar Grupo
            </label>
            <select class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none"
                id="grupo" name="grupo" onchange="this.form.submit()">
                <option value="">-- Seleccione un grupo --</option>
                @foreach ($grupos_practica as $gp)
                    <option value="{{ $gp->id }}" {{ $selected_grupo_id == $gp->id ? 'selected' : '' }}>
                        {{ $gp->seccion_academica->escuela->name }} - {{ $gp->seccion_academica->seccion }} : {{ $gp->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if(Auth::user()->hasAnyRoles([1, 2]))
        <x-data-filter
            route="seguimiento.revisar"
            :facultades="$facultades"
        />
    @endif

    <form method="GET" action="{{ route('seguimiento.revisar') }}" class="relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-5">
                <label for="grupo" class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">
                    Seleccionar Grupo
                </label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-collection-fill text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <select class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none"
                        id="grupo" name="grupo" onchange="this.form.submit()">
                        <option value="">-- Seleccione un grupo --</option>
                        @foreach ($grupos_practica as $gp)
                            <option value="{{ $gp->id }}" {{ $selected_grupo_id == $gp->id ? 'selected' : '' }}>
                                {{ $gp->seccion_academica->escuela->name }} - {{ $gp->seccion_academica->seccion }} : {{ $gp->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="bi bi-chevron-down text-xs text-slate-400"></i>
                    </div>
                </div>
            </div>
            <div class="md:col-span-7">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2 block ml-1">
                    Descripción Actual
                </label>
                <div class="bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl p-3 flex items-center gap-4 shadow-sm h-[48px]">
                    <div class="w-1 bg-gradient-to-b from-blue-400 to-indigo-500 h-full rounded-full"></div>
                    <div class="flex-1 min-w-0">
                         @if($selected_grupo_id)
                            <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 truncate">
                                <span class="font-medium">{{ $name_escuela }}</span>
                                <i class="bi bi-dot text-slate-300"></i>
                                <span class="font-medium">{{ $name_seccion }}</span>
                                <i class="bi bi-arrow-right-short text-slate-300"></i>
                                <span class="font-black text-blue-600 dark:text-blue-400">{{ $name_grupo }}</span>
                            </div>
                        @else
                            <span class="text-sm text-slate-400 font-medium italic flex items-center gap-2">
                                <i class="bi bi-info-circle"></i> Seleccione un grupo para ver detalles
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="form-modulo" method="GET" action="{{ route('seguimiento.revisar') }}" class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 relative z-10">
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

    @include('components.skeletonLoader-table')

    <div class="overflow-x-auto mt-8">
        <table id="tablaRevision" class="w-full text-left border-collapse table-skeleton-ready">
            <thead>
                <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">ID</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Facultad</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Escuela</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Estudiante</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Anexo 7</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Anexo 8</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grupo_estudiante as $index => $item)
                @php
                    $getStatusInfo = function ($state) {
                        $base = "w-full px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all active:scale-95 flex items-center justify-center gap-2 border-1 shadow-sm hover:shadow-md";

                        if (is_null($state)) return [
                            'classes' => "$base bg-slate-50 text-slate-400 border-slate-200 hover:bg-slate-100 hover:text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-500 dark:hover:text-slate-300",
                            'label' => 'Sin envío',
                            'icon' => 'bi-cloud-upload'
                        ];

                        switch ($state) {
                            case 5: return ['classes' => "$base bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-100 hover:text-emerald-700 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400", 'label' => 'Aprobado', 'icon' => 'bi-check-circle-fill'];
                            case 1: return ['classes' => "$base bg-amber-50 text-amber-600 border-amber-200 hover:bg-amber-100 hover:text-amber-700 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400", 'label' => 'Revisar', 'icon' => 'bi-hourglass-split'];
                            case 2:
                            case 3:
                            case 4: return ['classes' => "$base bg-rose-50 text-rose-600 border-rose-200 hover:bg-rose-100 hover:text-rose-700 dark:bg-rose-900/20 dark:border-rose-800 dark:text-rose-400", 'label' => 'Corregir', 'icon' => 'bi-exclamation-triangle-fill'];
                            default: return ['classes' => "$base bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100 hover:text-slate-700", 'label' => 'Pendiente', 'icon' => 'bi-dash-circle'];
                        }
                    };

                    $status7 = $getStatusInfo($item->status_anexo_7);
                    $status8 = $getStatusInfo($item->status_anexo_8);
                @endphp
                <tr>
                    <td><span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span></td>
                    <td><span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $item->asignacion_persona->seccion_academica->facultad->name }}</span></td>
                    <td><span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $item->asignacion_persona->seccion_academica->escuela->name }}</span></td>
                    <td>
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $item->asignacion_persona->persona->nombres }} {{ $item->asignacion_persona->persona->apellidos }}</span>
                            <small class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">
                                @if($item->state == 2)
                                    <span class="text-emerald-500">Aprobado</span>
                                @else
                                    <span class="text-blue-500">En Proceso</span>
                                @endif
                            </small>
                        </div>
                    </td>
                    <td>
                        <button class="{{ $status7['classes'] }}"
                            @click="openReviewModal({{ $item->id_ap }}, 7, '{{ $item->asignacion_persona->persona->nombres }} {{ $item->asignacion_persona->persona->apellidos }}')">
                            <i class="bi {{ $status7['icon'] }} text-sm"></i> <span>Anexo 7 ({{ $status7['label'] }})</span>
                        </button>
                    </td>
                    <td>
                        <button class="{{ $status8['classes'] }}"
                            @click="openReviewModal({{ $item->id_ap }}, 8, '{{ $item->asignacion_persona->persona->nombres }} {{ $item->asignacion_persona->persona->apellidos }}')">
                            <i class="bi {{ $status8['icon'] }} text-sm"></i> <span>Anexo 8 ({{ $status8['label'] }})</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal de Revisión (Alpine JS) -->
    <div x-show="reviewModalOpen"
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4"
        x-cloak>
        <div x-show="reviewModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="reviewModalOpen = false"></div>

        <div x-show="reviewModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-slate-50 dark:bg-slate-900 rounded-[1.5rem] shadow-2xl w-full max-w-md overflow-hidden border-1 border-slate-100 dark:border-slate-800">

            <!-- Header -->
            <div class="bg-gradient-to-r from-[#111c44] to-blue-900 px-6 py-4">
                 <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20">
                            <i class="bi bi-clipboard-check-fill text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none">Revisar Documento</h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2" x-text="requireData.student"></p>
                        </div>
                    </div>
                    <button @click="reviewModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-4">
                <!-- Loading State -->
                <template x-if="loading">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                </template>

                <div x-show="!loading">
                    <!-- Case: No file sent yet -->
                    <template x-if="!ldata">
                        <div class="bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl p-4 text-center">
                            <div class="text-slate-300 mb-2">
                                <i class="bi bi-file-earmark-x text-3xl"></i>
                            </div>
                            <h5 class="text-base font-bold text-slate-600 dark:text-slate-300 tracking-tight">Sin envío</h5>
                            <p class="text-xs text-slate-400 font-medium">El estudiante no ha enviado este anexo todavía.</p>
                        </div>
                    </template>

                    <!-- Case: File exists -->
                    <template x-if="ldata">
                        <div>
                            <!-- Status Banner -->
                            <div class="rounded-xl p-4 text-center mb-4 shadow-sm border-1"
                                :class="{
                                    'bg-green-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-green-100 text-emerald-700': ldata.state == 5,
                                    'bg-amber-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-amber-100 text-amber-700': ldata.state == 1,
                                    'bg-rose-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-rose-100 text-rose-700': [2,3,4].includes(ldata.state)
                                }">
                                <div class="mb-2" :class="{
                                    'text-emerald-500': ldata.state == 5,
                                    'text-amber-500': ldata.state == 1,
                                    'text-rose-500': [2,3,4].includes(ldata.state)
                                }">
                                    <i class="text-3xl bi" :class="{
                                        'bi-check-circle-fill': ldata.state == 5,
                                        'bi-hourglass-split': ldata.state == 1,
                                        'bi-exclamation-triangle-fill': [2,3,4].includes(ldata.state)
                                    }"></i>
                                </div>
                                <h5 class="text-base font-bold tracking-tight"
                                    :class="{
                                        'text-green-800 dark:text-green-500': ldata.state == 5,
                                        'text-amber-800 dark:text-amber-500': ldata.state == 1,
                                        'text-rose-800 dark:text-rose-500': [2,3,4].includes(ldata.state)
                                    }"
                                    x-text="ldata.state == 5 ? 'Aprobado' : (ldata.state == 1 ? 'Pendiente de Revisión' : 'En Corrección')"></h5>
                                <p class="text-sm font-medium"
                                   :class="{
                                        'text-green-600/80 dark:text-green-200': ldata.state == 5,
                                        'text-amber-600/80 dark:text-amber-200': ldata.state == 1,
                                        'text-rose-600/80 dark:text-rose-200': [2,3,4].includes(ldata.state)
                                   }"
                                   x-text="ldata.state == 5 ? 'Documento validado correctamente.' : (ldata.state == 1 ? 'El estudiante espera su calificación.' : 'Se solicitaron correcciones.')"></p>
                            </div>

                            <!-- File & Score Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-2">
                                <div class="md:col-span-8 flex flex-column gap-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                                        <i class="bi bi-paperclip"></i> Archivo Enviado
                                    </label>
                                    <div class="bg-slate-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-slate-200 p-2.5 rounded-xl d-flex justify-content-between align-items-center shadow-sm">
                                        <div class="flex items-center min-w-0 pr-4">
                                            <i class="bi bi-file-earmark-pdf text-xl me-2"
                                                :class="{
                                                    'text-green-500': ldata.state == 5,
                                                    'text-amber-500': ldata.state == 1,
                                                    'text-rose-500': [2,3,4].includes(ldata.state)
                                                }"></i>
                                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">Anexo_<span x-text="requireData.anexoNum"></span>.pdf</span>
                                        </div>
                                        <a :href="ldata.archivos[0] ? '/' + ldata.archivos[0].ruta : '#'" target="_blank"
                                        class="px-3 py-1 border-1 text-[10px] font-bold rounded-lg hover:text-white transition-all active:scale-95 flex items-center gap-2 shrink-0 uppercase"
                                        :class="{
                                            'border-green-600 text-green-600 hover:bg-green-600 hover:text-white': ldata.state == 5,
                                            'border-amber-600 text-amber-600 hover:bg-amber-600 hover:text-white': ldata.state == 1,
                                            'border-rose-600 text-rose-600 hover:bg-rose-600 hover:text-white': [2,3,4].includes(ldata.state)
                                        }">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </div>
                                </div>
                                <div class="md:col-span-4 flex flex-column gap-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                                        <i class="bi bi-clipboard-data"></i> Nota
                                    </label>
                                    <div class="bg-slate-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-slate-200 p-2.5 rounded-xl d-flex justify-content-center align-items-center shadow-sm min-h-[46px]">
                                        <span class="text-lg font-black" :class="{
                                            'text-green-600': ldata.state == 5,
                                            'text-amber-600': ldata.state == 1,
                                            'text-rose-600': [2,3,4].includes(ldata.state)
                                        }" x-text="ldata.nota || '--'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Form for Revision (Only if state is 1)-->
                            <template x-if="ldata.state == 1">
                                <form action="{{ route('actualizar.anexo') }}" method="POST" class="space-y-4 mt-4">
                                    @csrf
                                    <input type="hidden" name="ap_id" :value="requireData.id">
                                    <input type="hidden" name="evaluacion" :value="evalArchivoId">
                                    <input type="hidden" name="archivo" :value="archivoId">
                                    <input type="hidden" name="anexo" :value="anexoName">

                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2 ml-1">
                                            <i class="bi bi-gear-fill"></i> Dictamen
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="estado" value="Aprobado" x-model="selectedEstado" class="hidden peer">
                                                <div class="flex items-center justify-center gap-2 py-2 rounded-xl border-1 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs transition-all peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-md group-hover:border-emerald-300">
                                                    <i class="bi bi-check-lg"></i> Aprobar
                                                </div>
                                            </label>
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="estado" value="Corregir" x-model="selectedEstado" class="hidden peer">
                                                <div class="flex items-center justify-center gap-2 py-2 rounded-xl border-1 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs transition-all peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-md group-hover:border-rose-300">
                                                    <i class="bi bi-exclamation-triangle"></i> Corregir
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Correction Type Options -->
                                    <div x-show="selectedEstado == 'Corregir'"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Tipo de Corrección</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="correccionTipo" value="2" x-model="correccionTipo" class="hidden peer">
                                                <div class="flex flex-col items-center justify-center gap-1 p-2 rounded-xl border-1 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 transition-all peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-400 dark:peer-checked:bg-blue-900/20 group-hover:border-blue-300">
                                                    <i class="bi bi-file-earmark-pdf text-lg"></i>
                                                    <span class="text-[10px] font-black uppercase">Archivo</span>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="correccionTipo" value="3" x-model="correccionTipo" class="hidden peer">
                                                <div class="flex flex-col items-center justify-center gap-1 p-2 rounded-xl border-1 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 transition-all peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-400 dark:peer-checked:bg-blue-900/20 group-hover:border-blue-300">
                                                    <i class="bi bi-123 text-lg"></i>
                                                    <span class="text-[10px] font-black uppercase">Nota</span>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer group">
                                                <input type="radio" name="correccionTipo" value="4" x-model="correccionTipo" class="hidden peer">
                                                <div class="flex flex-col items-center justify-center gap-1 p-2 rounded-xl border-1 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-400 transition-all peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-400 dark:peer-checked:bg-blue-900/20 group-hover:border-blue-300">
                                                    <div class="flex gap-1">
                                                        <i class="bi bi-file-earmark-pdf text-xs"></i>
                                                        <i class="bi bi-plus text-[10px]"></i>
                                                        <span class="text-[10px] font-black uppercase">20/20</span>
                                                    </div>
                                                    <span class="text-[10px] font-black uppercase">Ambos</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2 ml-1">
                                            <i class="bi bi-chat-dots-fill "></i> Observaciones (Opcional)
                                        </label>
                                        <textarea name="comentario" x-model="comentario" rows="3"
                                            class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm font-medium text-slate-600 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-300"
                                            placeholder="Detalle los motivos..."></textarea>
                                    </div>

                                    <div class="flex items-center justify-end gap-3 pt-2">
                                        <button type="button" @click="reviewModalOpen = false"
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

                            <!-- History Section (Copy from EvaluacionPractica logic but for Reviewer) -->
                            <template x-if="hdata && hdata.evaluacion_archivo && hdata.evaluacion_archivo.length > (ldata.state == 1 ? 1 : 0)">
                                <div class="mt-4 border-t border-slate-100 dark:border-slate-800 pt-3">
                                    <button @click="showHistory = !showHistory" type="button"
                                        class="flex items-center justify-between w-full text-left text-xs font-bold text-slate-500 uppercase tracking-wider hover:text-blue-600 transition-colors focus:outline-none">
                                        <span class="flex items-center gap-2"><i class="bi bi-clock-history"></i> Historial de Envíos</span>
                                        <i class="bi transition-transform duration-300" :class="showHistory ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                    </button>

                                    <div x-show="showHistory"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="mt-3 space-y-2 max-h-60 overflow-y-auto pr-1 custom-scrollbar">

                                        <template x-for="(item, index) in hdata.evaluacion_archivo" :key="index">
                                            <!-- If current is pending (state 1), skip index 0 in history because it's the one being reviewed above -->
                                            <div x-show="(ldata.state == 1 && index > 0) || (ldata.state != 1 && index >= 0)"
                                                 class="bg-slate-50 dark:bg-slate-800 p-2 rounded-xl border-1 border-slate-100 dark:border-slate-700 flex justify-between items-center hover:bg-slate-50 transition-colors">
                                                <div>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="'Nota: ' + item.nota"></span>
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase"
                                                                :class="{
                                                                'bg-green-100 text-green-700': item.state == 5,
                                                                'bg-red-100 text-red-700': [2,3,4].includes(item.state),
                                                                'bg-blue-100 text-blue-700': item.state == 1
                                                                }"
                                                                x-text="item.state == 5 ? 'Aprobado' : ([2,3,4].includes(item.state) ? 'Observado' : 'Enviado')">
                                                        </span>
                                                        <template x-if="[2,3,4].includes(item.state)">
                                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-orange-100 text-orange-700 border border-orange-200"
                                                                    x-text="item.state == 2 ? 'Archivo' : (item.state == 3 ? 'Nota' : 'Todo')"></span>
                                                        </template>
                                                    </div>
                                                    <div class="text-[10px] text-slate-400 flex items-center gap-1">
                                                        <i class="bi bi-calendar3"></i>
                                                        <span x-text="item.archivos && item.archivos.length > 0 ? new Date(item.archivos[0].created_at).toLocaleString() : 'Sin fecha'"></span>
                                                    </div>
                                                </div>
                                                <template x-if="item.archivos && item.archivos.length > 0">
                                                    <a :href="'/' + item.archivos[0].ruta" target="_blank"
                                                        class="text-blue-600 hover:text-blue-800 text-[10px] font-bold bg-blue-50 px-2 py-1 rounded-lg transition-colors uppercase">
                                                        <i class="bi bi-file-earmark-pdf"></i> Ver
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
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
        $('#tablaRevision').DataTable({
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
                $('#skeletonLoader').addClass('hidden');
                $('#tablaRevision').addClass('dt-ready');
            }
        });
    });
</script>
@endpush
