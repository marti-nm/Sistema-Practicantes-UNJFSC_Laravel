@extends('template')
@section('title', 'Mi Práctica Pre-Profesional')
@section('subtitle', 'Gestiona el avance de tus prácticas')

@php
    // Ensure persona is available
    if(!isset($persona)){
        $persona = auth()->user()->persona;
    }

    // Get Docente info if available
    $docente = null;
    // Check if relationship exists to avoid errors on null
    if($persona && $persona->gruposEstudiante) {
        $grupo = $persona->gruposEstudiante->first(); 
        if($grupo && $grupo->grupo && $grupo->grupo->docente) {
            $docente = $grupo->grupo->docente; 
        }
    }
    
    // Helper for stage 1 forms
    $empresa = $practicas->empresa ?? null;
    $jefeInmediato = $practicas->jefeInmediato ?? null;
    $empresaExiste = !is_null($empresa);
    $jefeExiste = !is_null($jefeInmediato);
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="studentPracticeLogic({{ $practicas->state ?? 1 }}, {{ $practicas->id_ap ?? 'null' }}, {{ $practicas->id }}, {{ $practicas->calificacion ?? 'null' }})">

    <!-- Header info -->
    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-emerald-500/30">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        @if($docente)
                            Docente: {{ $docente->nombres }} {{ $docente->apellidos }}
                        @else
                            Mi Práctica
                        @endif
                    </h2>
                    <div class="flex flex-wrap gap-3 mt-2">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-code-slash mr-1"></i> {{ $practicas->tipo_practica }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-clock-history mr-1"></i> Estado: {{ $practicas->estado_practica }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stepper -->
    <div class="mb-8 overflow-x-auto px-10 py-4">
        <div class="flex items-center justify-between min-w-[600px] relative">
            <!-- Line -->
            <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-200 dark:bg-slate-800 -z-10 -translate-y-1/2 rounded-full"></div>
            
            <!-- Steps -->
            <template x-for="(step, index) in steps" :key="index">
                <div class="relative flex flex-col items-center group cursor-pointer py-4"
                     @click="setStage(index + 1)">
                    
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-4 transition-all duration-300 z-10"
                         :class="{
                            'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-500/30 scale-110': currentStage === index + 1,
                            'bg-green-500 border-green-500 text-white': currentStage > index + 1,
                            'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-400': currentStage < index + 1
                         }">
                         <template x-if="currentStage > index + 1">
                            <i class="bi bi-check-lg"></i>
                         </template>
                         <template x-if="currentStage <= index + 1">
                            <span x-text="index + 1"></span>
                         </template>
                    </div>
                    
                    <span class="absolute top-12 mt-4 text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-colors duration-300"
                          :class="{
                            'text-blue-600 dark:text-blue-400': currentStage === index + 1,
                            'text-green-500': currentStage > index + 1,
                            'text-slate-400': currentStage < index + 1
                          }"
                          x-text="step"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Content Area -->
    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 overflow-hidden min-h-[400px] relative">
        
        <!-- Loading Overlay -->
        <div x-show="loading" 
             x-transition.opacity
             class="absolute inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <i class="bi bi-arrow-repeat animate-spin text-4xl text-blue-600"></i>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Cargando información...</span>
            </div>
        </div>

        <!-- STAGE 1: REGISTRO (Empresa y Jefe) -->
        <div x-show="currentStage === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                   <h3 class="text-lg font-black text-slate-800 dark:text-white">Registro de Información</h3>
                   <p class="text-sm text-slate-500 mt-1">Registra los datos de la empresa y tu jefe inmediato.</p>
                </div>
                <template x-if="practicaState > currentStage">
                    <div class="px-4 py-2 bg-green-50 text-green-600 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i> Etapa Completada
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Empresa Card -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center gap-3 mb-4 text-blue-600 dark:text-blue-400">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-building text-xl"></i>
                            <h4 class="font-bold text-sm uppercase tracking-wider">Empresa</h4>
                        </div>
                        <!-- Status Badge -->
                        <div x-html="getRegistrationStatus(empresaData)"></div>
                    </div>
                    
                    <template x-if="empresaData && empresaData.razon_social">
                        <dl class="space-y-4 text-sm">
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">Razón Social</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="empresaData.razon_social || 'N/A'"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">RUC</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="empresaData.ruc || 'N/A'"></dd>
                            </div>
                        </dl>
                    </template>
                    <template x-if="!empresaData || !empresaData.razon_social">
                        <div class="text-center py-8 text-slate-400">
                            <p class="text-xs mb-4">Debes registrar la empresa</p>
                            <button type="button" @click="openModal('modalEmpresa')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg text-xs uppercase tracking-wider shadow-lg shadow-blue-500/20 transition">
                                Registrar Empresa
                            </button>
                        </div>
                    </template>
                    <!-- Edit Button if exists and correctable -->
                    <template x-if="empresaData && empresaData.razon_social && (empresaData.state == 3 || empresaData.state == 1)">
                         <button type="button" @click="openModal('modalEmpresa')" class="mt-4 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold py-2 rounded-lg text-xs uppercase tracking-wider hover:bg-slate-50 transition">
                            <i class="bi bi-pencil-square mr-1"></i> Editar Información
                        </button>
                    </template>
                </div>

                <!-- Jefe Card -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border border-slate-100 dark:border-slate-800">
                     <div class="flex justify-between items-center gap-3 mb-4 text-emerald-600 dark:text-emerald-400">
                        <div class="flex items-center gap-2">
                        <i class="bi bi-person-badge text-xl"></i>
                        <h4 class="font-bold text-sm uppercase tracking-wider">Jefe Inmediato</h4>
                        </div>
                         <!-- Status Badge -->
                        <div x-html="getRegistrationStatus(jefeData)"></div>
                    </div>

                    <template x-if="jefeData && jefeData.nombres">
                        <dl class="space-y-4 text-sm">
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">Nombre</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="`${jefeData.nombres} ${jefeData.apellidos}`"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">Cargo</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="jefeData.cargo"></dd>
                            </div>
                        </dl>
                    </template>
                     <template x-if="!jefeData || !jefeData.nombres">
                        <div class="text-center py-8 text-slate-400">
                            <p class="text-xs mb-4">Debes registrar al jefe</p>
                            <button type="button" @click="openModal('modalJefeInmediato')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-lg text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/20 transition">
                                Registrar Jefe
                            </button>
                        </div>
                    </template>
                     <template x-if="jefeData && jefeData.nombres && (jefeData.state == 3 || jefeData.state == 1)">
                         <button type="button" @click="openModal('modalJefeInmediato')" class="mt-4 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold py-2 rounded-lg text-xs uppercase tracking-wider hover:bg-slate-50 transition">
                            <i class="bi bi-pencil-square mr-1"></i> Editar Información
                        </button>
                    </template>
                </div>
            </div>
            
             <!-- Next Step Button (Only if both approved or just to nudge) -->
             <div class="mt-8 text-center" x-show="empresaData && jefeData">
                <p class="text-xs text-slate-400 italic">Si ya registraste ambos, espera la aprobación de tu docente.</p>
             </div>
        </div>

        <!-- STAGE 2, 3, 4: GENERIC DOC UPLOADER -->
        <div x-show="[2, 3, 4].includes(currentStage)" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             class="p-8">
            
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white" x-text="getStageInfo(currentStage).title"></h3>
                    <p class="text-sm text-slate-500 mt-1" x-text="getStageInfo(currentStage).subtitle"></p>
                </div>
                 <template x-if="practicaState > currentStage">
                    <div class="px-4 py-2 bg-green-50 text-green-600 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i> Etapa Completada
                    </div>
                </template>
            </div>

            <div class="grid gap-6">
                 <template x-for="docItem in getStageInfo(currentStage).docs" :key="docItem.key">
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                        <!-- Left Info -->
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-2xl"
                                 :class="docItem.color">
                                <i class="bi" :class="docItem.icon"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white" x-text="docItem.label"></h4>
                                <template x-if="docs[docItem.key]">
                                     <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">Estado: 
                                        <span class="px-3 py-2 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2"
                                             :class="getDocStatus(docs[docItem.key]).wrapper">
                                            <i class="bi" :class="getDocStatus(docs[docItem.key]).icon"></i>
                                            <span x-text="getDocStatus(docs[docItem.key]).text"></span>
                                        </span>
                                     </p>
                                     <p x-show="docs[docItem.key].observacion" class="text-xs text-red-500 mt-1 italic">
                                         <i class="bi bi-exclamation-circle-fill mr-1"></i>
                                         Observación: <span x-text="docs[docItem.key].observacion"></span>
                                     </p>
                                </template>
                                <template x-if="!docs[docItem.key]">
                                    <p class="text-xs text-slate-400 mt-0.5 italic">Documento pendiente de envío</p>
                                </template>
                            </div>
                        </div>

                        <!-- Right Actions for Student -->
                        <div class="flex gap-2">
                            <!-- Helper template for access to the doc object -->
                            <template x-if="docs[docItem.key]">
                                <div class="flex gap-2">
                                     <!-- If Corregir (0) or Pendiente implied? No, create logic -->
                                    <template x-if="docs[docItem.key].state == 0">
                                        <button type="button" @click="openUploadModal(docItem.key, docItem.key)"
                                            class="w-full sm:w-auto px-3 py-2 rounded-xl bg-yellow-50 text-yellow-600 font-bold text-xs uppercase tracking-widest hover:bg-yellow-100 transition flex items-center justify-center gap-2">
                                            <i class="bi bi-pencil-square"></i> Corregir
                                        </button>
                                    </template>
                                    
                                    <!-- View PDF Button -->
                                    <a :href="`/documento/${docs[docItem.key].ruta}`" target="_blank" class="btn bg-white border border-slate-200 text-slate-600 hover:text-blue-600 text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg shadow-sm">
                                        <i class="bi bi-eye mr-1"></i> Ver
                                    </a>
                                </div>
                            </template>
                            
                            <template x-if="!docs[docItem.key]">
                                 <button type="button" @click="openUploadModal(docItem.key, docItem.key)" 
                                    class="w-full sm:w-auto px-4 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition flex items-center justify-center gap-2">
                                    <i class="bi bi-cloud-upload"></i> Subir
                                 </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- STAGE 5: RESULTADOS -->
        <div x-show="currentStage === 5" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-8">
             <div class="mb-8">
                <h3 class="text-lg font-black text-slate-800 dark:text-white">Resultado Final</h3>
                <p class="text-sm text-slate-500 mt-1">Calificación y estado final de tus prácticas.</p>
             </div>
            
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-8 text-center border border-slate-100 dark:border-slate-800">
                <template x-if="practicaState > 5">
                     <div>
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-4 animate-bounce">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <h4 class="text-xl font-black text-slate-800 dark:text-white mb-2">¡Felicitaciones!</h4>
                        <p class="text-slate-500 mb-6">Has completado exitosamente todas las etapas.</p>
                        
                        <div class="inline-block bg-white dark:bg-slate-900 px-8 py-4 rounded-xl shadow-lg mb-8">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Nota Final</span>
                            <span class="text-4xl font-black text-blue-600" x-text="calificacionData || 'NA'"></span>
                        </div>

                         <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <!-- Request Edit Button -->
                             <button type="button" @click="openRequestEditModal()"
                                class="px-6 py-3 rounded-xl bg-orange-50 text-orange-600 font-bold text-xs uppercase tracking-widest hover:bg-orange-100 transition shadow-sm border border-orange-100">
                                <i class="bi bi-pencil-square mr-2"></i> Solicitar Revisión
                            </button>
                         </div>
                    </div>
                </template>
                 <template x-if="practicaState <= 5">
                    <div class="text-slate-500 italic">
                        <i class="bi bi-hourglass-split mb-2 text-2xl block"></i>
                        Esperando calificación del docente...
                    </div>
                </template>
            </div>
        </div>

    </div>

    <!-- UPLOAD MODAL (Student) -->
    <div x-show="uploadModalOpen" class="fixed inset-0 z-[1060] overflow-y-auto" aria-labelledby="modal-upload-title" role="dialog" aria-modal="true" x-cloak>
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="uploadModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
             <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all w-full max-w-lg border border-slate-100 dark:border-slate-700">
                 <form action="{{ route('subir.documento') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="practica" value="{{ $practicas->id }}">
                    <input type="hidden" name="tipo" x-model="uploadFileType">
                    
                    <div class="bg-blue-600 px-6 py-6">
                        <h3 class="text-lg font-black text-white" id="modal-upload-title">Subir Documento</h3>
                        <p class="text-blue-100 text-xs mt-1">Selecciona el archivo PDF correspondiente.</p>
                    </div>

                    <div class="p-6">
                         <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Archivo (PDF, Max 10MB)</label>
                            <input type="file" name="archivo" accept="application/pdf" required
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-xl file:border-0
                                file:text-xs file:font-bold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                transition-all"
                            />
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-900/80 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition-all">
                            <i class="bi bi-cloud-upload mr-2"></i> Subir Archivo
                        </button>
                        <button type="button" @click="uploadModalOpen = false" class="w-full inline-flex justify-center items-center rounded-xl bg-white border border-slate-200 text-slate-500 px-4 py-3 text-sm font-bold hover:bg-slate-50 transition-all">
                            Cancelar
                        </button>
                    </div>
                </form>
             </div>
        </div>
    </div>
    
     <!-- MODAL SOLICITAR EDICIÓN (Student) -->
    <div x-show="requestEditModalOpen" class="fixed inset-0 z-[1070] overflow-y-auto" role="dialog" aria-modal="true" x-cloak>
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="requestEditModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all w-full max-w-lg border border-slate-100 dark:border-slate-700">
                <form action="{{ route('solicitud_nota') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $practicas->id }}">
                    <div class="bg-orange-500 px-6 py-6">
                         <h3 class="text-lg font-black text-white">Solicitar Revisión de Nota</h3>
                    </div>
                    <div class="p-6">
                        <label class="block text-xs font-bold text-slate-500 mb-2 uppercase">Motivo</label>
                        <textarea name="motivo" required rows="3" class="w-full border border-slate-200 rounded-xl p-3 text-sm"></textarea>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="w-full rounded-xl bg-orange-500 text-white font-bold py-3 text-sm hover:bg-orange-600">Enviar</button>
                        <button type="button" @click="requestEditModalOpen = false" class="w-full rounded-xl bg-white border text-slate-500 font-bold py-3 text-sm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EMPRESA (Alpine.js + Tailwind) -->
    <div x-show="empresaModalOpen" 
         class="fixed inset-0 z-[1060] overflow-y-auto" 
         aria-labelledby="modal-empresa-title" 
         role="dialog" 
         aria-modal="true" 
         x-cloak>
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="empresaModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all w-full max-w-2xl border border-slate-100 dark:border-slate-700">
                <form action="{{ $empresaExiste && $empresa && $empresa->state == 3 ? route('empresa.edit', $empresa->id) : route('empresas.store', $practicas->id) }}" method="POST">
                    @csrf
                    <div class="bg-blue-600 px-6 py-6">
                        <h3 class="text-lg font-black text-white flex items-center gap-2" id="modal-empresa-title">
                            <i class="bi bi-building text-xl"></i> Datos de la Empresa
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Nombre de la Empresa</label>
                            <input type="text" name="empresa" value="{{ $empresa->nombre ?? '' }}" required
                                class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">RUC</label>
                                <input type="text" name="ruc" maxlength="11" value="{{ $empresa->ruc ?? '' }}" required
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Razón Social</label>
                                <input type="text" name="razon_social" value="{{ $empresa->razon_social ?? '' }}" required
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Dirección</label>
                            <input type="text" name="direccion" value="{{ $empresa->direccion ?? '' }}" required
                                class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Teléfono</label>
                                <input type="text" name="telefono" value="{{ $empresa->telefono ?? '' }}" required
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Email</label>
                                <input type="email" name="email" value="{{ $empresa->correo ?? '' }}" required
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Sitio Web (Opcional)</label>
                            <input type="url" name="sitio_web" value="{{ $empresa->web ?? '' }}"
                                class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-900/80 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-blue-600 hover:bg-blue-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition-all">
                            <i class="bi bi-check-circle mr-2"></i> Guardar Empresa
                        </button>
                        <button type="button" @click="empresaModalOpen = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 px-6 py-3 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL JEFE INMEDIATO (Alpine.js + Tailwind) -->
    <div x-show="jefeModalOpen" 
         class="fixed inset-0 z-[1060] overflow-y-auto" 
         aria-labelledby="modal-jefe-title" 
         role="dialog" 
         aria-modal="true" 
         x-cloak>
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="jefeModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all w-full max-w-2xl border border-slate-100 dark:border-slate-700">
                <form action="{{ $jefeExiste && $jefeInmediato && $jefeInmediato->state == 3 ? route('jefe_inmediato.edit', $jefeInmediato->id) : route('jefe_inmediato.store', $practicas->id) }}" method="POST">
                    @csrf
                    <div class="bg-emerald-600 px-6 py-6">
                        <h3 class="text-lg font-black text-white flex items-center gap-2" id="modal-jefe-title">
                            <i class="bi bi-person-badge text-xl"></i> Datos del Jefe Inmediato
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Nombres y Apellidos</label>
                            <input type="text" name="name" value="{{ $jefeInmediato->nombres ?? '' }}" required
                                class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">DNI</label>
                                <input type="text" name="dni" maxlength="8" value="{{ $jefeInmediato->dni ?? '' }}" required
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Cargo</label>
                                <input type="text" name="cargo" value="{{ $jefeInmediato->cargo ?? '' }}" required
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Área</label>
                                <input type="text" name="area" value="{{ $jefeInmediato->area ?? '' }}" required
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Teléfono</label>
                                <input type="text" name="telefono" maxlength="9" value="{{ $jefeInmediato->telefono ?? '' }}" required
                                    class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Email</label>
                            <input type="email" name="email" value="{{ $jefeInmediato->correo ?? '' }}" required
                                class="w-full border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-slate-900/80 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-emerald-600 hover:bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-500/30 transition-all">
                            <i class="bi bi-check-circle mr-2"></i> Guardar Jefe
                        </button>
                        <button type="button" @click="jefeModalOpen = false" class="w-full sm:w-auto inline-flex justify-center items-center rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 px-6 py-3 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>

<script>
    function studentPracticeLogic(initialState, id_ap, practicaId, initialGrade) {
        return {
            currentStage: initialState > 5 ? 5 : initialState, 
            practicaState: initialState,
            steps: ['Registro', 'Desarrollo', 'Seguimiento', 'Cierre', 'Evaluación'],
            loading: false,
            calificacionData: initialGrade,

            // Data
            empresaData: null,
            jefeData: null,
            docs: {
                fut: null,
                carta1: null,
                doc_seg1: null,
                doc_seg2: null,
                constancia: null,
                informe: null,
                carta_aceptacion: null,
                plan_actividades: null,
                registro_actividades: null,
                control_actividades: null
            },
            
            // UI State
            uploadModalOpen: false,
            requestEditModalOpen: false,
            empresaModalOpen: false,
            jefeModalOpen: false,
            uploadFileType: '',

            async init() {
                this.loadStageData(this.currentStage);
            },
            
            setStage(stage) {
                if (stage > this.practicaState && stage > 1) {
                    // Optional block
                }
                this.currentStage = stage;
                this.loadStageData(stage);
            },
            
            openUploadModal(type, key) {
                this.uploadFileType = type; // Map key to DB type if needed, assume they match or using aliases
                // Actually the API expects specific strings.
                // Step 98 in Admin view used: fut, carta_presentacion, carta_aceptacion etc.
                // In Admin view we loaded them into specific keys.
                // FOR STUDENT UPLOAD: The select form usually sends 'tipo'.
                // I need to ensure the 'type' I pass here matches what 'subir.documento' expects (FUT, Carta, etc).
                
                // Mapping keys to Upload Types (legacy or new)
                // Let's rely on the key being the type for now, or use a map.
                const map = {
                    'fut': 'fut',
                    'carta1': 'carta_presentacion', // Default
                    'carta_aceptacion': 'carta_aceptacion',
                    'plan_actividades': 'plan_actividades_ppp',
                    'registro_actividades': 'registro_actividades',
                    'control_actividades': 'control_mensual_actividades',
                    'constancia': 'constancia_cumplimiento',
                    'informe': 'informe_final_ppp',
                     // Legacy mapping for Development if 'carta1' is used generic
                     'doc_seg1': 'carta_aceptacion', // Dev
                     'doc_seg2': 'plan_actividades_ppp' // Dev
                };
                
                // Better strategy: Use the exact type key from the DOC constant in Controller or just use the DB value.
                // The keys in available docs loop (from `getStageInfo`) act as the `type` unless overridden.
                
                // Let's use a smart mapper based on practice type.
                const isDesarrollo = '{{ $practicas->tipo_practica }}' === 'desarrollo';
                let realType = type;
                
                if(key === 'carta1' && isDesarrollo) realType = 'carta_presentacion';
                else if(key === 'carta1' && !isDesarrollo) realType = 'carta_aceptacion'; // Only if S2 Conval uses carta1 alias? No, I separated them.
                else if (key === 'doc_seg1' && isDesarrollo) realType = 'carta_aceptacion';
                else if (key === 'doc_seg2' && isDesarrollo) realType = 'plan_actividades_ppp';
                
                // Explicit new keys
                if(key === 'plan_actividades') realType = 'plan_actividades_ppp';
                if(key === 'control_actividades') realType = 'control_mensual_actividades';
                if(key === 'constancia') realType = 'constancia_cumplimiento';
                if(key === 'informe') realType = 'informe_final_ppp';
                
                this.uploadFileType = realType;
                this.uploadModalOpen = true;
            },
            
            openModal(id) {
                // Use Alpine.js state instead of Bootstrap
                if(id === 'modalEmpresa') {
                    this.empresaModalOpen = true;
                } else if(id === 'modalJefeInmediato') {
                    this.jefeModalOpen = true;
                }
            },
            
            openRequestEditModal() {
                 this.requestEditModalOpen = true;
            },

            async loadStageData(stage) {
                this.loading = true;
                try {
                     if(stage === 1 && !this.empresaData) {
                         const res = await fetch(`/api/empresa/${practicaId}`);
                         if(res.ok) this.empresaData = await res.json();
                         
                         const res2 = await fetch(`/api/jefeinmediato/${practicaId}`);
                         if(res2.ok) this.jefeData = await res2.json();
                     }
                     
                     if(stage >= 2) {
                        const loadDoc = async (type, key) => {
                             const r = await fetch(`/api/documento/${id_ap}/${type}`);
                             if(r.ok) {
                                 const d = await r.json();
                                 if(d && d.length > 0) this.docs[key] = d[0];
                             }
                        };
                        
                        await loadDoc('fut', 'fut');
                        await loadDoc('carta_presentacion', 'carta1'); 
                        await loadDoc('carta_aceptacion', 'carta_aceptacion');
                        await loadDoc('plan_actividades_ppp', 'plan_actividades');
                        await loadDoc('registro_actividades', 'registro_actividades');
                        await loadDoc('control_actividades', 'control_actividades');
                        
                        const isDesarrollo = '{{ $practicas->tipo_practica }}' === 'desarrollo';
                        if(isDesarrollo) {
                             await loadDoc('carta_aceptacion', 'doc_seg1');
                             await loadDoc('plan_actividades_ppp', 'doc_seg2');
                        }
                        
                        await loadDoc('constancia_cumplimiento', 'constancia');
                        await loadDoc('informe_final_ppp', 'informe');
                     }
                } catch(e) {
                    console.error("Error loading data", e);
                } finally {
                    this.loading = false;
                }
            },
            
            getStageInfo(stage) {
                const isDesarrollo = '{{ $practicas->tipo_practica }}' === 'desarrollo';
                
                if(stage === 2) {
                    let docs = [
                        { key: 'fut', label: 'Formulario Único de Trámite (FUT)', icon: 'bi-file-earmark-pdf', color: 'bg-red-100 text-red-600' },
                        { key: 'carta1', label: 'Carta de Presentación', icon: 'bi-file-earmark-text', color: 'bg-indigo-100 text-indigo-600' }
                    ];
                    if (!isDesarrollo) {
                         docs.push({ key: 'carta_aceptacion', label: 'Carta de Aceptación', icon: 'bi-check2-circle', color: 'bg-teal-100 text-teal-600' });
                    }
                    return {
                        title: 'Documentación Inicial',
                        subtitle: 'Sube los documentos requeridos.',
                        docs: docs
                    };
                }
                if(stage === 3) {
                     let docs = [];
                     if (isDesarrollo) {
                         docs = [
                            { key: 'doc_seg1', label: 'Carta de Aceptación', icon: 'bi-check2-circle', color: 'bg-teal-100 text-teal-600' },
                            { key: 'doc_seg2', label: 'Plan de Actividades', icon: 'bi-list-check', color: 'bg-orange-100 text-orange-600' }
                         ];
                     } else {
                         docs = [
                            { key: 'plan_actividades', label: 'Plan de Actividades', icon: 'bi-list-check', color: 'bg-orange-100 text-orange-600' },
                            { key: 'registro_actividades', label: 'Registro de Actividades', icon: 'bi-clipboard-data', color: 'bg-blue-100 text-blue-600' },
                            { key: 'control_actividades', label: 'Control Mensual de Actividades', icon: 'bi-calendar-check', color: 'bg-purple-100 text-purple-600' }
                         ];
                     }
                     return {
                        title: 'Documentación de Seguimiento',
                        subtitle: 'Sube tu plan y documentos de control.',
                        docs: docs
                    };
                }
                if(stage === 4) {
                     return {
                        title: 'Cierre de Prácticas',
                        subtitle: 'Sube tus informes finales y constancias.',
                        docs: [
                             { key: 'constancia', label: 'Constancia de Cumplimiento', icon: 'bi-award', color: 'bg-pink-100 text-pink-600' },
                             { key: 'informe', label: 'Informe Final PPP', icon: 'bi-book', color: 'bg-violet-100 text-violet-600' }
                        ]
                    };
                }
                return { title: '', subtitle: '', docs: [] };
            },
            
            getDocStatus(doc) {
                const state = doc ? doc.state : null;
                if (state == 2) {
                    return { wrapper: 'bg-green-50 text-green-600', icon: 'bi-check-circle-fill', text: 'Aprobado' };
                }
                if (state == 0) {
                     return { wrapper: 'bg-yellow-50 text-yellow-600', icon: 'bi-exclamation-triangle-fill', text: 'Observado' };
                }
                if (state == 1) {
                     return { wrapper: 'bg-blue-50 text-blue-600', icon: 'bi-hourglass-split', text: 'En Revisión' };
                }
                return { wrapper: 'bg-gray-100 text-gray-600', icon: 'bi-circle-fill', text: 'Pendiente' };
            },
            
            getRegistrationStatus(data) {
                if(!data) return `<span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs font-bold uppercase">Pendiente</span>`;
                if(data.state == 1) return `<span class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-lg text-xs font-bold uppercase">En Revisión</span>`;
                if(data.state == 2) return `<span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-xs font-bold uppercase">Aprobado</span>`;
                if(data.state == 3) return `<span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-bold uppercase">Observado</span>`;
                return '';
            }
        }
    }
</script>
@endsection