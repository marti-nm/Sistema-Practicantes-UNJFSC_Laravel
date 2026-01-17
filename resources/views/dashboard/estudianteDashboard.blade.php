@extends('template')
@section('title', 'Dashboard Estudiante')
@section('subtitle', 'Panel de Información Académica')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" 
     x-data="{
        progresoModulo: {{ $progreso_modulo ?? 0 }},
        progresoPractica: {{ $progreso_practica ?? 0 }}
     }">

    <!-- Header Section -->
    <div class="flex items-center gap-3 mb-6">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
            <i class="bi bi-briefcase-fill text-lg"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">¡Bienvenido, {{ $ap->persona->nombres }}!</h2>
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                {{ $escuela->name ?? 'Escuela' }} • {{ $ap->seccion_academica->codigo ?? 'Sección' }}
            </p>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        
        <!-- Left Column: Progress Cards -->
        <div class="lg:col-span-2 space-y-4">
            
            <!-- Progress Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Módulo Progress Card -->
                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-md">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider">Progreso de Módulo</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Evaluación de Supervisión</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="bi bi-diagram-3 text-xl"></i>
                        </div>
                    </div>
                    
                    <!-- Circular Progress -->
                    <div class="flex items-center justify-center py-4">
                        <div class="relative w-32 h-32">
                            <svg class="transform -rotate-90 w-32 h-32">
                                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-200 dark:text-slate-700"></circle>
                                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" 
                                        :stroke-dasharray="2 * Math.PI * 56" 
                                        :stroke-dashoffset="2 * Math.PI * 56 * (1 - progresoModulo / 100)"
                                        class="text-blue-600 dark:text-blue-400 transition-all duration-1000 ease-out"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-black text-slate-800 dark:text-white" x-text="Math.round(progresoModulo) + '%'"></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Completado</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-600 dark:text-slate-400">Módulo Actual:</span>
                            <span class="font-black text-blue-600 dark:text-blue-400">
                                @if($modulo_actual)
                                    {{ $modulo_actual->name }}
                                @else
                                    Sin asignar
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Práctica Progress Card -->
                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-md">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider">Progreso de Práctica</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Etapas Completadas</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="bi bi-briefcase text-xl"></i>
                        </div>
                    </div>
                    
                    <!-- Circular Progress -->
                    <div class="flex items-center justify-center py-4">
                        <div class="relative w-32 h-32">
                            <svg class="transform -rotate-90 w-32 h-32">
                                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-200 dark:text-slate-700"></circle>
                                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" 
                                        :stroke-dasharray="2 * Math.PI * 56" 
                                        :stroke-dashoffset="2 * Math.PI * 56 * (1 - progresoPractica / 100)"
                                        class="text-emerald-600 dark:text-emerald-400 transition-all duration-1000 ease-out"></circle>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-black text-slate-800 dark:text-white" x-text="Math.round(progresoPractica) + '%'"></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Completado</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-600 dark:text-slate-400">Etapa Actual:</span>
                            <span class="font-black text-emerald-600 dark:text-emerald-400">
                                @if(isset($practicas->estate))
                                    Etapa {{ $practicas->estate }} de 6
                                @else
                                    Sin iniciar
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <!-- Matrícula -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center text-white shrink-0">
                            <i class="bi bi-journal-check text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-blue-900 dark:text-blue-100 uppercase tracking-wider truncate">Matrícula</p>
                            <p class="text-sm font-black text-blue-600 dark:text-blue-400 truncate">
                                @if($matricula && $matricula->estado_matricula == 'Completo')
                                    Completa
                                @else
                                    Pendiente
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Documentos Pendientes -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-600 flex items-center justify-center text-white shrink-0">
                            <i class="bi bi-file-earmark-text text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-amber-900 dark:text-amber-100 uppercase tracking-wider truncate">Documentos</p>
                            <p class="text-sm font-black text-amber-600 dark:text-amber-400">
                                {{ $estadisticas['documentos_pendientes'] }} pendientes
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Días de Práctica -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center text-white shrink-0">
                            <i class="bi bi-calendar-check text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-emerald-900 dark:text-emerald-100 uppercase tracking-wider truncate">Días</p>
                            <p class="text-sm font-black text-emerald-600 dark:text-emerald-400">
                                {{ $estadisticas['dias_practica'] }} días
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tipo de Práctica -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-600 flex items-center justify-center text-white shrink-0">
                            <i class="bi bi-gear text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-purple-900 dark:text-purple-100 uppercase tracking-wider truncate">Tipo</p>
                            <p class="text-sm font-black text-purple-600 dark:text-purple-400 truncate">
                                @if(isset($practicas->tipo_practica))
                                    {{ $practicas->tipo_practica == 1 ? 'Desarrollo' : 'Convalidación' }}
                                @else
                                    Sin definir
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Practice Timeline - Horizontal -->
            <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-md">
                <h3 class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="bi bi-clock-history text-blue-600"></i>
                    Línea de Tiempo de Práctica
                </h3>
                
                @php
                    $etapas = [
                        1 => ['nombre' => 'Inicio', 'icono' => 'play-circle'],
                        2 => ['nombre' => 'Desarrollo', 'icono' => 'code-slash'],
                        3 => ['nombre' => 'Eval. Inter.', 'icono' => 'clipboard-check'],
                        4 => ['nombre' => 'Continuación', 'icono' => 'arrow-repeat'],
                        5 => ['nombre' => 'Eval. Final', 'icono' => 'star'],
                        6 => ['nombre' => 'Completado', 'icono' => 'check-circle']
                    ];
                    $etapa_actual = $practicas->estate ?? 0;
                @endphp
                
                <div class="flex items-center justify-between gap-2">
                    @foreach($etapas as $numero => $etapa)
                        <div class="flex flex-col items-center flex-1 min-w-0">
                            <!-- Icon -->
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mb-1.5 {{ $numero <= $etapa_actual ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-slate-200 dark:bg-slate-700 text-slate-400' }} transition-all">
                                <i class="bi bi-{{ $etapa['icono'] }} text-sm"></i>
                            </div>
                            
                            <!-- Label -->
                            <p class="text-[9px] font-bold {{ $numero <= $etapa_actual ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400 dark:text-slate-600' }} text-center leading-tight">
                                {{ $etapa['nombre'] }}
                            </p>
                            
                            <!-- Current Badge -->
                            @if($numero == $etapa_actual)
                                <span class="mt-1 px-1.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[8px] font-black uppercase tracking-wider">Actual</span>
                            @endif
                        </div>
                        
                        <!-- Connector Line -->
                        @if($numero < count($etapas))
                            <div class="h-0.5 flex-1 {{ $numero < $etapa_actual ? 'bg-blue-600' : 'bg-slate-200 dark:bg-slate-700' }} transition-all"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Info Cards -->
        <div class="flex flex-col gap-4">
            
            <!-- Grupo de Práctica Card -->
            <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-md flex-1 flex flex-col">
                <h3 class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="bi bi-people text-blue-600"></i>
                    Mi Grupo de Práctica
                </h3>
                
                <div class="flex-1 flex flex-col justify-center">
                    @if($grupo_practica)
                        <div class="space-y-3">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Nombre del Grupo</p>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $grupo_practica->name }}</p>
                            </div>
                            
                            <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20">
                                <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Módulo Actual</p>
                                <p class="text-sm font-bold text-blue-700 dark:text-blue-300">
                                    @if($modulo_actual)
                                        {{ $modulo_actual->name }}
                                    @else
                                        Sin asignar
                                    @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="bi bi-inbox text-4xl text-slate-300 dark:text-slate-700 mb-2"></i>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No asignado a un grupo</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Docentes Card -->
            <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-md flex-1 flex flex-col">
                <h3 class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="bi bi-person-badge text-blue-600"></i>
                    Docentes Asignados
                </h3>
                
                <div class="flex-1 flex flex-col justify-center space-y-3">
                    <!-- Docente Titular -->
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white shrink-0 font-black text-sm">
                            {{ substr($docente->nombres ?? 'D', 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Docente Titular</p>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate">
                                {{ $docente->nombres ?? 'Pendiente' }} {{ $docente->apellidos ?? '' }}
                            </p>
                        </div>
                    </div>

                    <!-- Supervisor -->
                    @if($supervisor)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-600 to-emerald-600 flex items-center justify-center text-white shrink-0 font-black text-sm">
                                {{ substr($supervisor->nombres ?? 'S', 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Supervisor</p>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate">
                                    {{ $supervisor->nombres }} {{ $supervisor->apellidos }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-md">
                <h3 class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="bi bi-lightning text-blue-600"></i>
                    Acciones Rápidas
                </h3>
                
                <div class="space-y-2">
                    <a href="{{ route('matricula.estudiante') }}" 
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                            <i class="bi bi-file-earmark-check text-sm"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Ver Matrícula</span>
                    </a>

                    <a href="{{ route('practicas.estudiante') }}" 
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                            <i class="bi bi-briefcase text-sm"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Gestionar Prácticas</span>
                    </a>

                    <a href="{{ route('perfil') }}" 
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
                            <i class="bi bi-person text-sm"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Mi Perfil</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert if Matricula Incomplete -->
    @if($matricula && $matricula->estado_matricula != 'Completo')
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white shrink-0">
                <i class="bi bi-exclamation-triangle text-xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-black text-amber-900 dark:text-amber-100 mb-1">Acción Requerida</h4>
                <p class="text-xs font-semibold text-amber-800 dark:text-amber-200 leading-relaxed">
                    Tienes pendientes en tu matrícula. Por favor completa los requisitos para continuar con tus prácticas.
                </p>
                <a href="{{ route('matricula.estudiante') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-black uppercase tracking-wider rounded-lg transition-colors">
                    <i class="bi bi-arrow-right"></i>
                    Completar Matrícula
                </a>
            </div>
        </div>
    @endif
</div>
@endsection