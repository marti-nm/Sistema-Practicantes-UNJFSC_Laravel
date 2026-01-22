@extends('template')
@section('title', 'Lista de Estudiantes')
@section('subtitle', 'Supervisión de practicantes asignados')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
    detailModalOpen: false,
    selectedStudent: null
}">
    <x-header-content
        title="Estudiantes del Grupo"
        subtitle="{{ $grupo?->name ?? 'Sin Grupo Asignado' }}"
        icon="bi-people-fill"
        :enableButton="false"
    />

    <!-- Table Card -->
    <div class="">
        <!-- skeleton loader -->
        @include('components.skeletonLoader-table')

        <div class="overflow-x-auto">
            <table id="tablaGrupoEstudiantes" class="w-full text-left border-collapse table-skeleton-ready">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">Código</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Estudiante</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Empresa</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Etapa Actuall</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900/50">
                    @foreach($grupo_estudiante as $estudiante)
                    <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 tracking-widest">{{ $estudiante->codigo }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($estudiante->ruta_foto)
                                    <img src="{{ asset($estudiante->ruta_foto) }}" class="w-10 h-10 rounded-xl object-cover shadow-sm ring-1 ring-slate-100 dark:ring-slate-700 transition-transform group-hover:scale-110">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800 shadow-sm transition-transform group-hover:scale-110">
                                        <i class="bi bi-person-fill text-base"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-black text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $estudiante->nombres }} {{ $estudiante->apellidos }}
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">Practicante</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $estudiante->empresa }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $badgeClass = match($estudiante->state_practica) {
                                    1 => 'bg-slate-100 text-slate-600 border-slate-200',
                                    2 => 'bg-blue-50 text-blue-600 border-blue-100',
                                    3 => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                    4 => 'bg-amber-50 text-amber-600 border-amber-100',
                                    5 => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                                    6 => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    7 => 'bg-rose-50 text-rose-600 border-rose-100',
                                    default => 'bg-slate-50 text-slate-400 border-slate-100'
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badgeClass }}">
                                {{ $estudiante->etapa }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="selectedStudent = {{ json_encode($estudiante) }}; detailModalOpen = true" 
                                        class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 border border-blue-100 dark:border-blue-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5" 
                                        title="Ver Detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detalles (Placeholder por ahora) -->
    <template x-if="detailModalOpen">
        <div class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
            <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="detailModalOpen = false"></div>
            
            <div x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
                <div class="bg-gradient-to-r from-blue-900 to-indigo-900 px-8 py-6">
                    <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                        <i class="bi bi-info-circle-fill"></i>
                        Detalles del Estudiante
                    </h3>
                </div>
                
                <div class="p-8">
                    <div class="flex items-center gap-6 mb-8 pb-8 border-b border-slate-100 dark:border-slate-800">
                        <template x-if="selectedStudent.ruta_foto">
                            <img :src="'/' + selectedStudent.ruta_foto" class="w-20 h-20 rounded-2xl object-cover shadow-lg ring-4 ring-blue-50 dark:ring-blue-900/20">
                        </template>
                        <template x-if="!selectedStudent.ruta_foto">
                            <div class="w-20 h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-3xl font-black shadow-inner border border-indigo-100 dark:border-indigo-800">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </template>
                        <div>
                            <h4 class="text-xl font-black text-slate-800 dark:text-white leading-tight" x-text="selectedStudent.nombres + ' ' + selectedStudent.apellidos"></h4>
                            <p class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1" x-text="'CÓDIGO: ' + selectedStudent.codigo"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Empresa / Institución</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="selectedStudent.empresa"></p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1">Etapa del Proceso</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="selectedStudent.etapa"></p>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-10">
                        <button type="button" @click="detailModalOpen = false" class="w-full px-6 py-4 bg-[#111c44] text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-800 shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                            Cerrar Detalles
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaGrupoEstudiantes').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar estudiante...",
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
            $('#tablaGrupoEstudiantes').addClass('dt-ready');
        }
    });
});
</script>
@endpush