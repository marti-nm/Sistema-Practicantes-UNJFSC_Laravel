@extends('template')
@section('title', 'Dashboard Supervisor')
@section('subtitle', 'Panel de supervisión y seguimiento de estudiantes')

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
            title="Panel de Control del Supervisor"
            subtitle="Supervisión académica y seguimiento empresarial"
            icon="bi-eye"
            :enableButton="false"
        />

        <div class="">
            {{-- Indicadores de Supervisión --}}
            <div class="mb-12" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-1.5 h-8 bg-indigo-600 rounded-full shadow-[0_0_15px_rgba(79,70,229,0.5)]"></div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Indicadores de Supervisión</h3>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Estado actual de tus grupos asignados</p>
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
                                <span class="text-[10px] font-bold text-emerald-500 uppercase">Estudiantes</span>
                            </div>
                        </div>
                    </div>

                    <!-- Módulo Actual -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 200ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-layers-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Módulo Actual</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-xl font-black text-slate-800 dark:text-white tracking-tight uppercase">{{ $currentModule }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Anexo 7 -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-cyan-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 300ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-cyan-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-file-earmark-check-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Anexo 7 Completos</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">{{ $totalAnexo7 }}</span>
                                <span class="text-[12px] font-black text-slate-400">/ {{ $totalEstudiantes }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Anexo 8 -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 400ms;">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-5 group-hover:rotate-6 transition-transform text-2xl font-black">
                                <i class="bi bi-clipboard2-check-fill"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Anexo 8 Completos</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">{{ $totalAnexo8 }}</span>
                                <span class="text-[12px] font-black text-slate-400">/ {{ $totalEstudiantes }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progreso General -->
                    <div class="group relative bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500 hover:-translate-y-1"
                         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                         style="transition-delay: 500ms;">
                        <div class="relative">
                            <div class="flex justify-between items-center mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform">
                                    <i class="bi bi-speedometer2 text-xl"></i>
                                </div>
                                <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $progressGeneral }}%</span>
                            </div>
                            <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-3">Progreso General</h4>
                            <div class="w-full h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-rose-500 to-pink-600 transition-all duration-1000 ease-out" 
                                     :style="loaded ? 'width: {{ $progressGeneral }}%' : 'width: 0%'"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de Alumnos Supervisados --}}
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 mb-12 shadow-sm border border-slate-100 dark:border-slate-700 transition-all duration-300">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold shadow-inner">
                            <i class="bi bi-person-check text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-wider">Alumnos en Supervisión</h3>
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Detalle de avance y centro de prácticas</p>
                        </div>
                    </div>
                    
                    <div class="relative group/search max-w-sm w-full">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="bi bi-search text-slate-400 group-focus-within/search:text-blue-500 transition-colors"></i>
                        </div>
                        <input type="text" id="filtroTabla" 
                               class="block w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none" 
                               placeholder="Buscar practicante...">
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-700 table-container">
                    <table class="w-full text-left border-collapse" id="tablaAlumnos">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-slate-50 dark:bg-slate-900 shadow-sm">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Practicante</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500">Entidad Recceptora</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 text-center">Módulo</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 text-center">Estado Anexos</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($alumnos as $alumno)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group/row">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($alumno['foto'])
                                                <img src="{{ asset('storage/' . $alumno['foto']) }}" class="w-10 h-10 rounded-xl object-cover shadow-sm ring-1 ring-slate-100 dark:ring-slate-700">
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white font-black text-sm shadow-lg uppercase">
                                                    {{ substr($alumno['nombres'], 0, 1) }}{{ substr($alumno['apellidos'], 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $alumno['nombres'] }} {{ $alumno['apellidos'] }}</p>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Estudiante de Prácticas</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-building text-slate-400"></i>
                                            <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">{{ $alumno['empresa'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase tracking-wider border border-blue-100 dark:border-blue-800">
                                            {{ $alumno['modulo'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-3">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-[9px] font-black text-slate-400 uppercase">Anexo 7</span>
                                                @if($alumno['anexo_7'] === 'Completado')
                                                    <div class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </div>
                                                @else
                                                    <div class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-300 dark:text-slate-500">
                                                        <i class="bi bi-dash"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-[9px] font-black text-slate-400 uppercase">Anexo 8</span>
                                                @if($alumno['anexo_8'] === 'Completado')
                                                    <div class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </div>
                                                @else
                                                    <div class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-300 dark:text-slate-500">
                                                        <i class="bi bi-dash"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($alumno['anexo_7_pdf'] || $alumno['anexo_8_pdf'] || $alumno['anexo_6_pdf'])
                                                <button class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all border border-slate-100 dark:border-slate-800" title="Ver Documentos">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </button>
                                            @endif
                                            <a href="#" class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20" title="Detalle del Practicante">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if($alumnos->isEmpty())
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-20 h-20 rounded-3xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-300 dark:text-slate-700 border border-slate-100 dark:border-slate-800 shadow-inner">
                                                <i class="bi bi-people text-4xl"></i>
                                            </div>
                                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 italic">No tienes practicantes asignados actualmente.</p>
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Buscador en tabla
    const filtroInput = document.getElementById('filtroTabla');
    if (filtroInput) {
        filtroInput.addEventListener('input', function () {
            const valor = this.value.toLowerCase();
            const filas = document.querySelectorAll('#tablaAlumnos tbody tr');

            filas.forEach(fila => {
                const texto = fila.innerText.toLowerCase();
                fila.style.display = texto.includes(valor) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
