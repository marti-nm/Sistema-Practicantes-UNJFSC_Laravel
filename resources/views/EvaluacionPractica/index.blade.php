@extends('template')
@section('title', 'Evaluación de Supervisión de Prácticas')
@section('subtitle', 'Panel de supervisión y seguimiento de estudiantes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    x-data="{
        superviseModalOpen: false,
        showHistory: false,
        loading: false,
        requireData: { id: null, anexo: null, student: null },
        ldata: null, // last data
        hdata: null, // history data

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
        },

        async fetchSupervises(id, anexo) {
            this.loading = true;
            this.showHistory = false;
            this.ldata = null;
            this.hdata = null;
            try {
                const ANEXO = 'anexo_' + anexo;
                const ID_MODULO = {{ $id_modulo }};
                const r = await fetch(`/api/evaluacion_practica/${id}/${ID_MODULO}/${ANEXO}`);
                const result = await r.json();
                const data = result.length > 0 ? result[0] : null;

                if (data) {
                    this.hdata = data;
                    if (data.evaluacion_archivo && data.evaluacion_archivo.length > 0) {
                        this.ldata = data.evaluacion_archivo[0];
                    }
                }
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        openSuperviseModal(id, anexo, student) {
            this.requireData.id = id;
            this.requireData.anexo = anexo;
            this.requireData.student = student;
            this.superviseModalOpen = true;
            this.fetchSupervises(id, anexo);
        },
    }">
    <x-header-content
        title="Evaluación de Prácticas"
        subtitle="Gestión académica oficial"
        icon="bi-clipboard-data-fill"
        :enableButton="false"
    />
    @if(Auth::user()->hasAnyRoles([1, 2]))
        <x-data-filter
            route="seguimiento.evaluation"
            :facultades="$facultades"
        />
    @endif

        <form method="GET" action="{{ route('seguimiento.evaluation') }}" class="relative z-10">
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

        <form id="form-modulo" method="GET" action="{{ route('seguimiento.evaluation') }}" class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 relative z-10">
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
        <div class="overflow-x-auto">
            <table id="tablaSupervision" class="w-full text-left border-collapse table-skeleton-ready">
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
                <tbody id="evaluation-table-body">
                    @foreach ($grupo_estudiante as $index => $item)
                    @php
                        $getStatusInfo = function ($state) {
                            $base = "w-full px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all active:scale-95 flex items-center justify-center gap-2 border-1 shadow-sm hover:shadow-md";

                            if (is_null($state)) return [
                                'classes' => "$base bg-slate-50 text-slate-400 border-slate-200 hover:bg-slate-100 hover:text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-500 dark:hover:text-slate-300",
                                'label' => 'Sin envío',
                                'icon' => 'bi-cloud-upload'
                            ];

                            // state 1: Enviado, 5: Aprobado, 2,3,4: Corregir
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
                            <div class="d-flex flex-column">
                                <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $item->asignacion_persona->persona->nombres }} {{ $item->asignacion_persona->persona->apellidos }}</span>
                                <small class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">Estado Gral:
                                    @if($item->state == 2)
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-lg text-xs font-bold uppercase tracking-wider">Aprobado</span>
                                    @else
                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold uppercase tracking-wider">En Proceso</span>
                                    @endif
                                </small>
                            </div>
                        </td>
                        <td>
                            <button class="{{ $status7['classes'] }}"
                                @click="openSuperviseModal({{ $item->id_ap }}, 7, '{{ $item->asignacion_persona->persona->nombres }} {{ $item->asignacion_persona->persona->apellidos }}')">
                                <i class="bi {{ $status7['icon'] }} text-sm"></i> <span>Anexo 7 ({{ $status7['label'] }})</span>
                            </button>
                        </td>
                        <td>
                            <button class="{{ $status8['classes'] }}"
                                @click="openSuperviseModal({{ $item->id_ap }}, 8, '{{ $item->asignacion_persona->persona->nombres }} {{ $item->asignacion_persona->persona->apellidos }}')">
                                <i class="bi {{ $status8['icon'] }} text-sm"></i> <span>Anexo 8 ({{ $status8['label'] }})</span>
                            </button>
                        </td>
                    </tr>
                    <!-- Modales para Anexo 7 y Anexo 8 -->

                    @endforeach
                </tbody>
            </table>
        </div>
    <div x-show="superviseModalOpen"
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4"
        x-cloak>
        <x-backdrop-modal name="superviseModalOpen" />

        <div x-show="superviseModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-slate-50 dark:bg-slate-900 rounded-[1.5rem] shadow-2xl w-full max-w-md overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-[#111c44] to-blue-900 px-6 py-4">
                 <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-clipboard-data-fill text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none">Calificar Estudiante</h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2" x-text="requireData.student"></p>
                        </div>
                    </div>
                    <button @click="superviseModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <template x-if="loading">
                    <div class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                    </div>
                </template>

                <!-- Estado 1: Enviado / Pendiente de Revisión -->
                <template x-if="!loading && ldata && ldata.state == 1">
                    <div>
                        <div class="bg-blue-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-blue-100 rounded-xl p-4 text-center mb-4 shadow-sm">
                            <div class="text-blue-500 mb-2">
                                <i class="bi bi-hourglass-split text-3xl"></i>
                            </div>
                            <h5 class="text-base font-bold text-blue-800 dark:text-blue-200 tracking-tight">Enviado para Revisión</h5>
                            <p class="text-sm text-blue-600/80 dark:text-blue-200 font-medium">Ya has enviado este anexo. El docente lo está revisando.</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-2">
                            <div class="md:col-span-8 flex flex-column gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                                    <i class="bi bi-paperclip"></i> Archivo enviado
                                </label>

                                <div class="bg-slate-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-slate-200 p-2.5 rounded-xl d-flex justify-content-between align-items-center shadow-sm hover:border-blue-300 transition-colors">
                                    <div class="flex items-center min-w-0 pr-4">
                                        <i class="bi bi-file-earmark-pdf text-red-500 text-xl me-2"></i>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">Anexo_7_Estudiante.pdf</span>
                                    </div>
                                    <a :href="ldata.archivos[0].ruta" target="_blank"
                                    class="px-3 py-1 border-1 border-blue-600 text-blue-600 text-[10px] font-bold rounded-lg hover:bg-blue-600 hover:text-white transition-all active:scale-95 flex items-center gap-2 shrink-0 uppercase">
                                        <i class="bi bi-box-arrow-up-right"></i> Ver
                                    </a>
                                </div>
                            </div>

                            <div class="md:col-span-4 flex flex-column gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                                    <i class="bi bi-clipboard-data"></i> Nota
                                </label>

                                <div class="bg-slate-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-slate-200 p-2.5 rounded-xl d-flex justify-content-center align-items-center shadow-sm min-h-[46px]">
                                    <span class="text-lg font-black text-blue-700 dark:text-green-500" x-text="ldata.nota">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Estado 5: Aprobado -->
                <template x-if="!loading && ldata && ldata.state == 5">
                    <div>
                        <div class="bg-green-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-green-100 rounded-xl p-4 text-center mb-4 shadow-sm">
                            <div class="text-green-500 mb-2">
                                <i class="bi bi-check-circle-fill text-3xl"></i>
                            </div>
                            <h5 class="text-base font-bold text-green-800 dark:text-green-200 tracking-tight">Aprobado</h5>
                            <p class="text-sm text-green-600/80 dark:text-green-200 font-medium">El docente ha aprobado este anexo. No se requieren más acciones.</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-2">
                            <div class="md:col-span-8 flex flex-column gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                                    <i class="bi bi-paperclip"></i> Archivo Aprobado
                                </label>
                                <div class="bg-slate-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-slate-200 p-2.5 rounded-xl d-flex justify-content-between align-items-center shadow-sm">
                                    <div class="flex items-center min-w-0 pr-4">
                                        <i class="bi bi-file-earmark-pdf text-green-500 text-xl me-2"></i>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">Anexo_Aprobado.pdf</span>
                                    </div>
                                    <a :href="ldata.archivos[0].ruta" target="_blank" class="px-3 py-1 border-1 border-green-600 text-green-600 text-[10px] font-bold rounded-lg hover:bg-green-600 hover:text-white transition-all flex items-center gap-2 shrink-0 uppercase">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </div>
                            </div>
                            <div class="md:col-span-4 flex flex-column gap-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2"><i class="bi bi-clipboard-data"></i> Nota Final</label>
                                <div class="bg-green-50 dark:bg-slate-800 border-1 dark:border-slate-800 border-green-200 p-2.5 rounded-xl d-flex justify-content-center align-items-center shadow-sm min-h-[46px]">
                                    <span class="text-lg font-black text-green-600 dark:text-green-400" x-text="ldata.nota"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Formulario: Nuevo (null), Corregir Archivo (2), Corregir Nota (3), Corregir Todo (4) -->
                <template x-if="!loading && (!ldata || [2, 3, 4].includes(ldata.state))">
                    <form id="submission-form"
                        action="{{ route('subir.anexo') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        x-data="{ hasFile: false, fileName: 'Nuevo_Anexo.pdf' }"
                        class="space-y-4">
                        @csrf

                        <input type="hidden" id="ap_id" name="ap_id" :value="requireData.id">
                        <input type="hidden" id="number" name="number" :value="requireData.anexo">
                        <input type="hidden" id="modulo" name="modulo" value="{{ $id_modulo_now }}">

                        <!-- Mensaje de Corrección si existe -->
                        <template x-if="ldata && [2, 3, 4].includes(ldata.state)">
                            <div class="bg-orange-50 border border-orange-100 rounded-xl p-3 mb-2">
                                <div class="flex items-start gap-3">
                                    <i class="bi bi-exclamation-triangle-fill text-orange-500 mt-0.5"></i>
                                    <div class="w-full">
                                        <div class="flex justify-between items-center mb-1">
                                            <h6 class="text-sm font-bold text-orange-800 m-0">Requiere Corrección</h6>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-orange-100 text-orange-700 border border-orange-200"
                                                  x-text="ldata.state == 2 ? 'Archivo' : (ldata.state == 3 ? 'Nota' : 'Todo')">
                                            </span>
                                        </div>
                                        <p class="text-xs text-orange-700 leading-tight" x-text="ldata.observacion || 'Por favor revise los campos indicados.'"></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Input de Archivo: Visible si es nuevo, o estado 2 (Corregir Archivo) o 4 (Corregir Todo) -->
                        <div x-show="!ldata || [2, 4].includes(ldata.state)" class="space-y-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2 ml-1">
                                <i class="bi bi-file-pdf-fill text-red-500"></i>
                                Subir Anexo (PDF)
                            </label>

                            <div class="relative group">
                                <input type="file"
                                    name="anexo"
                                    accept="application/pdf"
                                    onchange="validateFileSize(this, 10)"
                                    :required="!ldata || [2, 4].includes(ldata.state)"
                                    class="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-3
                                            file:rounded-xl file:border-0 file:text-xs file:font-black
                                            file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                            border-1 border-slate-200 dark:border-slate-800 rounded-2xl p-1 focus-within:border-blue-400 transition-all">
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium ml-1">Documento PDF • Tamaño máximo 10MB</p>
                        </div>

                        <!-- Archivo Existente: Visible solo si es estado 3 (Corregir Nota) -->
                        <div x-show="ldata && ldata.state == 3"
                            class="bg-slate-50 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl p-3">

                            <input type="hidden" name="rutaAnexo" :value="ldata ? ldata.archivos[0].ruta : ''">

                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-tight mb-2 block">
                                Archivo actualmente en el sistema:
                            </label>

                            <div class="flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-100 shadow-sm">
                                <div class="flex items-center min-w-0 pr-2">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center mr-3 shrink-0">
                                        <i class="bi bi-file-earmark-pdf-fill text-red-500"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700 truncate">Anexo_Enviado.pdf</span>
                                </div>
                                <a :href="ldata ? '/' + ldata.archivos[0].ruta : '#'" target="_blank"
                                class="flex items-center gap-2 text-[10px] font-bold text-blue-600 hover:text-blue-800 transition-colors shrink-0 px-3 py-1 bg-blue-50 rounded-lg uppercase">
                                    <i class="bi bi-box-arrow-up-right text-sm"></i>
                                    VER
                                </a>
                            </div>
                        </div>

                        <div class="bg-blue-50/50 dark:bg-slate-900 p-3 rounded-xl border-1 border-blue-100 dark:border-slate-800">
                            <label for="finalScore" class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-2 block ml-1">
                                <i class="bi bi-star-fill mr-1"></i> Nota Final (0-20)
                            </label>
                            <div class="relative">
                                <input type="number"
                                    name="nota"
                                    id="finalScore"
                                    min="0"
                                    max="20"
                                    placeholder="00"
                                    :value="ldata ? ldata.nota : ''"
                                    :readonly="ldata && ldata.state == 2"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xl font-black text-blue-800 dark:text-white focus:ring-2 focus:ring-blue-400 placeholder:text-blue-200 py-2 px-3 shadow-inner">
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-300 font-bold">
                                    / 20
                                </div>
                            </div>
                            <template x-if="ldata && ldata.state == 2">
                                <p class="text-[10px] text-blue-400 mt-2 ml-1">* La nota se mantiene, solo debe corregir el archivo.</p>
                            </template>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-2">
                            <button type="button"
                                    @click="superviseModalOpen = false"
                                    class="px-5 py-1.5 bg-gray-400 text-slate-500 text-xs font-bold hover:text-slate-700 rounded-xl transition-colors uppercase tracking-widest">
                                Cancelar
                            </button>

                            <button type="submit"
                                    id="saveEvaluation"
                                    class="px-5 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs font-black rounded-xl
                                        hover:from-blue-700 hover:to-indigo-800 shadow-lg shadow-blue-500/30
                                        transition-all active:scale-95 uppercase tracking-widest flex items-center gap-2">
                                <i class="bi bi-check-lg text-base"></i>
                                Subir
                            </button>
                        </div>
                    </form>
                </template>

                <!-- Historial Collapsible -->
                <template x-if="!loading && hdata && hdata.evaluacion_archivo && hdata.evaluacion_archivo.length > 1">
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

                            <template x-for="(item, index) in hdata.evaluacion_archivo" :key="index">
                                <!-- Ignorar el índice 0 si es el que se muestra arriba, o mostrar todos si se prefiere log completo.
                                     La lógica bootstrap saltaba el index 0 si state == 1. Aquí mostraremos del 1 en adelante para no duplicar el actual. -->
                                <div x-show="index > 0" class="bg-slate-50 dark:bg-slate-800 p-2 rounded-xl border-1 border-slate-100 dark:border-slate-700 flex justify-between items-center hover:bg-slate-50 transition-colors">
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
                                        <a :href="'/' + item.archivos[0].ruta" target="_blank" class="text-blue-600 hover:text-blue-800 text-[10px] font-bold bg-blue-50 px-2 py-1 rounded-lg transition-colors uppercase">
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
</div>

@endsection

@push('js')

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tablaSupervision').DataTable({
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
                $('#tablaSupervision').addClass('dt-ready');
            }
        });
    });
</script>
@endpush
