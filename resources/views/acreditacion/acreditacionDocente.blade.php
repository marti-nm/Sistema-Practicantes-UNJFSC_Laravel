@extends('template')
@section('title', 'Acreditacion Pendiente')
@section('subtitle', 'Gestión de Documentos Obligatorios')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <x-header-content
        title="Requisitos de Habilitación de Acceso"
        subtitle="Gestionar y validar documentos académicos de estudiantes"
        icon="bi-patch-check-fill"
        :enableButton="false"
    />

    <!-- Info Banner -->
    <div class="mb-8 p-6 bg-slate-50 dark:bg-slate-800 rounded-[1.25rem] shadow-sm border-1 border-slate-100 dark:border-slate-700 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <i class="bi bi-shield-lock-fill text-9xl text-slate-300 dark:text-slate-600"></i>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start gap-5">
            <div class="shrink-0 p-3.5 bg-blue-50 dark:bg-blue-900/30 rounded-2xl text-blue-600 dark:text-blue-400">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                    <path d="M4 8a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3A.5.5 0 0 1 4 8m5.5 0a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m-5.5 4a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m5.5 0a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight mb-2">Requisitos de Habilitación de Acceso</h3>
                <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed max-w-3xl">
                    Debe subir la documentación obligatoria correspondiente a su rol para habilitar la navegación completa del sistema. Asegúrese de que los documentos sean legibles y estén en formato PDF.
                </p>
            </div>
        </div>
    </div>

    <!-- Grid Cards -->
    <div class="grid grid-cols-1 {{ ($ap->id_rol == 4) ? 'lg:grid-cols-3' : 'lg:grid-cols-2' }} gap-6">
        @php
            $archivosPorTipo = $acreditacion->archivos->groupBy('tipo');

            $getLatest = function ($tipo) use ($archivosPorTipo) {
                $history = $archivosPorTipo->get($tipo);
                // Si existe, lo ordenamos y tomamos el primero (el más nuevo)
                return $history ? $history->sortByDesc('created_at')->first() : null; 
            };

            $latestCL = $getLatest('carga_lectiva');
            $estadoCL = $latestCL ? $latestCL->estado_archivo : 'Falta';
            $msjCL = ($estadoCL === 'Corregir') ? $latestCL->comentario : null;


            $latestHorario = $getLatest('horario');
            $estadoHorario = $latestHorario ? $latestHorario->estado_archivo : 'Falta';
            $msjHorario = ($estadoHorario === 'Corregir') ? $latestHorario->comentario : null;


            $latestResolucion = $getLatest('resolucion');
            $estadoResolucion = $latestResolucion ? $latestResolucion->estado_archivo : 'Falta';
            $msjResolucion = ($estadoResolucion === 'Corregir') ? $latestResolucion->comentario : null;
        @endphp

        <!-- TARJETA 1: CARGA LECTIVA -->
        <div class="group relative bg-slate-50 dark:bg-slate-800 rounded-[1.5rem] border-1 border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col"
             x-data="{ fileName: '' }">
            <div class="p-6 sm:p-8 flex-1 flex flex-col">
                <div class="flex items-start gap-5 mb-6">
                    <div class="shrink-0 w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm6-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1zm6-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">Carga Lectiva (C.L.)</h2>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Documento oficial de distribución de horas.</p>
                    </div>
                </div>

                <div class="mb-6 pl-4 border-l-4 border-blue-400 dark:border-blue-500">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Requisito</p>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Debe estar firmado y sellado. (Formato PDF).</p>
                </div>

                <div class="mt-auto space-y-4">
                    @if($estadoCL === 'Aprobado')
                        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border-1 border-emerald-100 dark:border-emerald-800/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-800/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <p class="text-sm font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">¡Documento Aprobado!</p>
                            </div>
                            @if($latestCL->ruta ?? null)
                                <a href="{{ asset($latestCL->ruta) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border-1 border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors">
                                    <i class="bi bi-eye-fill"></i> Ver Versión Aprobada
                                </a>
                            @endif
                        </div>
                    @elseif($estadoCL === 'Enviado')
                        <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border-1 border-blue-100 dark:border-blue-800/30 flex items-start gap-3">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-800/50 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-blue-800 dark:text-blue-300">En Revisión</p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Su constancia está siendo revisada. Por favor, espere.</p>
                            </div>
                        </div>
                    @elseif($estadoCL === 'Corregir')
                        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border-1 border-rose-100 dark:border-rose-800/30">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-800/50 flex items-center justify-center text-rose-600 dark:text-rose-400">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <p class="text-sm font-black text-rose-800 dark:text-rose-400 uppercase tracking-wide">Debe Corregir</p>
                            </div>
                            <p class="text-xs font-medium text-rose-700 dark:text-rose-300 mb-3 pl-11">
                                <span class="font-bold">Observación:</span> {{ $msjCL ?: 'Sin comentarios.' }}
                            </p>
                            @if($latestCL->ruta ?? null)
                                <a href="{{ asset($latestCL->ruta) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border-1 border-rose-200 dark:border-rose-700 text-rose-600 dark:text-rose-400 text-[10px] font-bold uppercase tracking-wider hover:bg-rose-50 transition-colors mb-2">
                                    Ver Versión Anterior
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($estadoCL != 'Enviado' && $estadoCL != 'Aprobado')
                        <form action="{{ route('subir.clectiva') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="ap_id" value="{{ $ap->id }}">
                            
                            <div class="relative group/drop">
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-2 ml-1">Subir Archivo</label>
                                <div class="relative w-full h-32 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition-all cursor-pointer flex flex-col items-center justify-center text-center p-4 group-hover/drop:scale-[1.01]"
                                     @click="document.getElementById('carga_lectiva').click()">
                                    
                                    <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 shadow-sm flex items-center justify-center text-blue-500 mb-2 group-hover/drop:scale-110 transition-transform">
                                        <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300" x-text="fileName || 'Click para seleccionar PDF'">Click para seleccionar PDF</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Máximo 5MB</p>
                                    
                                    <input id="carga_lectiva" name="carga_lectiva" type="file" class="hidden" 
                                           @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" 
                                           accept=".pdf" required>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl shadow-lg shadow-blue-500/20 font-bold text-xs uppercase tracking-widest transform active:scale-95 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-send-fill"></i>
                                Confirmar y Subir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- TARJETA 2: HORARIO DE CLASES -->
        <div class="group relative bg-slate-50 dark:bg-slate-800 rounded-[1.5rem] border-1 border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col"
             x-data="{ fileName: '' }">
            <div class="p-6 sm:p-8 flex-1 flex flex-col">
                <div class="flex items-start gap-5 mb-6">
                    <div class="shrink-0 w-14 h-14 rounded-2xl bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400 shadow-sm">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1h2.5V7a.5.5 0 0 1 .5-.5z"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">Horario de Clases</h2>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Distribución semanal de la actividad docente.</p>
                    </div>
                </div>

                <div class="mb-6 pl-4 border-l-4 border-teal-400 dark:border-teal-500">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Requisito</p>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Asegúrese de que coincida con la Carga Lectiva. (PDF).</p>
                </div>

                <div class="mt-auto space-y-4">
                    @if($estadoHorario === 'Aprobado')
                        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border-1 border-emerald-100 dark:border-emerald-800/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-800/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <p class="text-sm font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">¡Documento Aprobado!</p>
                            </div>
                            @if($latestHorario->ruta ?? null)
                                <a href="{{ asset($latestHorario->ruta) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border-1 border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors">
                                    <i class="bi bi-eye-fill"></i> Ver Versión Aprobada
                                </a>
                            @endif
                        </div>
                    @elseif($estadoHorario === 'Enviado')
                        <div class="p-4 rounded-2xl bg-teal-50 dark:bg-teal-900/20 border-1 border-teal-100 dark:border-teal-800/30 flex items-start gap-3">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-teal-100 dark:bg-teal-800/50 flex items-center justify-center text-teal-600 dark:text-teal-400">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-teal-800 dark:text-teal-300">En Revisión</p>
                                <p class="text-xs text-teal-600 dark:text-teal-400 mt-1">Su constancia está siendo revisada. Por favor, espere.</p>
                            </div>
                        </div>
                    @elseif($estadoHorario === 'Corregir')
                        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border-1 border-rose-100 dark:border-rose-800/30">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-800/50 flex items-center justify-center text-rose-600 dark:text-rose-400">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <p class="text-sm font-black text-rose-800 dark:text-rose-400 uppercase tracking-wide">Debe Corregir</p>
                            </div>
                            <p class="text-xs font-medium text-rose-700 dark:text-rose-300 mb-3 pl-11">
                                <span class="font-bold">Observación:</span> {{ $msjHorario ?: 'Sin comentarios.' }}
                            </p>
                            @if($latestHorario->ruta ?? null)
                                <a href="{{ asset($latestHorario->ruta) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border-1 border-rose-200 dark:border-rose-700 text-rose-600 dark:text-rose-400 text-[10px] font-bold uppercase tracking-wider hover:bg-rose-50 transition-colors mb-2">
                                    Ver Versión Anterior
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($estadoHorario != 'Enviado' && $estadoHorario != 'Aprobado')
                        <form action="{{ route('subir.horario') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="ap_id" value="{{ $ap->id }}">
                            
                            <div class="relative group/drop">
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-2 ml-1">Subir Archivo</label>
                                <div class="relative w-full h-32 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 hover:border-teal-500 dark:hover:border-teal-400 hover:bg-teal-50/50 dark:hover:bg-teal-900/10 transition-all cursor-pointer flex flex-col items-center justify-center text-center p-4 group-hover/drop:scale-[1.01]"
                                     @click="document.getElementById('horario').click()">
                                    
                                    <div class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 shadow-sm flex items-center justify-center text-teal-500 mb-2 group-hover/drop:scale-110 transition-transform">
                                        <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300" x-text="fileName || 'Click para seleccionar PDF'">Click para seleccionar PDF</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Máximo 5MB</p>
                                    
                                    <input id="horario" name="horario" type="file" class="hidden" 
                                           @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" 
                                           accept=".pdf" required>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white rounded-xl shadow-lg shadow-teal-500/20 font-bold text-xs uppercase tracking-widest transform active:scale-95 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-send-fill"></i>
                                Confirmar y Subir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- TARJETA 3: RESOLUCION DE DESINGACION DE SUPERVISOR -->
        @if($ap->id_rol == 4)
        <div class="group relative bg-slate-50 dark:bg-slate-800 rounded-[1.5rem] border-1 border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex flex-col"
             x-data="{ fileName: '' }">
            <div class="p-6 sm:p-8 flex-1 flex flex-col">
                <div class="flex items-start gap-5 mb-6">
                    <div class="shrink-0 w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-500 dark:text-amber-400 shadow-sm">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
                            <path d="M8 5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1 0-1h4V5.5a.5.5 0 0 1-.5-.5zM8 8.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1 0-1h4V9a.5.5 0 0 1-.5-.5zM12 11.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1 0-1h4V12a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">Resolución</h2>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500 mt-1">Resolución de designación de docente supervisor.</p>
                    </div>
                </div>

                <div class="mb-6 pl-4 border-l-4 border-amber-400 dark:border-amber-500">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Requisito</p>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Asegúrese de que coincida con la Carga Lectiva. (PDF).</p>
                </div>

                <div class="mt-auto space-y-4">
                    @if($estadoResolucion === 'Aprobado')
                        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border-1 border-emerald-100 dark:border-emerald-800/30">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-800/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <p class="text-sm font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">¡Documento Aprobado!</p>
                            </div>
                            @if($latestResolucion->ruta ?? null)
                                <a href="{{ asset($latestResolucion->ruta) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border-1 border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors">
                                    <i class="bi bi-eye-fill"></i> Ver Versión Aprobada
                                </a>
                            @endif
                        </div>
                    @elseif($estadoResolucion === 'Enviado')
                        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20 border-1 border-amber-100 dark:border-amber-800/30 flex items-start gap-3">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-800/50 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-amber-800 dark:text-amber-300">En Revisión</p>
                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Su constancia está siendo revisada. Por favor, espere.</p>
                            </div>
                        </div>
                    @elseif($estadoResolucion === 'Corregir')
                        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-900/20 border-1 border-rose-100 dark:border-rose-800/30">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-800/50 flex items-center justify-center text-rose-600 dark:text-rose-400">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <p class="text-sm font-black text-rose-800 dark:text-rose-400 uppercase tracking-wide">Debe Corregir</p>
                            </div>
                            <p class="text-xs font-medium text-rose-700 dark:text-rose-300 mb-3 pl-11">
                                <span class="font-bold">Observación:</span> {{ $msjResolucion ?: 'Sin comentarios.' }}
                            </p>
                            @if($latestResolucion->ruta ?? null)
                                <a href="{{ asset($latestResolucion->ruta) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border-1 border-rose-200 dark:border-rose-700 text-rose-600 dark:text-rose-400 text-[10px] font-bold uppercase tracking-wider hover:bg-rose-50 transition-colors mb-2">
                                    Ver Versión Anterior
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($estadoResolucion != 'Enviado' && $estadoResolucion != 'Aprobado')
                        <form action="{{ route('subir.resolucion') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="ap_id" value="{{ $ap->id }}">
                            
                            <div class="relative group/drop">
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-2 ml-1">Subir Archivo</label>
                                <div class="relative w-full h-32 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 hover:border-amber-500 dark:hover:border-amber-400 hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition-all cursor-pointer flex flex-col items-center justify-center text-center p-4 group-hover/drop:scale-[1.01]"
                                     @click="document.getElementById('resolucion').click()">
                                    
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center text-amber-500 mb-2 group-hover/drop:scale-110 transition-transform">
                                        <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300" x-text="fileName || 'Click para seleccionar PDF'">Click para seleccionar PDF</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Máximo 5MB</p>
                                    
                                    <input id="resolucion" name="resolucion" type="file" class="hidden" 
                                           @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" 
                                           accept=".pdf" required>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-xl shadow-lg shadow-amber-500/20 font-bold text-xs uppercase tracking-widest transform active:scale-95 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-send-fill"></i>
                                Confirmar y Subir
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Mensaje de Estado (Si aplica) -->
    @if(session('status'))
        <div class="mt-8 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border-1 border-emerald-100 dark:border-emerald-800/30 flex items-center gap-4 shadow-lg shadow-emerald-500/10" role="alert">
            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-800/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                <i class="bi bi-check-lg text-xl"></i>
            </div>
            <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300">{{ session('status') }}</p>
        </div>
    @endif
</div>

@endsection
