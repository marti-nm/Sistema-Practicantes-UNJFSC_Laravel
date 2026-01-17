@extends('template')
@section('title', 'Supervisión de Práctica')
@section('subtitle', 'Revisión y seguimiento del estudiante')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="supervisionLogic({{ $practicaData->state ?? 1 }}, {{ $practicaData->id }}, {{ $practicaData->calificacion ?? 'null' }})">

    <!-- Information Header -->
    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-sm border-1 border-slate-200 dark:border-slate-800 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-blue-500/30">
                    {{ substr($estudiante->nombres, 0, 1) }}{{ substr($estudiante->apellidos, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $estudiante->nombres }} {{ $estudiante->apellidos }}
                    </h2>
                    <div class="flex flex-wrap gap-3 mt-2">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-upc-scan mr-1"></i> {{ $estudiante->codigo }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-briefcase mr-1"></i> {{ $practicaData->tipo_practica }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-building mr-1"></i> {{ $escuela->name }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3">
                 <a href="{{ route('supervision') }}" 
                   class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    <i class="bi bi-arrow-left mr-2"></i> Volver
                </a>
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
                <div class="relative flex flex-col items-center group cursor-pointer"
                     @click="setStage(index + 1)">
                    
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-4 transition-all duration-300 z-10"
                         :class="{
                            'bg-blue-600 border-blue-600 text-white shadow-lg shadow-blue-500/30 scale-110': currentStage === index + 1,
                            'bg-green-500 border-green-500 text-white': currentStage > index + 1,
                            'bg-slate-200 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-400': currentStage < index + 1
                         }">
                         <template x-if="currentStage > index + 1">
                            <i class="bi bi-check-lg"></i>
                         </template>
                         <template x-if="currentStage <= index + 1">
                            <span x-text="index + 1"></span>
                         </template>
                    </div>
                    
                    <span class="absolute top-12 text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-colors duration-300"
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
    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-xl border-1 border-slate-100 dark:border-slate-800 overflow-hidden min-h-[400px] relative">
        
        <!-- Loading Overlay -->
        <div x-show="loading" 
             x-transition.opacity
             class="absolute inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm z-50 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <i class="bi bi-arrow-repeat animate-spin text-4xl text-blue-600"></i>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Cargando información...</span>
            </div>
        </div>

        <!-- STAGE 1: INICIO (Datos de Empresa y Jefe) -->
        <div x-show="currentStage === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                   <h3 class="text-lg font-black text-slate-800 dark:text-white">Datos de la Empresa y Jefe Inmediato</h3>
                   <p class="text-sm text-slate-500 mt-1">Verifica la información registrada por el estudiante.</p>
                </div>
                <div class="px-3 py-1 bg-yellow-50 text-yellow-600 rounded-lg text-xs font-bold border border-yellow-100" x-show="!empresaData">
                    Pendiente de Registro
                </div>
                <template x-if="practicaState > currentStage">
                    <div class="px-4 py-2 bg-green-50 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i> Etapa Completada
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Empresa Card -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border-1 border-slate-200 dark:border-slate-800">
                    <div class="flex justify-between items-center gap-3 mb-4 text-blue-600 dark:text-blue-400">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-building text-xl"></i>
                            <h4 class="font-bold text-sm uppercase tracking-wider">Empresa</h4>
                        </div>
                        <template x-if="empresaData.state == 1">
                            <div class="flex gap-2">
                                <button type="button" @click="openActionModal('empresa', empresaData.id, 'Corregir')"
                                    class="w-full sm:w-auto px-3 py-2 rounded-xl bg-red-50 text-red-600 font-bold text-xs uppercase tracking-widest hover:bg-red-100 hover:text-red-700 transition flex items-center justify-center gap-2">
                                    <i class="bi bi-x-circle"></i> Observar
                                </button>

                                <button type="button" @click="openActionModal('empresa', empresaData.id, 'Aprobado')"
                                    class="w-full sm:w-auto px-3 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition flex items-center justify-center gap-2">
                                    <i class="bi bi-check-lg"></i> Aprobar
                                </button>
                            </div>
                        </template>
                        <template x-if="empresaData.state == 2">
                            <div class="px-4 py-2 bg-green-50 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> Aprobado
                            </div>
                        </template>
                        <template x-if="empresaData.state == 3">
                            <div class="px-4 py-2 bg-yellow-50 dark:bg-yellow-800 text-yellow-600 dark:text-yellow-400 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> Enviado a correción
                            </div>
                        </template>
                    </div>
                    
                    <template x-if="empresaData">
                        <dl class="space-y-4 text-sm">
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">Razón Social</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="empresaData.razon_social || 'N/A'"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">RUC</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="empresaData.ruc || 'N/A'"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">Dirección</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="empresaData.direccion || 'N/A'"></dd>
                            </div>
                        </dl>
                    </template>
                    <template x-if="!empresaData">
                        <div class="text-center py-8 text-slate-400">
                            <i class="bi bi-exclamation-circle text-2xl mb-2 block"></i>
                            <span class="text-xs">No hay datos de empresa</span>
                        </div>
                    </template>
                </div>

                <!-- Jefe Card -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-6 border-1 border-slate-200 dark:border-slate-800">
                     <div class="flex justify-between items-center gap-3 mb-4 text-emerald-600 dark:text-emerald-400">
                        <div class="flex items-center gap-2">
                        <i class="bi bi-person-badge text-xl"></i>
                        <h4 class="font-bold text-sm uppercase tracking-wider">Jefe Inmediato</h4>
                        </div>
                        <template x-if="jefeData.state == 1">
                            <div class="flex gap-2">
                                <button type="button" @click="openActionModal('jefe', jefeData.id, 'Corregir')"
                                    class="w-full sm:w-auto px-3 py-2 rounded-xl bg-red-50 text-red-600 font-bold text-xs uppercase tracking-widest hover:bg-red-100 hover:text-red-700 transition flex items-center justify-center gap-2">
                                    <i class="bi bi-x-circle"></i> Observar
                                </button>

                                <button type="button" @click="openActionModal('jefe', jefeData.id, 'Aprobado')"
                                    class="w-full sm:w-auto px-3 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition flex items-center justify-center gap-2">
                                    <i class="bi bi-check-lg"></i> Aprobar
                                </button>
                            </div>
                        </template>
                        <template x-if="jefeData.state == 2">
                            <div class="px-4 py-2 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> Aprobado
                            </div>
                        </template>
                        <template x-if="jefeData.state == 3">
                            <div class="px-4 py-2 bg-yellow-50 dark:bg-yellow-800 text-yellow-600 dark:text-yellow-400 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                                <i class="bi bi-check-circle-fill"></i> Enviado a correción
                            </div>
                        </template>
                    </div>

                    <template x-if="jefeData">
                        <dl class="space-y-4 text-sm">
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">Nombre Completo</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="`${jefeData.grado_academico || ''} ${jefeData.nombres || ''} ${jefeData.apellidos || ''}`"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">Cargo / Área</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1" x-text="`${jefeData.cargo || '-'} / ${jefeData.area || '-'}`"></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold text-slate-400 uppercase">Contacto</dt>
                                <dd class="font-semibold text-slate-700 dark:text-slate-200 mt-1 space-y-1">
                                    <div x-text="jefeData.telefono"></div>
                                    <div x-text="jefeData.correo" class="text-blue-500"></div>
                                </dd>
                            </div>
                        </dl>
                    </template>
                     <template x-if="!jefeData">
                        <div class="text-center py-8 text-slate-400">
                            <i class="bi bi-exclamation-circle text-2xl mb-2 block"></i>
                            <span class="text-xs">No hay datos del jefe</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- STAGE 2, 3, 4: DOCUMENT LIST GENERIC VIEWER -->
        <div x-show="[2, 3, 4].includes(currentStage)" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0" 
             class="p-8">
            
            <!-- Dynamic Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white" x-text="getStageInfo(currentStage).title"></h3>
                    <p class="text-sm text-slate-500 mt-1" x-text="getStageInfo(currentStage).subtitle"></p>
                </div>
                <template x-if="practicaState > currentStage">
                     <div class="px-4 py-2 bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                         <i class="bi bi-check-circle-fill"></i> Etapa Completada
                     </div>
                 </template>
            </div>

            <div class="grid gap-6">
                <template x-for="docItem in getStageInfo(currentStage).docs" :key="docItem.key">
                    <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border-1 border-slate-100 dark:border-slate-800">
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
                                </template>
                                <template x-if="!docs[docItem.key]">
                                    <p class="text-xs text-slate-400 mt-0.5 italic">Documento pendiente de subida</p>
                                </template>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex gap-2">
                            <!-- Helper template for access to the doc object -->
                            <template x-if="docs[docItem.key]">
                                <div class="flex gap-2">
                                     <!-- Actions for Pending/Correcting State -->
                                    <template x-if="docs[docItem.key].state == 1">
                                        <div class="flex gap-2">
                                            <button type="button" @click="openActionModal('archivo', docs[docItem.key].id, 'Corregir')"
                                                class="w-full sm:w-auto px-3 py-2 rounded-xl bg-red-50 text-red-600 font-bold text-xs uppercase tracking-widest hover:bg-red-100 hover:text-red-700 transition flex items-center justify-center gap-2">
                                                <i class="bi bi-x-circle"></i> Observar
                                            </button>
                                            <button type="button" @click="openActionModal('archivo', docs[docItem.key].id, 'Aprobado')"
                                                class="w-full sm:w-auto px-3 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition flex items-center justify-center gap-2">
                                                <i class="bi bi-check-lg"></i> Aprobar
                                            </button>
                                        </div>
                                    </template>
                                    
                                    <!-- View PDF Button -->
                                    <a :href="`/documento/${docs[docItem.key].ruta}`" target="_blank" class="btn bg-slate-50 dark:bg-slate-900/50 border-1 border-slate-200 dark:border-slate-900 text-slate-600 dark:text-white hover:text-blue-600 text-sm font-bold uppercase tracking-wider px-4 py-2 rounded-lg shadow-sm">
                                        <i class="bi bi-eye mr-1"></i> Ver PDF
                                    </a>
                                </div>
                            </template>
                            
                            <template x-if="!docs[docItem.key]">
                                 <span class="text-xs font-bold text-slate-400 italic px-4 py-2">No subido</span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- STAGE 5: EVALUACION -->
         <div x-show="currentStage === 5" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="p-8">
             <div class="mb-8">
               <h3 class="text-lg font-black text-slate-800 dark:text-white">Evaluación Final</h3>
               <p class="text-sm text-slate-500 mt-1">Calificación y cierre del proceso.</p>
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-8 text-center border-1 border-slate-200 dark:border-slate-800">
                <template x-if="practicaState > 5">
                     <div>
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <h4 class="text-xl font-black text-slate-800 dark:text-white mb-2">¡Práctica Aprobada!</h4>
                        <p class="text-slate-500 mb-6">El estudiante ha completado satisfactoriamente el proceso.</p>
                        
                        <div class="inline-block bg-white dark:bg-slate-900 px-8 py-4 rounded-xl shadow-lg mb-8">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Calificación Final</span>
                            <span class="text-4xl font-black text-blue-600" x-text="calificacionData || 'NA'"></span>
                        </div>

                        <!-- Actions for Graded State -->
                         <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <!-- Button for Student/Supervisor to Request Edit -->
                             <button type="button" @click="openRequestEditModal()"
                                class="px-6 py-3 rounded-xl bg-orange-50 text-orange-600 font-bold text-xs uppercase tracking-widest hover:bg-orange-100 transition shadow-sm border border-orange-100">
                                <i class="bi bi-pencil-square mr-2"></i> Solicitar Edición
                            </button>

                            <!-- Button for Admin to Review Request (Only if Role 1 or 2, usually checked by blade or JS) -->
                            @if(Auth::user()->hasAnyRoles([1, 2]))
                                <div x-show="hasPendingRequest" class="relative">
                                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    </span>
                                    <button type="button" @click="openAdminReviewModal()"
                                        class="px-6 py-3 rounded-xl bg-blue-600 text-white font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                                        <i class="bi bi-shield-check mr-2"></i> Revisar Solicitud
                                    </button>
                                </div>
                            @endif
                         </div>

                        <!-- Status Alert -->
                         <div x-show="hasPendingRequest" class="max-w-md mx-auto mt-6 bg-yellow-50 text-yellow-800 px-4 py-3 rounded-lg text-sm flex items-center gap-3 border border-yellow-100">
                            <i class="bi bi-info-circle-fill text-yellow-500"></i>
                            <span>Hay una solicitud de modificación pendiente de aprobación.</span>
                         </div>
                    </div>
                </template>
                
                <template x-if="practicaState <= 5">
                    <form action="{{ route('practica.calificar') }}" method="POST" class="max-w-md mx-auto">
                        @csrf
                        <input type="hidden" name="practica_id" value="{{ $practicaData->id }}">
                        
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Asignar Calificación (0 - 20)</label>
                            <input type="number" name="calificacion" min="0" max="20" step="0.01" required
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-center text-xl font-bold focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-widest py-4 rounded-xl shadow-lg shadow-blue-600/20 transition-all">
                            Finalizar y Calificar
                        </button>
                    </form>
                </template>
            </div>
        </div>

    </div>
    
    <!-- MODAL SOLICITAR EDICIÓN (Student/Supervisor) -->
    <div x-show="requestEditModalOpen" 
         class="fixed inset-0 z-[1070] overflow-y-auto" 
         aria-labelledby="modal-request-title" role="dialog" aria-modal="true" x-cloak>
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" 
             x-show="requestEditModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             @click="requestEditModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="requestEditModalOpen"
                 x-transition:enter="transform transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="transform transition ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all w-full max-w-lg border border-slate-100 dark:border-slate-700">
                
                <form action="{{ route('solicitud_nota') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $practicaData->id }}">
                    
                    <div class="bg-gradient-to-r from-orange-400 to-red-500 px-6 py-6 shadow-lg relative overflow-hidden">
                        <div class="relative z-10 flex items-center gap-4 text-white">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-md border border-white/20">
                                <i class="bi bi-pencil-square text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight">Solicitar Edición</h3>
                                <p class="text-orange-50 text-xs font-bold uppercase tracking-widest mt-0.5">Requiere aprobación administrativa</p>
                            </div>
                        </div>
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-900/30 rounded-xl p-4 flex gap-3">
                             <i class="bi bi-info-circle-fill text-orange-500 shrink-0 mt-0.5"></i>
                             <p class="text-xs text-orange-700 dark:text-orange-300 leading-relaxed">
                                 Para modificar una calificación ya registrada, debe solicitar autorización al Administrador. Una vez aprobado, el sistema habilitará el formulario nuevamente.
                             </p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Motivo de la solicitud</label>
                            <textarea name="motivo" required rows="3"
                                class="w-full border border-slate-200 dark:border-slate-600 rounded-xl shadow-sm p-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm font-medium resize-none placeholder:text-slate-400"
                                placeholder="Explique brevemente por qué necesita editar la nota..."></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center rounded-xl bg-orange-500 hover:bg-orange-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-orange-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all transform hover:scale-[1.02]">
                            Enviar Solicitud
                        </button>
                        <button type="button" @click="requestEditModalOpen = false" 
                                class="w-full inline-flex justify-center items-center rounded-xl bg-white dark:bg-slate-800 border-none hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-3 text-sm font-bold text-slate-500 dark:text-slate-400 transition-all">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL REVISAR SOLICITUD (Admin) -->
    <div x-show="adminReviewModalOpen" 
         class="fixed inset-0 z-[1070] overflow-y-auto" 
         aria-labelledby="modal-review-title" role="dialog" aria-modal="true" x-cloak>
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" 
              x-show="adminReviewModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             @click="adminReviewModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
             <div x-show="adminReviewModalOpen"
                 x-transition:enter="transform transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="transform transition ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all w-full max-w-lg border border-slate-100 dark:border-slate-700">
                
                <form action="{{ route('solicitud.nota') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_practica" value="{{ $practicaData->id }}">
                    
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-6 shadow-lg relative overflow-hidden">
                        <div class="relative z-10 flex items-center gap-4 text-white">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-md border border-white/20">
                                <i class="bi bi-shield-lock text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black tracking-tight">Gestionar Solicitud</h3>
                                <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mt-0.5">Administración de Notas</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Student Info -->
                         <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700">
                            <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 font-bold">
                                {{ substr($estudiante->nombres, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Estudiante</p>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</p>
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div>
                             <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-2"><i class="bi bi-chat-quote-fill mr-1"></i> Motivo de Solicitud</h4>
                             <p class="text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-lg border border-slate-100 dark:border-slate-800 italic"
                                x-text="currentSolicitud ? currentSolicitud.motivo : 'Cargando motivo...'">
                             </p>
                        </div>

                        <!-- Decision -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">Decisión Administrativa</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="estado" value="1" class="peer sr-only" checked>
                                    <div class="rounded-xl border-2 border-slate-200 dark:border-slate-700 p-3 text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                                        <i class="bi bi-check-circle-fill text-2xl mb-1 block"></i>
                                        <span class="text-xs font-black uppercase tracking-wider">Aprobar</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="estado" value="2" class="peer sr-only">
                                    <div class="rounded-xl border-2 border-slate-200 dark:border-slate-700 p-3 text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                                        <i class="bi bi-x-circle-fill text-2xl mb-1 block"></i>
                                        <span class="text-xs font-black uppercase tracking-wider">Rechazar</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                         <!-- Justificacion -->
                         <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Justificación de la decisión</label>
                            <textarea name="justificacion" required rows="2"
                                class="w-full border border-slate-200 dark:border-slate-600 rounded-xl shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm font-medium resize-none"
                                placeholder="Ingrese una justificación..."></textarea>
                        </div>

                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:scale-[1.02]">
                            Guardar Decisión
                        </button>
                        <button type="button" @click="adminReviewModalOpen = false" 
                                class="w-full inline-flex justify-center items-center rounded-xl bg-white dark:bg-slate-800 border-none hover:bg-slate-50 dark:hover:bg-slate-700 px-4 py-3 text-sm font-bold text-slate-500 dark:text-slate-400 transition-all">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ACTION MODAL -->
    <div x-show="modalOpen" class="fixed inset-0 z-[1060] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
        <!-- Backdrop -->
        <div x-show="modalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" 
             @click="modalOpen = false"></div>

        <!-- Modal Panel Wrapper -->
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <!-- Modal Panel -->
            <div x-show="modalOpen"
                 x-transition:enter="transform transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="transform transition ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all w-full max-w-lg border-2 border-slate-100 dark:border-slate-700">
                
                <div class="bg-slate-200 dark:bg-slate-800 px-6 py-8">
                    <div class="flex flex-col items-center text-center">
                        <!-- Icon -->
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-6 ring-4 ring-white dark:ring-slate-800 shadow-lg" 
                             :class="modalAction === 'Aprobado' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                             <i class="bi text-3xl" :class="modalAction === 'Aprobado' ? 'bi-check-lg' : 'bi-exclamation-triangle-fill'"></i>
                        </div>
                        
                        <!-- Title & Content -->
                        <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2" id="modal-title" x-text="modalTitle"></h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 max-w-xs mx-auto" x-text="modalAction === 'Aprobado' ? '¿Confirmas que este documento es válido? La aprobación será notificada.' : 'Indica el motivo por el cual se observa este documento para que el estudiante lo corrija.'"></p>
                        
                        <!-- Input -->
                        <div class="w-full text-left bg-slate-50 dark:bg-slate-900/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">
                                <span x-text="modalAction === 'Aprobado' ? 'Nota Adicional (Opcional)' : 'Motivo de Observación'"></span>
                                <span x-show="modalAction !== 'Aprobado'" class="text-red-500">*</span>
                            </label>
                            <textarea x-model="modalComment" 
                                      class="w-full border border-slate-200 dark:border-slate-600 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm transition-all resize-none font-medium" 
                                      rows="3" 
                                      :placeholder="modalAction === 'Aprobado' ? 'Escribe aqui...' : 'Descripción detallada...'"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="bg-gray-50 dark:bg-slate-900/80 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="submitModal" 
                            class="w-full inline-flex justify-center items-center rounded-xl border border-transparent px-4 py-3 text-sm font-bold text-white shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all transform hover:scale-[1.02]"
                            :class="modalAction === 'Aprobado' ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/30 ring-blue-500' : 'bg-red-600 hover:bg-red-700 shadow-red-500/30 ring-red-500'">
                        <span x-text="modalAction === 'Aprobado' ? 'Aprobar Documento' : 'Registrar Observación'"></span>
                    </button>
                    <button type="button" @click="modalOpen = false" 
                            class="w-full inline-flex justify-center items-center rounded-xl border-transparent bg-transparent px-4 py-3 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none transition-all">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function supervisionLogic(initialState, practicaId, initialGrade) {
        return {
            currentStage: initialState > 5 ? 5 : initialState, // Cap at 5 for view
            practicaState: initialState,
            steps: ['Inicio', 'Desarrollo', 'Seguimiento', 'Finalización', 'Evaluación'],
            loading: false,
            calificacionData: initialGrade,
            currentSolicitud: null,
            hasPendingRequest: false,

            // Modal states
            requestEditModalOpen: false,
            adminReviewModalOpen: false,

            empresaData: null,
            jefeData: null,
            docs: {
                fut: null,
                carta1: null,
                doc_seg1: null,
                doc_seg2: null,
                constancia: null,
                informe: null,
                // Extended docs
                carta_aceptacion: null,
                plan_actividades: null,
                registro_actividades: null,
                control_actividades: null
            },

            pendingAction: null,
            modalOpen: false,
            modalTitle: '',
            modalAction: '', // 'Aprobado' or 'Corregir'
            modalComment: '',
            targetId: null,
            targetType: '', // 'empresa', 'jefe', 'archivo'
            
            async init() {
                // Determine logic based on current stage? 
                // We can cache or load on demand. Let's load data for current stage.
                this.loadStageData(this.currentStage);
            },
            
            openRequestEditModal() {
                 this.requestEditModalOpen = true;
            },
            
            openAdminReviewModal() {
                // Ensure we have the request details
                if(!this.currentSolicitud) {
                    // Fallback if not loaded
                    this.loadSolicitudData();
                }
                this.adminReviewModalOpen = true;
            },
            
            async loadSolicitudData() {
                 try {
                     // Assuming we can fetch status. For now, since we lack specific endpoint documentation,
                     // we might need to rely on what passed via blade or a generic fetch if available.
                     // IMPORTANT: We will try to fetch from practica status or a specific route if guessed.
                     // The user requested we add the modal logic.
                     const solv = await fetch(`/api/practica/${practicaId}/solicitud-activa`);
                     if(solv.ok) {
                          this.currentSolicitud = await solv.json();
                     }
                 } catch(e) { console.error(e); }
            },

            openActionModal(type, id, action) {
                this.pendingAction = { type, id, action };
                this.modalAction = action;
                this.targetType = type;
                this.targetId = id;
                this.modalComment = '';
                this.modalTitle = action === 'Aprobado' ? 'Confirmar Aprobación' : 'Registrar Observación';
                this.modalOpen = true;
            },

            submitModal() {
                if (!this.pendingAction) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';

                let actionUrl = '';
                if (this.pendingAction.type === 'empresa') actionUrl = "{{ route('empresa.actualizar.estado') }}";
                else if (this.pendingAction.type === 'jefe') actionUrl = "{{ route('jefe_inmediato.actualizar.estado') }}";
                else if (this.pendingAction.type === 'archivo') actionUrl = "{{ route('actualizar.archivo') }}";

                form.action = actionUrl;

                const csrfInput = document.createElement('input');
                csrfInput.name = '_token';
                csrfInput.value = "{{ csrf_token() }}";
                form.appendChild(csrfInput);

                const idInput = document.createElement('input');
                idInput.name = 'id';
                idInput.value = this.pendingAction.id;
                form.appendChild(idInput);

                const stateInput = document.createElement('input');
                stateInput.name = 'estado';
                stateInput.value = this.pendingAction.action;
                form.appendChild(stateInput);

                const commentInput = document.createElement('input');
                commentInput.name = 'comentario';
                commentInput.value = this.modalComment;
                form.appendChild(commentInput);

                document.body.appendChild(form);
                form.submit();
            },

            setStage(stage) {
                if (stage > this.practicaState && stage > 1) {
                }
                this.currentStage = stage;
                this.loadStageData(stage);
            },

            async loadStageData(stage) {
                this.loading = true;
                try {
                    // Fetch generic data needed for stages
                     if(stage === 1 && !this.empresaData) {
                         const res = await fetch(`/api/empresa/${practicaId}`);
                         if(res.ok) this.empresaData = await res.json();
                         
                         const res2 = await fetch(`/api/jefeinmediato/${practicaId}`);
                         if(res2.ok) this.jefeData = await res2.json();
                     }
                     
                     if(stage >= 2 && !this.docs.fut) {
                        // Load docs once or based on stage
                        // Simplified document loader
                        const loadDoc = async (type, key) => {
                             const r = await fetch(`/api/documento/${practicaId}/${type}`);
                             console.log('Response: wal -', r);
                             if(r.ok) {
                                 const d = await r.json();
                                 if(d && d.length > 0) this.docs[key] = d[0];
                             }
                        };
                        
                        await loadDoc('fut', 'fut');
                        
                        // Load all documents to support dynamic rendering
                        await loadDoc('carta_presentacion', 'carta1'); // Used as carta1 in generic logic or direct
                        await loadDoc('carta_aceptacion', 'carta_aceptacion');
                        
                        await loadDoc('plan_actividades_ppp', 'plan_actividades');
                        await loadDoc('registro_actividades', 'registro_actividades');
                        await loadDoc('control_actividades', 'control_actividades');
                        
                        await loadDoc('constancia_cumplimiento', 'constancia');
                        await loadDoc('informe_final_ppp', 'informe');
                     }
                     
                     if(stage === 5 && this.practicaState > 5) {
                         // Load Grade
                         const r = await fetch(`/api/practica/${practicaId}/calificacion`);
                         if(r.ok) {
                             const d = await r.json();
                             this.calificacionData = d.calificacion;
                         }
                         
                         // Check for pending requests (If state is 7 or via separate endpoint)
                         // For now we check if state is 7 (Pending request based on legacy controller)
                         // or we check a specific endpoint. 
                         // Note: In legacy, state 7 implies pending request.
                         if(this.practicaState === 7) {
                             this.hasPendingRequest = true;
                             // Try to fetch active solicitud?
                             // Since we don't have the dedicated endpoint confirmed, we'll try to use a common pattern or skip if necessary.
                             // BUT, for the modal to work we need the 'motivo'.
                             const solv = await fetch(`/api/practica/${practicaId}/solicitud-activa`);
                             if(solv.ok) {
                                  this.currentSolicitud = await solv.json();
                             }
                         }
                     }

                } catch(e) {
                    console.error("Error loading data", e);
                } finally {
                    this.loading = false;
                }
            },

            getStageInfo(stage) {
                const isDesarrollo = '{{ $practicaData->tipo_practica }}' === 'desarrollo';
                
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
                        subtitle: 'Revisa los documentos iniciales requeridos.',
                        docs: docs
                    };
                }
                if(stage === 3) {
                     let docs = [];
                     
                     if (isDesarrollo) {
                         docs = [
                            { key: 'carta_aceptacion', label: 'Carta de Aceptación', icon: 'bi-check2-circle', color: 'bg-teal-100 text-teal-600' },
                            { key: 'plan_actividades', label: 'Plan de Actividades', icon: 'bi-list-check', color: 'bg-orange-100 text-orange-600' }
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
                        subtitle: 'Plan de actividades y documentos de control.',
                        docs: docs
                    };
                }
                if(stage === 4) {
                     return {
                        title: 'Cierre de Prácticas',
                        subtitle: 'Revisión de informes finales y constancias.',
                        docs: [
                             { key: 'constancia', label: 'Constancia de Cumplimiento', icon: 'bi-award', color: 'bg-pink-100 text-pink-600' },
                             { key: 'informe', label: 'Informe Final PPP', icon: 'bi-book', color: 'bg-violet-100 text-violet-600' }
                        ]
                    };
                }
                 // Return empty defaults for other stages to allow safe render
                return { title: '', subtitle: '', docs: [] };
            },

            getDocStatus(doc) {
                const state = doc ? doc.state : 3;
                if (state == 2) {
                    return {
                        wrapper: 'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400',
                        icon: 'bi-check-circle-fill',
                        text: 'Aprobado'
                    };
                }
                if (state == 0) {
                     return {
                        wrapper: 'bg-yellow-100 dark:bg-yellow-800 text-yellow-600 dark:text-yellow-400',
                        icon: 'bi-exclamation-triangle-fill',
                        text: 'Enviado a corrección'
                    };
                }
                if (state == 1) {
                     return {
                        wrapper: 'bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-400',
                        icon: 'bi-file-earmark-text-fill',
                        text: 'Enviado'
                    };
                }
                return {
                    wrapper: 'bg-gray-100 text-gray-600',
                    icon: 'bi-circle-fill',
                    text: 'Pendiente'
                };
            }
        }
    }
</script>
@endsection
