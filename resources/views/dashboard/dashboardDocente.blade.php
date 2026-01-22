@extends('template')
@section('title', 'Dashboard Docente')
@section('subtitle', 'Panel de control y supervisión de estudiantes')

@push('css')
<style>
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
        <x-header-content
            title="Panel de Supervisión Docente"
            subtitle="Gestión y seguimiento de prácticas"
            icon="bi-mortarboard"
            :enableButton="false"
        />
        <!-- Header Section -->
        {{--<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-white shadow-xl shadow-emerald-500/20">
                    <i class="bi bi-mortarboard text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Panel de Supervisión Docente</h2>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1">Gestión y seguimiento de prácticas</p>
                </div>
            </div>
        </div>--}}

        <div class="">
            {{-- Indicadores de Supervisión --}}
            <div class="mb-12" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-1.5 h-8 bg-emerald-600 rounded-full shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Indicadores de Supervisión</h3>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Resumen de actividades y métricas clave</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                    <!-- Total Estudiantes -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 100ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Total Estudiantes</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $totalEstudiantes }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 600)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-emerald-500 uppercase">Activos</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fichas Validadas -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 200ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-cyan-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-file-earmark-check-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Fichas Validadas</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $totalFichasValidadas }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 700)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-cyan-500 uppercase">Completas</span>
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

                    <!-- Matriculados -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 400ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-clipboard-check-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Matriculados</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $totalMatriculados ?? 0 }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 900)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-blue-500 uppercase">Completos</span>
                            </div>
                        </div>
                    </div>

                    <!-- No Matriculados -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 500ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-clipboard-x-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">No Matriculados</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight" 
                                      x-data="{ count: 0, target: {{ $totalNoMatriculados ?? 0 }} }" 
                                      x-init="setTimeout(() => { let start = 0; const duration = 1000; const startTime = performance.now(); const animate = (currentTime) => { const elapsed = currentTime - startTime; const progress = Math.min(elapsed / duration, 1); count = Math.floor(progress * target); if (progress < 1) requestAnimationFrame(animate); else count = target; }; requestAnimationFrame(animate); }, 1000)" 
                                      x-text="count">0</span>
                                <span class="text-[10px] font-bold text-rose-500 uppercase">Pendientes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grupos de Práctica por Supervisor --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 mb-12 shadow-sm border border-slate-100 dark:border-slate-700 transition-all duration-300">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center text-violet-600 dark:text-violet-400 font-bold shadow-inner">
                        <i class="bi bi-collection text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Grupos de Práctica</h3>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Distribución por supervisor y módulo actual</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-700 table-container max-h-[500px]">
                    <table class="w-full text-left border-collapse">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-slate-50 dark:bg-slate-900 shadow-sm">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Nombre del Grupo</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Supervisor</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 text-center">Estudiantes</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 text-center">Módulo Actual</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($groupsData as $group)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group/row">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white font-black text-sm shadow-lg">
                                                {{ substr($group['name'], 0, 2) }}
                                            </div>
                                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $group['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">{{ $group['supervisor'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-black text-sm">
                                            {{ $group['students'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-black uppercase tracking-wider border border-indigo-100 dark:border-indigo-800">
                                            <i class="bi bi-bookmark-fill"></i>
                                            {{ $group['modulo'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($group['status'] === 'Activo')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider border border-emerald-100 dark:border-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 dark:bg-slate-900/30 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider border border-slate-100 dark:border-slate-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            @if($groupsData->isEmpty())
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-20 h-20 rounded-3xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-300 dark:text-slate-700 border border-slate-100 dark:border-slate-800 shadow-inner">
                                                <i class="bi bi-collection text-4xl"></i>
                                            </div>
                                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 italic">No hay grupos de práctica asignados.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
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
    console.log("Dashboard docente cargado");
});
</script>
@endpush