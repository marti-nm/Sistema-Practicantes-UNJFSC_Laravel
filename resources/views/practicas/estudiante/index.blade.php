@extends('template')

@section('title', 'Mi Práctica')
@section('subtitle', 'Gestión de avances y documentación')

@section('content')
@php
    $empresa = $practicas->empresa ?? null;
    $jefeInmediato = $practicas->jefeInmediato ?? null;
@endphp

<div class="h-[calc(100vh-120px)] flex flex-col px-4 sm:px-6 overflow-hidden" 
     x-data="studentPracticeManager()" 
     x-init="init()">
    
    <!-- HEADER INTEGRADO (COMPACTO Y PLANO) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-3 shrink-0 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/10">
                <i class="bi bi-mortarboard-fill text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black text-slate-800 dark:text-white tracking-tight leading-none uppercase">Mi Avance de Prácticas</h2>
                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">
                    {{ $practicas->tipo_practica === 'desarrollo' ? 'Desarrollo de Prácticas' : 'Convalidación de Prácticas' }}
                </p>
            </div>
        </div>

        <!-- STEPPER COMPACTO -->
        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-white/5 p-1 rounded-xl">
            <template x-for="(step, index) in steps" :key="index">
                <button @click="setStage(index + 1)"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all"
                        :class="currentStage === index + 1 ? 'bg-white dark:bg-slate-800 shadow-sm text-blue-600' : 'text-slate-400 hover:text-slate-600'">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black border-2"
                         :class="currentStage === index + 1 ? 'border-blue-600 bg-blue-600 text-white' : (practicaState > index + 1 ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 dark:border-slate-600')">
                        <template x-if="practicaState > index + 1">
                            <i class="bi bi-check-lg"></i>
                        </template>
                        <template x-if="!(practicaState > index + 1)">
                            <span x-text="index + 1"></span>
                        </template>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-tight hidden lg:block" x-text="step"></span>
                </button>
            </template>
        </div>

        <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/50 px-3 py-1.5 rounded-xl border border-slate-100 dark:border-white/5">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Status Final</p>
            <span class="text-[11px] font-bold" :class="practicaState >= 6 ? 'text-emerald-500' : 'text-amber-500'" x-text="practicaState >= 6 ? 'Calificado' : 'En Proceso'"></span>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="flex flex-1 overflow-hidden gap-4 py-4 min-h-0 relative">
        
        <!-- SIDEBAR IZQUIERDO: SELECCIÓN DE DOCUMENTOS/OPCIONES -->
        <aside class="w-72 hidden lg:flex flex-col gap-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl p-4 shrink-0 h-full min-h-0">
            <div class="flex flex-col h-full">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 pl-1" x-text="currentStage === 1 ? 'Información General' : 'Documentación Etapa ' + currentStage"></h3>
                
                <div class="space-y-2 flex-1 overflow-y-auto custom-scrollbar pr-1 min-h-0">
                    
                    <!-- OPCIONES ETAPA 1 -->
                    <template x-if="currentStage === 1">
                        <div class="space-y-2">
                            <button @click="selectOption('empresa')" 
                                    class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200 group relative"
                                    :class="selectedOption === 'empresa' ? 'border-blue-600 bg-blue-50/20 dark:bg-blue-900/10' : 'border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                                <div class="flex items-center gap-3 relative z-10">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors shadow-sm shrink-0"
                                        :class="selectedOption === 'empresa' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'">
                                        <i class="bi bi-building text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-black uppercase tracking-tight truncate" :class="selectedOption === 'empresa' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-700 dark:text-slate-300'">Empresa</p>
                                        <span class="text-[9px] font-bold uppercase transition-colors" 
                                              :class="getRegistrationStatusColor(empresaData)"
                                              x-text="getRegistrationStatusText(empresaData)"></span>
                                    </div>
                                </div>
                            </button>

                            <button @click="selectOption('jefe')" 
                                    class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200 group relative"
                                    :class="selectedOption === 'jefe' ? 'border-emerald-600 bg-emerald-50/20 dark:bg-emerald-900/10' : 'border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                                <div class="flex items-center gap-3 relative z-10">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors shadow-sm shrink-0"
                                        :class="selectedOption === 'jefe' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'">
                                        <i class="bi bi-person-badge text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-black uppercase tracking-tight truncate" :class="selectedOption === 'jefe' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300'">Jefe Inmediato</p>
                                        <span class="text-[9px] font-bold uppercase transition-colors" 
                                              :class="getRegistrationStatusColor(jefeData)"
                                              x-text="getRegistrationStatusText(jefeData)"></span>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </template>

                    <!-- OPCIONES ETAPAS 2, 3, 4 (DOCUMENTOS) -->
                    <template x-if="[2, 3, 4].includes(currentStage)">
                        <div class="space-y-2">
                            <template x-for="docItem in currentStageDocs" :key="docItem.key">
                                <button @click="selectDocument(docItem.key)" 
                                        class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200 group relative"
                                        :class="selectedDocKey === docItem.key ? 'border-blue-600 bg-blue-50/20 dark:bg-blue-900/10' : 'border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                                    <div class="flex items-center gap-3 relative z-10">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors shadow-sm shrink-0"
                                            :class="selectedDocKey === docItem.key ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'">
                                            <i class="bi" :class="docItem.icon"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] font-black uppercase tracking-tight truncate" :class="selectedDocKey === docItem.key ? 'text-blue-600 dark:text-blue-400' : 'text-slate-700 dark:text-slate-300'" x-text="docItem.label"></p>
                                            <span class="text-[9px] font-bold uppercase" 
                                                :class="getStatusTextColor(docs[docItem.key]?.estado_archivo)"
                                                x-text="getDocStatusText(docs[docItem.key]?.estado_archivo)"></span>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </template>

                    <!-- OPCIÓN ETAPA 5 -->
                    <template x-if="currentStage === 5">
                        <div class="space-y-2">
                             <button class="w-full text-left p-4 rounded-xl border-2 border-emerald-600 bg-emerald-50/20 dark:bg-emerald-900/10 relative">
                                <div class="flex items-center gap-3 relative z-10">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-sm shrink-0">
                                        <i class="bi bi-award text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-black uppercase tracking-tight truncate text-emerald-600">Resultado Final</p>
                                        <span class="text-[9px] font-bold uppercase text-emerald-500">Evaluación</span>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-900/20">
                        <div class="flex gap-3">
                            <i class="bi bi-info-circle-fill text-blue-600 mt-0.5"></i>
                            <p class="text-[10px] text-blue-700 dark:text-blue-400 font-medium leading-relaxed">
                                Completa cada etapa para progresar. Los documentos serán revisados por tu docente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <input type="file" x-ref="fileInput" class="hidden" @change="handleFileSelect" accept=".pdf">
        </aside>

        <!-- ÁREA CENTRAL: VISUALIZADOR / FORMULARIOS -->
        <main class="flex-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden flex flex-col relative min-h-0"
             @dragover.prevent="dragOver = true"
             @dragleave.prevent="dragOver = false"
             @drop.prevent="handleDrop">
            
            <!-- OVERLAY DE CARGA -->
            <div x-show="verifying || loading" x-transition.opacity
                class="absolute inset-0 z-[60] flex flex-col items-center justify-center bg-blue-600/90 backdrop-blur-sm transition-all text-white">
                <div class="mb-4">
                    <i class="bi bi-arrow-repeat text-5xl animate-spin"></i>
                </div>
                <h4 class="text-xl font-black uppercase tracking-widest mb-1" x-text="verifying ? 'Verificando...' : 'Cargando...'"></h4>
                <p class="text-[10px] font-bold opacity-75 uppercase tracking-[0.3em] ">Procesando información</p>
            </div>

            <!-- VIEWER / CONTENT AREA -->
            <div class="flex-1 flex flex-col relative min-h-0 h-full">
                
                <!-- STAGE 1: FORMULARIOS EMPRESA / JEFE -->
                <template x-if="currentStage === 1">
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-8">
                        <div class="max-w-2xl mx-auto">
                            <!-- EMPRESA FORM -->
                            <div x-show="selectedOption === 'empresa'" class="animate-fade-in">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 flex items-center justify-center">
                                        <i class="bi bi-building text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Datos de la Empresa</h3>
                                        <p class="text-xs text-slate-500 font-medium">Registra o actualiza la información del lugar de prácticas.</p>
                                    </div>
                                </div>

                                <form action="{{ $empresa && $empresa->state == 3 ? route('empresa.edit', $empresa->id) : route('empresas.store', $practicas->id) }}" method="POST" class="space-y-6">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Razón Social</label>
                                            <input type="text" name="razon_social" value="{{ $empresa->razon_social ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-blue-500 transition-all outline-none" />
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">RUC</label>
                                            <input type="text" name="ruc" maxlength="11" value="{{ $empresa->ruc ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-blue-500 transition-all outline-none" />
                                        </div>
                                        <div>
                                             <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Teléfono</label>
                                             <input type="text" name="telefono" value="{{ $empresa->telefono ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-blue-500 transition-all outline-none" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Dirección</label>
                                            <input type="text" name="direccion" value="{{ $empresa->direccion ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-blue-500 transition-all outline-none" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Correo de Contacto</label>
                                            <input type="email" name="email" value="{{ $empresa->correo ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-blue-500 transition-all outline-none" />
                                        </div>
                                    </div>
                                    @if($empresa->state != 2)
                                    <div class="pt-4 flex justify-end">
                                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                                            Guardar Empresa
                                        </button>
                                    </div>
                                    @endif
                                </form>
                            </div>

                            <!-- JEFE FORM -->
                            <div x-show="selectedOption === 'jefe'" class="animate-fade-in">
                                <div class="flex items-center gap-3 mb-8">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center">
                                        <i class="bi bi-person-badge text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Jefe Inmediato</h3>
                                        <p class="text-xs text-slate-500 font-medium">Registra los datos de la persona a cargo de tu supervisión.</p>
                                    </div>
                                </div>

                                <form action="{{ $jefeInmediato && $jefeInmediato->state == 3 ? route('jefe_inmediato.edit', $jefeInmediato->id) : route('jefe_inmediato.store', $practicas->id) }}" method="POST" class="space-y-6">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nombres y Apellidos</label>
                                            <input type="text" name="name" value="{{ $jefeInmediato->nombres ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-emerald-500 transition-all outline-none" />
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">DNI</label>
                                            <input type="text" name="dni" maxlength="8" value="{{ $jefeInmediato->dni ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-emerald-500 transition-all outline-none" />
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cargo</label>
                                            <input type="text" name="cargo" value="{{ $jefeInmediato->cargo ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-emerald-500 transition-all outline-none" />
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Área / Departamento</label>
                                            <input type="text" name="area" value="{{ $jefeInmediato->area ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-emerald-500 transition-all outline-none" />
                                        </div>
                                        <div>
                                             <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Teléfono</label>
                                             <input type="text" name="telefono" maxlength="9" value="{{ $jefeInmediato->telefono ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-emerald-500 transition-all outline-none" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Correo Electrónico</label>
                                            <input type="email" name="email" value="{{ $jefeInmediato->correo ?? '' }}" required 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:border-emerald-500 transition-all outline-none" />
                                        </div>
                                    </div>
                                    <div class="pt-4 flex justify-end">
                                        <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                                            Guardar Jefe Inmediato
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- STAGES 2-4: PDF VIEWER / DROPZONE -->
                <template x-if="[2, 3, 4].includes(currentStage)">
                    <div class="flex-1 flex flex-col relative min-h-0 h-full">
                        <!-- DROPZONE INDICATOR -->
                        <div x-show="dragOver" x-transition.opacity
                            class="absolute inset-0 z-40 bg-blue-600/10 pointer-events-none border-4 border-dashed border-blue-500/50 m-4 rounded-xl flex items-center justify-center backdrop-blur-[2px]">
                            <i class="bi bi-cloud-arrow-up-fill text-5xl text-blue-600 animate-bounce"></i>
                        </div>

                        <!-- PDF IFRAME -->
                        <template x-if="filePreviewUrl">
                            <iframe :src="filePreviewUrl" class="w-full h-full border-none bg-slate-50 dark:bg-slate-950 flex-1 min-h-0 rounded-xl"></iframe>
                        </template>

                        <!-- EMPTY STATE DOC -->
                        <template x-if="!filePreviewUrl && !verifying && !loading">
                            <div class="flex-1 flex flex-col items-center justify-center p-12 text-center animate-fade-in min-h-0 cursor-pointer group"
                                @click="$refs.fileInput.click()">
                                <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] flex items-center justify-center mx-auto border-2 border-slate-100 dark:border-slate-800 group-hover:border-blue-300 transition-all">
                                    <i class="bi bi-file-earmark-arrow-up text-4xl text-slate-200 group-hover:text-blue-400 transition-colors"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-800 dark:text-white mt-6 mb-2 uppercase">Subir Documento</h4>
                                <p class="text-xs text-slate-400 font-medium italic mb-2 px-12">Arrastra tu archivo aquí o haz clic para buscarlo.</p>
                                <span class="px-4 py-1.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 text-[10px] font-black uppercase rounded-lg mt-4" x-text="selectedDocLabel"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- STAGE 5: RESULTADOS -->
                <template x-if="currentStage === 5">
                   <div class="flex-1 flex flex-col items-center justify-center p-12 text-center animate-fade-in">
                       <template x-if="practicaState > 5">
                            <div class="max-w-md">
                                <div class="w-24 h-24 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-emerald-500/10">
                                    <i class="bi bi-trophy-fill text-4xl"></i>
                                </div>
                                <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2 uppercase tracking-tight">¡Práctica Finalizada!</h3>
                                <p class="text-sm text-slate-500 mb-8 leading-relaxed">Has completado satisfactoriamente el proceso de prácticas pre-profesionales.</p>
                                
                                <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border-2 border-emerald-500 shadow-xl mb-6">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tu calificación obtenida</p>
                                    <span class="text-5xl font-black text-slate-800 dark:text-white" x-text="practicaCalificacion || '--'"></span>
                                </div>

                                <button @click="solicitarRevision()" class="text-[10px] font-black uppercase text-blue-600 hover:underline tracking-widest">
                                    ¿Deseas solicitar una revisión de nota?
                                </button>
                            </div>
                       </template>
                       <template x-if="practicaState <= 5">
                           <div class="max-w-md">
                                <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="bi bi-hourglass-split text-4xl text-slate-300 animate-spin-slow"></i>
                                </div>
                                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2 uppercase tracking-tight">Evaluación en proceso</h3>
                                <p class="text-sm text-slate-500 leading-relaxed italic">Tu docente está revisando toda tu documentación final. Recibirás tu nota en breve.</p>
                           </div>
                       </template>
                   </div>
                </template>
            </div>
        </main>

        <!-- PANEL DERECHO: INFO / ACCIONES / HISTORIAL -->
        <aside class="w-80 hidden xl:flex flex-col bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden shrink-0 h-full min-h-0 transition-all duration-300">
            <div class="flex flex-col h-full min-h-0 animate-fade-in">
                <!-- Tabs Minimalistas -->
                <div class="flex border-b border-slate-50 dark:border-white/5 shrink-0">
                    <button @click="docViewMode = 'info'; saveState()" 
                            class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest transition-all relative"
                            :class="docViewMode === 'info' ? 'text-blue-600 bg-blue-50/10' : 'text-slate-400 hover:text-slate-600'">
                        Información
                        <div x-show="docViewMode === 'info'" class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600"></div>
                    </button>
                    <button @click="docViewMode = 'history'; saveState()" 
                            class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest transition-all relative"
                            :class="docViewMode === 'history' ? 'text-blue-600 bg-blue-50/10' : 'text-slate-400 hover:text-slate-600'">
                        Historial
                        <div x-show="docViewMode === 'history'" class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600"></div>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-5 space-y-6 min-h-0">
                    
                    <!-- INFO MODE -->
                    <div x-show="docViewMode === 'info'" class="space-y-6 flex flex-col h-full">
                        
                        <!-- ACCIÓN DE ENVÍO (IF TEMP FILE) -->
                        <template x-if="tempFile">
                            <div class="space-y-4 animate-bounce-in shrink-0">
                                <div class="p-5 bg-blue-600 text-white rounded-xl shadow-xl shadow-blue-500/20 relative overflow-hidden group">
                                    <div class="relative z-10">
                                        <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-1 opacity-70">Nuevo Documento Listo</p>
                                        <p class="text-xs font-black truncate mb-4" x-text="tempFileName"></p>
                                        
                                        <div class="flex items-center gap-2">
                                            <button @click="uploadFile()" 
                                                    class="flex-1 py-2.5 bg-white text-blue-600 text-[10px] font-black uppercase rounded-lg hover:bg-slate-50 transition-all shadow-lg">
                                                Enviar Ahora
                                            </button>
                                            <button @click="clearTempFile()" 
                                                    class="p-2.5 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <i class="bi bi-send-fill absolute -right-4 -bottom-4 opacity-10 rotate-12 text-7xl"></i>
                                </div>
                            </div>
                        </template>

                        <!-- DETAILS CARD -->
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-white/5">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Resumen de Etapa</p>
                            
                            <template x-if="currentStage === 1">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="font-bold text-slate-400">Docente:</span>
                                        <span class="font-black text-slate-700 dark:text-slate-200">
                                            {{ $docente ? $docente->nombres . ' ' . $docente->apellidos : 'Pendiente' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="font-bold text-slate-400">Supervisor:</span>
                                        <span class="font-black text-slate-700 dark:text-slate-200">
                                            {{ $supervisor ? $supervisor->nombres . ' ' . $supervisor->apellidos : 'Pendiente' }}
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="[2, 3, 4].includes(currentStage)">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="font-bold text-slate-400">Estado:</span>
                                        <span class="text-[9px] font-black px-2 py-0.5 rounded uppercase" 
                                              :class="getStatusBadgeClass(docs[selectedDocKey]?.estado_archivo)"
                                              x-text="getDocStatusText(docs[selectedDocKey]?.estado_archivo)"></span>
                                    </div>
                                    <template x-if="docs[selectedDocKey]">
                                        <div class="flex justify-between items-center text-[10px]">
                                            <span class="font-bold text-slate-400">Actualizado:</span>
                                            <span class="font-black text-slate-700 dark:text-slate-200" x-text="new Date(docs[selectedDocKey].updated_at).toLocaleDateString()"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- OBSERVACIONES DEL DOCENTE -->
                        <template x-if="[2, 3, 4].includes(currentStage) && docs[selectedDocKey]?.estado_archivo === 'Corregir'">
                            <div class="p-5 bg-rose-50 dark:bg-rose-950/20 border-l-4 border-rose-500 rounded-xl space-y-4 animate-fade-in shadow-sm">
                                <div class="flex items-center gap-2 text-rose-600 font-black text-[10px] uppercase tracking-widest">
                                    <i class="bi bi-exclamation-octagon-fill"></i> Observación del Docente
                                </div>
                                <p class="text-[11px] font-bold text-rose-800 dark:text-rose-300 italic leading-relaxed" x-text="docs[selectedDocKey].comentario"></p>
                                
                                <button @click="$refs.fileInput.click()" 
                                        class="w-full py-3 bg-white border-2 border-rose-500 text-rose-600 text-[10px] font-black uppercase rounded-xl hover:bg-rose-50 transition-all flex items-center justify-center gap-2">
                                    <i class="bi bi-arrow-repeat"></i> Corregir Ahora
                                </button>
                            </div>
                        </template>

                        <!-- COMPLETED / APPROVED STATE -->
                        <template x-if="[2, 3, 4].includes(currentStage) && docs[selectedDocKey]?.estado_archivo === 'Aprobado'">
                            <div class="p-6 text-center bg-emerald-50 dark:bg-emerald-950/20 rounded-xl border border-emerald-100 dark:border-emerald-800/30">
                                <i class="bi bi-check-all text-4xl text-emerald-500 block mb-2"></i>
                                <p class="text-[11px] font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Documento Validado</p>
                            </div>
                        </template>

                    </div>

                    <!-- HISTORY MODE -->
                    <div x-show="docViewMode === 'history'" class="space-y-2">
                        <template x-if="[2, 3, 4].includes(currentStage)">
                           <div class="space-y-2">
                                <template x-for="(item, index) in currentDocHistory" :key="item.id">
                                    <button @click="filePreviewUrl = '/documento/' + item.ruta; tempFile = null" 
                                        class="w-full text-left p-4 bg-white dark:bg-slate-900 rounded-xl border-2 transition-all duration-300 relative group/item"
                                        :class="filePreviewUrl === '/documento/' + item.ruta ? 'border-blue-600 bg-blue-50/10' : 'border-transparent hover:bg-slate-50'">
                                         <div class="flex items-center justify-between">
                                             <div>
                                                <p class="text-[11px] font-black text-slate-800 dark:text-slate-200 mb-1" x-text="`Versión #${currentDocHistory.length - index}`"></p>
                                                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest" x-text="new Date(item.created_at).toLocaleString()"></p>
                                             </div>
                                             <i class="bi bi-chevron-right text-slate-300 group-hover/item:translate-x-1 transition-transform"></i>
                                         </div>
                                    </button>
                                </template>
                                <template x-if="currentDocHistory.length === 0">
                                    <div class="py-12 text-center">
                                        <i class="bi bi-clock-history text-4xl text-slate-100 dark:text-slate-800 block mb-3"></i>
                                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Sin historial de envíos</p>
                                     </div>
                                </template>
                           </div>
                        </template>
                        <template x-if="![2, 3, 4].includes(currentStage)">
                            <div class="py-12 text-center">
                                <i class="bi bi-info-circle text-4xl text-slate-100 dark:text-slate-800 block mb-3"></i>
                                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Historial no disponible para esta etapa</p>
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </aside>
    </div>
</div>

<script>
function studentPracticeManager() {
    return {
        currentStage: {{ $practicas->state > 5 ? 5 : $practicas->state }},
        practicaState: {{ $practicas->state }},
        practicaId: {{ $practicas->id }},
        id_ap: {{ $ap->id }},
        tipoPractica: '{{ $practicas->tipo_practica }}',
        practicaCalificacion: {{ $practicas->calificacion ?? 'null' }},
        
        steps: ['Registro', 'Inicio', 'Seguimiento', 'Cierre', 'Evaluación'],
        loading: false,
        verifying: false,
        dragOver: false,
        
        selectedOption: 'empresa', // Para etapa 1
        selectedDocKey: '', // Para etapas 2, 3, 4
        
        filePreviewUrl: null,
        tempFile: null,
        tempFileName: null,
        docViewMode: 'info',
        
        empresaData: @json($practicas->empresa),
        jefeData: @json($practicas->jefeInmediato),
        docs: {}, // All documents grouped by key
        history: {}, // All doc histories grouped by key
        
        storageKey: 'prac_est_state',

        async init() {
            this.restoreState();
            await this.loadAllDocs();
            this.updateAutoSelection();
        },

        saveState() {
            localStorage.setItem(this.storageKey, JSON.stringify({
                currentStage: this.currentStage,
                selectedOption: this.selectedOption,
                selectedDocKey: this.selectedDocKey,
                docViewMode: this.docViewMode
            }));
        },

        restoreState() {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    this.currentStage = data.currentStage || this.currentStage;
                    this.selectedOption = data.selectedOption || 'empresa';
                    this.selectedDocKey = data.selectedDocKey || '';
                    this.docViewMode = data.docViewMode || 'info';
                } catch (e) {}
            }
        },

        setStage(stage) {
            this.currentStage = stage;
            this.updateAutoSelection();
            this.saveState();
        },

        updateAutoSelection() {
            this.tempFile = null;
            this.filePreviewUrl = null;

            if (this.currentStage === 1) {
                if (!this.selectedOption) this.selectedOption = 'empresa';
            } else if ([2, 3, 4].includes(this.currentStage)) {
                const docs = this.currentStageDocs;
                if (docs.length > 0 && (!this.selectedDocKey || !docs.find(d => d.key === this.selectedDocKey))) {
                    this.selectedDocKey = docs[0].key;
                }
                this.updatePreview();
            }
        },

        selectOption(opt) {
            this.selectedOption = opt;
            this.saveState();
        },

        selectDocument(key) {
            this.selectedDocKey = key;
            this.tempFile = null;
            this.updatePreview();
            this.saveState();
        },

        updatePreview() {
            const doc = this.docs[this.selectedDocKey];
            if (doc) {
                this.filePreviewUrl = '/documento/' + doc.ruta;
            } else {
                this.filePreviewUrl = null;
            }
        },

        async loadAllDocs() {
            this.loading = true;
            try {
                // We could fetch them as needed, but for a smooth experience, load all
                const docKeys = [
                    'fut', 'carta_presentacion', 'carta_aceptacion', 
                    'plan_actividades_ppp', 'registro_actividades', 
                    'control_mensual_actividades', 'constancia_cumplimiento', 
                    'informe_final_ppp'
                ];
                
                await Promise.all(docKeys.map(async (key) => {
                    const res = await fetch(`/api/documento/${this.id_ap}/${key}`);
                    if (res.ok) {
                        const data = await res.json();
                        if (data && data.length > 0) {
                            this.docs[key] = data[0]; // Latest
                            this.history[key] = data; // All
                        }
                    }
                }));
            } catch (e) {
                console.error("Error loading docs", e);
            } finally {
                this.loading = false;
            }
        },

        get currentStageDocs() {
            const isDesarrollo = this.tipoPractica === 'desarrollo';
            if (this.currentStage === 2) {
                let docs = [
                    { key: 'fut', label: 'FUT', icon: 'bi-file-earmark-pdf' },
                    { key: 'carta_presentacion', label: 'Carta Presentación', icon: 'bi-file-earmark-text' }
                ];
                if (!isDesarrollo) docs.push({ key: 'carta_aceptacion', label: 'Carta Aceptación', icon: 'bi-check2-circle' });
                return docs;
            }
            if (this.currentStage === 3) {
                if (isDesarrollo) {
                    return [
                        { key: 'carta_aceptacion', label: 'Carta Aceptación', icon: 'bi-check2-circle' },
                        { key: 'plan_actividades_ppp', label: 'Plan Actividades', icon: 'bi-list-check' }
                    ];
                } else {
                    return [
                        { key: 'plan_actividades_ppp', label: 'Plan Actividades', icon: 'bi-list-check' },
                        { key: 'registro_actividades', label: 'Registro Actividades', icon: 'bi-clipboard-data' },
                        { key: 'control_mensual_actividades', label: 'Control Mensual', icon: 'bi-calendar-check' }
                    ];
                }
            }
            if (this.currentStage === 4) {
                return [
                    { key: 'constancia_cumplimiento', label: 'Constancia', icon: 'bi-award' },
                    { key: 'informe_final_ppp', label: 'Informe Final', icon: 'bi-book' }
                ];
            }
            return [];
        },

        get selectedDocLabel() {
            const found = this.currentStageDocs.find(d => d.key === this.selectedDocKey);
            return found ? found.label : 'Documento';
        },

        get currentDocHistory() {
            return this.history[this.selectedDocKey] || [];
        },

        // Drag & Drop
        handleDrop(e) {
            this.dragOver = false;
            const file = e.dataTransfer.files[0];
            this.processFile(file);
        },

        handleFileSelect(e) {
            const file = e.target.files[0];
            this.processFile(file);
        },

        async processFile(file) {
            if (!file || ![2, 3, 4].includes(this.currentStage)) return;
            
            this.verifying = true;
            await new Promise(r => setTimeout(r, 600));

            if (file.type !== 'application/pdf') {
                this.verifying = false;
                return Swal.fire('Formato Inválido', 'Solo se aceptan archivos PDF.', 'error');
            }
            if (file.size > 10 * 1024 * 1024) {
                this.verifying = false;
                return Swal.fire('Archivo muy pesado', 'El límite es de 10MB.', 'error');
            }

            this.tempFile = file;
            this.tempFileName = file.name;
            
            const reader = new FileReader();
            reader.onload = (e) => { this.filePreviewUrl = e.target.result; };
            reader.readAsDataURL(file);
            
            this.verifying = false;
            this.docViewMode = 'info';
        },

        clearTempFile() {
            this.tempFile = null;
            this.tempFileName = null;
            this.updatePreview();
        },

        async uploadFile() {
            if (!this.tempFile) return;
            this.loading = true;
            
            const fd = new FormData();
            fd.append('archivo', this.tempFile);
            fd.append('practica', this.practicaId);
            fd.append('tipo', this.selectedDocKey);
            fd.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route('subir.documento') }}', {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (res.ok) {
                    Swal.fire('¡Éxito!', 'Documento enviado correctamente.', 'success').then(() => window.location.reload());
                } else {
                    const err = await res.json();
                    Swal.fire('Error', err.message || 'Error al procesar el archivo', 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Fallo de conexión.', 'error');
            } finally {
                this.loading = false;
            }
        },

        solicitarRevision() {
            Swal.fire({
                title: 'Solicitar Revisión de Nota',
                input: 'textarea',
                inputLabel: 'Indica el motivo de tu solicitud',
                inputPlaceholder: 'Escribe aquí...',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Enviar Solicitud',
                preConfirm: (motivo) => {
                    if (!motivo) return Swal.showValidationMessage('Debes ingresar un motivo');
                    return motivo;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                     this.loading = true;
                     const fd = new FormData();
                     fd.append('id', this.practicaId);
                     fd.append('motivo', result.value);
                     fd.append('_token', '{{ csrf_token() }}');
                     
                     fetch('{{ route('solicitud_nota') }}', { method: 'POST', body: fd })
                        .then(r => r.ok ? Swal.fire('Enviado', 'Tu solicitud ha sido registrada.', 'success') : Swal.fire('Error', 'No se pudo enviar la solicitud.', 'error'))
                        .finally(() => this.loading = false);
                }
            });
        },

        // Helpers
        getRegistrationStatusText(data) {
            if (!data) return 'Pendiente';
            if (data.state == 1) return 'Aprobado';
            if (data.state == 2) return 'En Revisión';
            if (data.state == 3) return 'Observado';
            return 'Incompleto';
        },

        getRegistrationStatusColor(data) {
            if (!data) return 'text-slate-400';
            if (data.state == 1) return 'text-emerald-500 font-black';
            if (data.state == 2) return 'text-amber-500';
            if (data.state == 3) return 'text-rose-500 font-black';
            return 'text-slate-400';
        },

        getDocStatusText(val) {
            if (val == 1 || val === 'Aprobado') return 'Aprobado';
            if (val == 2 || val === 'Enviado' || val === 'En Revisión') return 'En Revisión';
            if (val == 3 || val === 'Corregir' || val === 'Observado') return 'Observado';
            return 'Pendiente';
        },

        getStatusTextColor(val) {
            if (val == 1 || val === 'Aprobado') return 'text-emerald-500 font-bold';
            if (val == 2 || val === 'Enviado' || val === 'En Revisión') return 'text-amber-500 font-bold';
            if (val == 3 || val === 'Corregir' || val === 'Observado') return 'text-rose-500 font-black animate-pulse';
            return 'text-slate-400 font-medium';
        },

        getStatusBadgeClass(val) {
            if (val == 1 || val === 'Aprobado') return 'bg-emerald-100 text-emerald-700';
            if (val == 2 || val === 'Enviado' || val === 'En Revisión') return 'bg-amber-100 text-amber-700';
            if (val == 3 || val === 'Corregir' || val === 'Observado') return 'bg-rose-100 text-rose-700';
            return 'bg-slate-100 text-slate-500';
        },

        getStatusDotClass(state) {
            if (state == 2) return 'bg-emerald-500';
            if (state == 1) return 'bg-amber-500';
            if (state == 0) return 'bg-rose-500 animate-pulse';
            return 'bg-slate-300';
        }
    };
}
</script>

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .animate-bounce-in { animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    @keyframes bounceIn { 0% { transform: scale(0.95); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
    @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-spin-slow { animation: spin-slow 3s linear infinite; }
</style>

@endsection