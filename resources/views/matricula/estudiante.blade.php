@extends('template')
@section('title', 'Matrícula')
@section('subtitle', 'Requisitos para Prácticas Pre-Profesionales')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
         loading: false
     }">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
            <i class="bi bi-mortarboard-fill text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Requisitos para Prácticas</h2>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Documentación obligatoria para habilitar el sistema</p>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-3 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-green-600 dark:text-green-400 text-sm"></i>
            <p class="text-xs font-semibold text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="bi bi-x-lg text-xs"></i></button>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded-lg flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-red-600 dark:text-red-400 text-sm"></i>
            <p class="text-xs font-semibold text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
        <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="bi bi-x-lg text-xs"></i></button>
    </div>
    @endif

    <!-- Completion Banner -->
    @if($matricula && $matricula->estado_matricula == 'Completo')
    <div class="mb-5 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex items-center gap-3">
        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="bi bi-check-circle-fill text-blue-600 dark:text-blue-400 text-xl"></i>
        </div>
        <div>
            <h3 class="text-sm font-black text-slate-800 dark:text-white">¡Matrícula Completada!</h3>
            <p class="text-xs text-slate-600 dark:text-slate-400">La matrícula ha sido completada y revisada correctamente por el docente.</p>
        </div>
    </div>
    @endif

    <!-- Documents Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- FICHA DE MATRÍCULA -->
        <div class="group relative bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-sm border-2 border-slate-200 dark:border-slate-800 overflow-hidden hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300">
            <!-- Accent Bar -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
            
            <!-- Card Content -->
            <div class="p-5">
                <!-- Title Section -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <i class="bi bi-file-text-fill text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 dark:text-white">Ficha de Matrícula</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Documento obligatorio</p>
                        </div>
                    </div>
                </div>

                @if($ficha)
                    <!-- Status Badge -->
                    <div class="mb-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold
                            @if($ficha->estado_archivo == 'Aprobado') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                            @elseif($ficha->estado_archivo == 'Enviado') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                            @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 @endif">
                            <i class="bi 
                                @if($ficha->estado_archivo == 'Aprobado') bi-check-circle-fill
                                @elseif($ficha->estado_archivo == 'Enviado') bi-clock-history
                                @else bi-exclamation-triangle-fill @endif"></i>
                            <span>
                                @if($ficha->estado_archivo == 'Aprobado') Aprobado
                                @elseif($ficha->estado_archivo == 'Enviado') En Revisión
                                @else Requiere Corrección @endif
                            </span>
                        </div>
                    </div>

                    <!-- File Info -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 mb-4 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <i class="bi bi-file-earmark-pdf text-red-500 text-xl flex-shrink-0"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">Ficha Enviada</p>
                                    @if($ficha->estado_archivo == 'Corregir' && $ficha->comentario)
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 italic truncate mt-0.5">{{ $ficha->comentario }}</p>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('documentos.show', ['path' => str_replace('storage/', '', $ficha->ruta)]) }}" 
                               target="_blank"
                               class="ml-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm flex-shrink-0">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6 mb-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700">
                        <i class="bi bi-cloud-upload text-slate-400 dark:text-slate-600 text-3xl mb-2 block"></i>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Ningún archivo subido</p>
                    </div>
                @endif

                <!-- Upload Form -->
                @if(!$ficha || $ficha->estado_archivo == 'Corregir')
                <form action="{{ route('subir.ficha') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="ap_id" value="{{ $ap->id }}">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-2">
                            <i class="bi bi-paperclip mr-1"></i> Seleccionar Archivo PDF (Máx. 20MB)
                        </label>
                        <input type="file" 
                               name="ficha" 
                               accept=".pdf" 
                               required
                               class="block w-full text-xs text-slate-600 dark:text-slate-400
                                      file:mr-3 file:py-2 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-xs file:font-bold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100
                                      dark:file:bg-blue-900/30 dark:file:text-blue-400
                                      border border-slate-200 dark:border-slate-700 rounded-lg
                                      transition-all cursor-pointer">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs uppercase tracking-wide shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-cloud-arrow-up text-base"></i>
                        {{ isset($ficha) ? 'Subir Corrección' : 'Subir Ficha' }}
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- RECORD DE NOTAS -->
        <div class="group relative bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-sm border-2 border-slate-200 dark:border-slate-800 overflow-hidden hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
            <!-- Accent Bar -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-600 to-teal-600"></div>
            
            <!-- Card Content -->
            <div class="p-5">
                <!-- Title Section -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <i class="bi bi-journal-bookmark-fill text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 dark:text-white">Record de Notas</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Documento obligatorio</p>
                        </div>
                    </div>
                </div>

                @if($record)
                    <!-- Status Badge -->
                    <div class="mb-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold
                            @if($record->estado_archivo == 'Aprobado') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                            @elseif($record->estado_archivo == 'Enviado') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400
                            @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 @endif">
                            <i class="bi 
                                @if($record->estado_archivo == 'Aprobado') bi-check-circle-fill
                                @elseif($record->estado_archivo == 'Enviado') bi-clock-history
                                @else bi-exclamation-triangle-fill @endif"></i>
                            <span>
                                @if($record->estado_archivo == 'Aprobado') Aprobado
                                @elseif($record->estado_archivo == 'Enviado') En Revisión
                                @else Requiere Corrección @endif
                            </span>
                        </div>
                    </div>

                    <!-- File Info -->
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 mb-4 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <i class="bi bi-file-earmark-pdf text-red-500 text-xl flex-shrink-0"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">Record Enviado</p>
                                    @if($record->estado_archivo == 'Corregir' && $record->comentario)
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 italic truncate mt-0.5">{{ $record->comentario }}</p>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('documentos.show', ['path' => str_replace('storage/', '', $record->ruta)]) }}" 
                               target="_blank"
                               class="ml-2 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm flex-shrink-0">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-6 mb-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700">
                        <i class="bi bi-cloud-upload text-slate-400 dark:text-slate-600 text-3xl mb-2 block"></i>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Ningún archivo subido</p>
                    </div>
                @endif

                <!-- Upload Form -->
                @if(!$record || $record->estado_archivo == 'Corregir')
                <form action="{{ route('subir.record') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="ap_id" value="{{ $ap->id }}">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-2">
                            <i class="bi bi-paperclip mr-1"></i> Seleccionar Archivo PDF (Máx. 20MB)
                        </label>
                        <input type="file" 
                               name="record" 
                               accept=".pdf" 
                               required
                               class="block w-full text-xs text-slate-600 dark:text-slate-400
                                      file:mr-3 file:py-2 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-xs file:font-bold
                                      file:bg-emerald-50 file:text-emerald-700
                                      hover:file:bg-emerald-100
                                      dark:file:bg-emerald-900/30 dark:file:text-emerald-400
                                      border border-slate-200 dark:border-slate-700 rounded-lg
                                      transition-all cursor-pointer">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs uppercase tracking-wide shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-cloud-arrow-up text-base"></i>
                        {{ isset($record) ? 'Subir Corrección' : 'Subir Record' }}
                    </button>
                </form>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection