@extends('template')
@section('title', 'Acreditación del Docente')
@section('subtitle', 'Gestionar acreditación del oficial')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" 
    x-data="{ 
        accreditModalOpen: false,
        showHistory: false,
        loading: false,
        requireData: { id: null, type:null, people:null, tipo:null },
        ldata: null,
        hdata: null,

        async fetchAccredit(id, type) {
            this.loading = true;
            this.showHistory = false;
            this.ldata = null;
            this.hdata = null;

            if(!id) {
                this.ldata == null;
                this.hdata == null;
            }

            try {
                const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));
                await sleep(1000);
                const r = await fetch(`/api/acreditacion/archivos/${id}/${type}`);
                const result = await r.json();
                if(result && result.length > 0) {
                    this.hdata = result;
                    this.ldata = result[0];
                }
                console.log('Data alpine: ', result);
                console.log('Data to ldata: ', this.hdata);
            } finally { this.loading = false; }
        },

        openAccreditModal(data) {
            this.requireData = data;
            console.log(this.requireData);
            this.accreditModalOpen = true;
            this.fetchAccredit(data.id, data.type);
        },

        showModal: false 
    }">

    <x-header-content
        title="Lista de Docentes para Acreditación"
        subtitle="Gestionar y validar documentos académicos del docente"
        icon="bi-patch-check-fill"
        :enableButton="true"
        msj="Registrar Docente"
        icon_msj="bi-mortarboard-fill"
        route="registrar"
    />

    @if(auth()->user()->getRolId() == 1)
        <x-data-filter
        route="docente"
        :facultades="$facultades"
        />
    @endif

    <!-- Skeleton Loader - Simple gray silhouettes -->
    @include('components.skeletonLoader-table')
    <div class="overflow-x-auto">
        <table id="tablaValidacion" class="w-full text-left border-separate border-spacing-0 table-skeleton-ready rounded-t-2xl overflow-hidden">
            <thead>
                <tr class="bg-gradient-to-r from-blue-800 to-blue-600 text-white">
                    <th class="px-6 py-5 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">ID</th>
                    <th class="px-6 py-5 text-left text-[11px] font-black uppercase tracking-[0.15em] border-none">Docente</th>
                    <th class="px-6 py-5 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Semestre</th>
                    <th class="px-6 py-5 text-left text-[11px] font-black uppercase tracking-[0.15em] border-none">Escuela</th>
                    <th class="px-6 py-5 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">C. Lectiva</th>
                    <th class="px-6 py-5 text-center text-[11px] font-black uppercase tracking-[0.15em] {{ $option == 2 ? 'last:rounded-tr-2xl' : '' }} border-none">Horario</th>
                    @if($option == 2)
                    <th class="px-6 py-5 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Resolución</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900/50">
                @foreach ($usuarios as $index => $item)
                    @php
                        $acreditacion = $item->asignacion_persona->acreditacion->first();
                        $archivosPorTipo = $acreditacion ? $acreditacion->archivos->groupBy('tipo') : collect();

                        $getLatest = function ($tipo) use ($archivosPorTipo) {
                            $history = $archivosPorTipo->get($tipo);
                            return $history ? $history->sortByDesc('created_at')->first() : null;
                        };

                        // Mapping for Tailwind colors based on status
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

                        $latestCL = $getLatest('carga_lectiva');
                        $estadoCL = $latestCL ? $latestCL->estado_archivo : 'Falta';
                        $class_cl = $getTailwindClass($estadoCL);
                        $icon_cl = $estadoCL == 'Aprobado' ? 'bi-check-circle-fill' : ($estadoCL == 'Corregir' ? 'bi-x-circle-fill' : ($estadoCL == 'Enviado' ? 'bi-file-earmark-text-fill' : 'bi-dash-circle'));

                        $latestHorario = $getLatest('horario');
                        $estadoHorario = $latestHorario ? $latestHorario->estado_archivo : 'Falta';
                        $class_horario = $getTailwindClass($estadoHorario);
                        $icon_horario = $estadoHorario == 'Aprobado' ? 'bi-check-circle-fill' : ($estadoHorario == 'Corregir' ? 'bi-x-circle-fill' : ($estadoHorario == 'Enviado' ? 'bi-file-earmark-text-fill' : 'bi-dash-circle'));

                        $class_resolucion = 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700 opacity-70';
                        $icon_resolucion = 'bi-dash-circle';
                        
                        if ($item->asignacion_persona->id_rol == 4) {
                            $latestResolucion = $getLatest('resolucion');
                            $estadoResolucion = $latestResolucion ? $latestResolucion->estado_archivo : 'Falta';
                            $class_resolucion = $getTailwindClass($estadoResolucion);
                            $icon_resolucion = $estadoResolucion == 'Aprobado' ? 'bi-check-circle-fill' : ($estadoResolucion == 'Corregir' ? 'bi-x-circle-fill' : ($estadoResolucion == 'Enviado' ? 'bi-file-earmark-text-fill' : 'bi-dash-circle'));
                        }
                    @endphp
                    <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ $index+1 }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 border-1 border-slate-200 dark:border-slate-700 font-black text-xs">
                                    {{ substr($item->nombres, 0, 1) }}{{ substr($item->apellidos, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-800 dark:text-slate-200 leading-tight">{{ $item->apellidos }}</div>
                                    <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $item->nombres }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-wider">
                                {{ $item->asignacion_persona->semestre->codigo }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $item->asignacion_persona->seccion_academica->escuela->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl border-1 {{ $class_cl }} font-bold text-xs transition-all hover:scale-105 active:scale-95"
                                @click="openAccreditModal({ id: {{ $acreditacion->id ?? 'null' }}, type: 'carga_lectiva', people: '{{ $item->apellidos.' '.$item->nombres }}', tipo: 'Carga Lectiva' })">
                                <i class="bi {{ $icon_cl }}"></i>
                                <span class="hidden xl:inline">C. Lectiva</span>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl border-1 {{ $class_horario }} font-bold text-xs transition-all hover:scale-105 active:scale-95"
                                    @click="openAccreditModal({ id: {{ $acreditacion->id ?? 'null' }}, type: 'horario', people: '{{ $item->apellidos.' '.$item->nombres }}', tipo: 'Horario' })">
                                <i class="bi {{ $icon_horario }}"></i>
                                <span class="hidden xl:inline">Horario</span>
                            </button>
                        </td>
                        @if($item->asignacion_persona->id_rol == 4)
                        <td class="px-6 py-4 text-center">
                            <button class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl border-1 {{ $class_resolucion }} font-bold text-xs transition-all hover:scale-105 active:scale-95"
                                    @click="openAccreditModal({ id: {{ $acreditacion->id ?? 'null' }}, type: 'resolucion', people: '{{ $item->apellidos.' '.$item->nombres }}', tipo: 'Resolución' })">
                                <i class="bi {{ $icon_resolucion }}"></i>
                                <span class="hidden xl:inline">Resolución</span>
                            </button>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="accreditModalOpen"
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4"
        x-cloak>
        <x-backdrop-modal name="accreditModalOpen" />

        <div x-show="accreditModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-clipboard-data-fill text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none" x-text="'Validación de ' + requireData.tipo"></h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2" x-text="requireData.people">VELE</p>
                        </div>
                    </div>
                    <button @click="accreditModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
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
                    <div class="bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl p-4 text-center">
                        <div class="text-slate-300 mb-2">
                            <i class="bi bi-file-earmark-x text-3xl"></i>
                        </div>
                        <h5 class="text-base font-bold text-slate-600 dark:text-slate-300 tracking-tight">Sin envío</h5>
                        <p class="text-xs text-slate-400 font-medium">El docente no ha enviado este archivo todavía.</p>
                    </div>
                </template>
                <template x-if="ldata">
                    <div>
                        <div class="dark:bg-slate-800 border-1 dark:border-slate-800 rounded-xl p-4 text-center mb-4 shadow-sm"
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
                                <div class="bg-white dark:bg-slate-800 border-1 dark:border-slate-800 border-slate-200 p-2.5 rounded-xl flex justify-between items-center shadow-sm">
                                    <div class="flex items-center min-w-0 pr-4">
                                        <i class="bi bi-file-earmark-pdf text-xl me-2" :class="{
                                            'text-green-500': ldata.estado_archivo === 'Aprobado',
                                            'text-red-500': ldata.estado_archivo === 'Corregir',
                                            'text-yellow-500': ldata.estado_archivo === 'Enviado'
                                        }"></i>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">Archivo.pdf</span>
                                    </div>
                                    <a :href="ldata.ruta" target="_blank" class="px-3 py-1 border-1 text-[10px] font-bold rounded-lg hover:text-white transition-all flex items-center gap-2 shrink-0 uppercase"
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
                    <form id="formValidacionDocente" action="{{ route('actualizar.estado.archivo') }}" method="POST" class="animate-fade-in">
                        @csrf
                        <input type="hidden" name="id" id="id" :value="ldata.id">
                        <input type="hidden" name="tipo" id="tipo" :value="ldata.tipo">
                        <input type="hidden" name="acreditacion" id="acreditacion" :value="ldata.archivo_id">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2 ml-1">
                                <i class="bi bi-gear-fill"></i> Dictamen
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="estado" value="Aprobado" class="hidden peer" checked>
                                    <div class="flex items-center justify-center gap-2 py-2 rounded-xl border-1 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs transition-all peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-md group-hover:border-emerald-300">
                                        <i class="bi bi-check-lg"></i> Aprobar
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="estado" value="Corregir" class="hidden peer">
                                    <div class="flex items-center justify-center gap-2 py-2 rounded-xl border-1 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-500 font-bold text-xs transition-all peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-md group-hover:border-rose-300">
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
                                class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm font-medium text-slate-600 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-300"
                                placeholder="Detalle los motivos..."></textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="accreditModalOpen = false"
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
                <!-- Historial Collapsible -->
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
                                <div x-show="index > 0" class="bg-slate-50 dark:bg-slate-800 p-2 rounded-xl border-1 border-slate-100 dark:border-slate-700 flex justify-between items-center hover:bg-slate-50 transition-colors">
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
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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

<script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#tablaValidacion').DataTable({
            language: {
                "lengthMenu": "Mostrar _MENU_",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "",
                "searchPlaceholder": "Buscar docente...",
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
                $('#tablaValidacion').addClass('dt-ready');
            }
        });
    });
</script>
@endpush