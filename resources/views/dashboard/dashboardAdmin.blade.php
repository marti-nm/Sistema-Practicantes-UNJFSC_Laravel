@extends('template')
@section('title', 'Dashboard Administrativo')
@section('subtitle', 'Panel de control y métricas del sistema de prácticas')

@push('css')
<style>
    /* Estilos específicos para componentes de terceros si es necesario */
    .table-container {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .table-container::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .table-container::-webkit-scrollbar-track {
        background: transparent;
    }
    .table-container::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
    .dark .table-container::-webkit-scrollbar-thumb {
        background-color: #475569;
    }
</style>
@endpush

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-xl shadow-blue-500/20">
                    <i class="bi bi-speedometer2 text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Panel de Control Administrativo</h2>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">Gestión académica oficial</p>
                </div>
            </div>
        </div>

        <div class="">
            {{-- Filtros --}}
            <x-data-filter
                route="admin.Dashboard"
                :facultades="$facultades"
            />

            {{-- Métricas --}}
            <div class="mb-12" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-1.5 h-8 bg-blue-600 rounded-full shadow-[0_0_15px_rgba(37,99,235,0.5)]"></div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Métricas de Rendimiento</h3>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Resumen estadístico del periodo actual</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                    <!-- Total Alumnos -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 100ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Total Alumnos</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $totalPorEscuelaEnSemestre }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 600)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-blue-500 uppercase">Total</span>
                            </div>
                        </div>
                    </div>

                    <!-- Matriculados -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 200ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-cyan-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Matriculados</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $totalMatriculados }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 700)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-cyan-500 uppercase">Activos</span>
                            </div>
                        </div>
                    </div>

                    <!-- Supervisores -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 300ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Supervisores</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $totalSupervisores }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 800)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-amber-500 uppercase">Asignados</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fichas Completas -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 400ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-file-earmark-check-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Fichas Completas</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $completos }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 900)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-emerald-500 uppercase">Validadas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pendientes -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 500ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-exclamation-circle-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Pendientes</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $pendientes }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 1000)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-rose-500 uppercase">Revisión</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de Estudiantes --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 mb-12 shadow-sm border border-slate-100 dark:border-slate-700 transition-all duration-300">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 font-bold shadow-inner">
                            <i class="bi bi-list-ul text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Lista de Estudiantes</h3>
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Detalle de matriculados en el semestre</p>
                        </div>
                    </div>
                    <div class="relative w-full md:w-96 group">
                        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors group-focus-within:text-blue-500"></i>
                        <input type="text" id="filtroTabla" 
                               class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl text-sm font-semibold text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none border-1 border-slate-100 dark:border-slate-700" 
                               placeholder="Buscar estudiantes...">
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-700 table-container max-h-[500px]">
                    <table class="w-full text-left border-collapse" id="tablaEstudiantes">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-slate-50 dark:bg-slate-900 shadow-sm">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">#</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Nombre</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Apellido</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Facultad</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Escuela</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 text-center">Ficha</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 text-center">Record</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($listaEstudiantes as $i => $item)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group/row">
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-black text-slate-400">#{{ $i + 1 }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $item->asignacion_persona->persona->nombres }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $item->asignacion_persona->persona->apellidos }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                       <span class="text-[10px] font-black uppercase bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded text-slate-500 dark:text-slate-400">{{ $item->asignacion_persona->seccion_academica->facultad->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $item->asignacion_persona->seccion_academica->escuela->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php $estadoFicha = $item->estado_ficha ?? 'Sin registrar'; @endphp
                                        @if($estadoFicha === 'Completo')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider border border-emerald-100 dark:border-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Completo
                                            </span>
                                        @elseif($estadoFicha === 'En proceso')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase tracking-wider border border-amber-100 dark:border-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                En proceso
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase tracking-wider border border-rose-100 dark:border-rose-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php $estadoRecord = $item->estado_record ?? 'Sin registrar'; @endphp
                                        @if($estadoRecord === 'Completo')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider border border-emerald-100 dark:border-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Completo
                                            </span>
                                        @elseif($estadoRecord === 'En proceso')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-[10px] font-black uppercase tracking-wider border border-amber-100 dark:border-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                En proceso
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase tracking-wider border border-rose-100 dark:border-rose-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            @if($listaEstudiantes->isEmpty())
                                <tr>
                                    <td colspan="7" class="py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-20 h-20 rounded-3xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-300 dark:text-slate-700 border border-slate-100 dark:border-slate-800 shadow-inner">
                                                <i class="bi bi-people text-4xl"></i>
                                            </div>
                                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 italic">No se encontraron estudiantes registrados.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Gráficos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Gráfico de Barras --}}
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-700 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400 font-bold shadow-inner border border-rose-100 dark:border-rose-900/20">
                            <i class="bi bi-bar-chart text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Estado por Escuela</h3>
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Distribución de fichas académicas</p>
                        </div>
                    </div>
                    <div class="h-80 w-full">
                        <canvas id="stackedBarChart"></canvas>
                    </div>
                </div>

                {{-- Gráfico de Líneas --}}
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-100 dark:border-slate-700 transition-all duration-300">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold shadow-inner border border-blue-100 dark:border-blue-900/20">
                            <i class="bi bi-graph-up-arrow text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Flujo de Validaciones</h3>
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Tendencia mensual de fichas ({{ date('Y') }})</p>
                        </div>
                    </div>
                    <div class="h-80 w-full">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>





@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Gráfico de Líneas
    const ctxLineEl = document.getElementById('lineChart');
    if (ctxLineEl) {
        new Chart(ctxLineEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($fichasPorMes->pluck('mes')) !!},
                datasets: [{
                    label: 'Fichas validadas',
                    data: {!! json_encode($fichasPorMes->pluck('total')) !!},
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(30, 58, 138, 0.1)',
                    pointBackgroundColor: '#1e3a8a',
                    pointBorderColor: '#fff',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { font: { family: 'Inter', weight: 'bold' } } }
                },
                scales: {
                    x: { ticks: { font: { family: 'Inter' } }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Inter' } } }
                }
            }
        });
    }

    // 2. Gráfico de Barras
    let stackedChart;
    const renderStackedChart = (data) => {
        const ctx = document.getElementById('stackedBarChart');
        if (!ctx) return;
        if (stackedChart) stackedChart.destroy();
        stackedChart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.map(i => i.escuela),
                datasets: [
                    { label: 'Completo', data: data.map(i => i.completos), backgroundColor: '#059669' },
                    { label: 'En proceso', data: data.map(i => i.en_proceso), backgroundColor: '#d97706' },
                    { label: 'Pendiente', data: data.map(i => i.pendientes), backgroundColor: '#dc2626' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    };
    renderStackedChart(@json($fichasPorEscuela));

    // 3. Filtros de Facultad/Escuela/Docente
    const facultadSelect = document.getElementById('facultad');
    const escuelaSelect = document.getElementById('escuela');
    const docenteSelect = document.getElementById('docente');
    const semestreActivoId = {{ session('semestre_actual_id') ?? 'null' }};

    if (facultadSelect) {
        facultadSelect.addEventListener('change', function() {
            const id = this.value;
            escuelaSelect.innerHTML = '<option value="">-- Todas --</option>';
            docenteSelect.innerHTML = '<option value="">-- Todos --</option>';
            if (!id) return;
            fetch(`/api/escuelas/${id}`).then(r => r.json()).then(data => {
                data.forEach(e => escuelaSelect.innerHTML += `<option value="${e.id}">${e.name}</option>`);
            });
        });
    }

    if (escuelaSelect) {
        escuelaSelect.addEventListener('change', function() {
            const id = this.value;
            docenteSelect.innerHTML = '<option value="">-- Todos --</option>';
            if (!id || !semestreActivoId) return;
            fetch(`/api/secciones/${id}/${semestreActivoId}`).then(r => r.json()).then(data => {
                data.forEach(d => docenteSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`);
            });
        });
    }

    // 4. Buscador de Tabla
    const searchInput = document.getElementById('filtroTabla');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tablaEstudiantes tbody tr:not(.no-results)');
            let matches = 0;
            rows.forEach(r => {
                const match = r.textContent.toLowerCase().includes(q);
                r.style.display = match ? '' : 'none';
                if (match) matches++;
            });
            const tbody = document.querySelector('#tablaEstudiantes tbody');
            const old = tbody.querySelector('.no-results');
            if (old) old.remove();
            if (matches === 0 && q.length > 0) {
                const tr = document.createElement('tr');
                tr.className = 'no-results';
                tr.innerHTML = `<td colspan="7" class="py-20 text-center text-slate-400 font-medium italic">No se encontraron resultados para "${q}"</td>`;
                tbody.appendChild(tr);
            }
        });
    }

    // 5. Efectos de UI
    $('form').on('submit', function() {
        const btn = $(this).find('button[type="submit"]');
        if (btn.length) {
            btn.html('<i class="bi bi-hourglass-split animate-spin mr-2"></i>Filtrando...').prop('disabled', true);
        }
    });
});
</script>
@endpush