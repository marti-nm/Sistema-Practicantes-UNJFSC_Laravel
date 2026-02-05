@extends('template')
@section('title', 'Supervisión de Prácticas')
@section('subtitle', 'Monitorear y gestionar el proceso de prácticas preprofesionales')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-xl shadow-blue-500/20">
                <i class="bi bi-mortarboard-fill text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Supervisión de Estudiantes en Prácticas</h2>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">Gestión académica oficial de supervisión de prácticas</p>
            </div>
        </div>
    </div>
    @if(Auth::user()->hasAnyRoles([1, 2]))
        <x-data-filter
            route="seguimiento.ppp"
            :facultades="$facultades"
        />
    @endif
    @include('components.skeletonLoader-table')
    <div class="overflow-x-auto">
        <table id="tablaPracticas" class="w-full text-left border-collapse table-skeleton-ready rounded-t-2xl overflow-hidden">
            <thead>
                <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">#</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Escuela</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Sección</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Tipo de Práctica</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Apellidos y Nombres</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Área</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($personas as $index => $persona)
                    @php
                        // 1. Usa optional() para manejar posibles valores null en asignacion_persona
                        // 2. Usa last() para obtener el último MODELO de la colección 'practicas'
                        $practica = optional($persona->asignacion_persona)->practicas->last();
                    @endphp
                <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $persona->asignacion_persona->seccion_academica->escuela->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $persona->asignacion_persona->seccion_academica->seccion }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($practica)
                            <span class="bg-cyan-600 text-white px-2 py-1 rounded text-xs uppercase">{{ $practica->tipo_practica }}</span>
                        @else
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">Sin asignar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ strtoupper($persona->apellidos . ' ' . $persona->nombres) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($practica && $practica->jefeInmediato)
                            <span class="bg-green-600 text-white px-2 py-1 rounded text-xs uppercase">{{ $practica->jefeInmediato->area ?: 'Área no reg.' }}</span>
                        @else
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">Sin asignar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($practica)
                        <a href="{{ route('supervision.detalle', $practica->id) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md shadow-cyan-600/20">
                            <i class="bi bi-list-check text-sm"></i>
                            Supervisar
                        </a>
                        @else
                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Sin práctica asignada
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Proceso -->
{{-- <div class="modal fade" id="modalProceso" tabindex="-1" role="dialog" aria-labelledby="modalProcesoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProcesoLabel">
                    <i class="bi bi-diagram-3"></i>
                    Proceso de Supervisión
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="stepper" class="stepper">
                    <!-- Stepper Items (Generated/Updated by JS) -->
                    <div class="stepper-item" data-stage="1">
                        <div class="stepper-circle">1</div>
                        <span class="stepper-label">Inicio</span>
                    </div>
                    <div class="stepper-item" data-stage="2">
                        <div class="stepper-circle">2</div>
                        <span class="stepper-label">Desarrollo</span>
                    </div>
                    <div class="stepper-item" data-stage="3">
                        <div class="stepper-circle">3</div>
                        <span class="stepper-label">Seguimiento</span>
                    </div>
                    <div class="stepper-item" data-stage="4">
                        <div class="stepper-circle">4</div>
                        <span class="stepper-label">Finalización</span>
                    </div>
                    <div class="stepper-item" data-stage="5">
                        <div class="stepper-circle">5</div>
                        <span class="stepper-label">Evaluación</span>
                    </div>
                </div>

                <!-- Stage Content -->
                <div class="tab-content" id="supervisionTabContent">
                    <!-- Stage 1: Inicio -->
                    <div class="tab-pane fade show active" id="content-stage-1" role="tabpanel">
                        @include('practicas.admin.supervision.supe_E1')
                    </div>

                    <!-- Stage 2: Desarrollo -->
                    <div class="tab-pane fade" id="content-stage-2" role="tabpanel">
                        <div id="etapa2-content">
                            @include('practicas.admin.supervision.supe_E2', ['etapa' => 1])
                        </div>
                        <!-- Internal nav for E2 (Boss details again? It was in the original file) -->
                         <div id="etapa2-jefe" style="display: none;">
                            @include('practicas.admin.supervision.supe_E2', ['etapa' => 3])
                        </div>
                    </div>

                    <!-- Stage 3: Seguimiento -->
                    <div class="tab-pane fade" id="content-stage-3" role="tabpanel">
                        @include('practicas.admin.supervision.supe_E3')
                    </div>

                    <!-- Stage 4: Finalización -->
                    <div class="tab-pane fade" id="content-stage-4" role="tabpanel">
                        @include('practicas.admin.supervision.supe_E4')
                    </div>

                    <!-- Stage 5: Evaluación -->
                    <div class="tab-pane fade" id="content-stage-5" role="tabpanel">
                        @include('practicas.admin.supervision.supe_E5')
                    </div>
                </div>

                <!-- Generic Review Form (Hidden by default, shown via JS) -->
                @include('practicas.admin.supervision.review_form')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>--}}
@endsection

@push('js')
<script src="{{ asset('js/supervision_practica.js') }}"></script>
@endpush


@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Inicialización de DataTable (Tabla Principal)
    $('#tablaPracticas').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar estudiante...",
            "paginate": { "first": "Primero", "last": "Último", "next": "Sig.", "previous": "Ant." },
        },
        pageLength: 10,
        responsive: true,
        dom: '<"flex flex-col md:flex-row md:items-center justify-between gap-4 py-8 px-2"lf>rt<"flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4 pb-2 px-2"ip>',
        initComplete: function() {
            $('#skeletonLoader').addClass('hidden');
            $('#tablaPracticas').addClass('dt-ready');
        }
    });
});
</script>
@endpush
