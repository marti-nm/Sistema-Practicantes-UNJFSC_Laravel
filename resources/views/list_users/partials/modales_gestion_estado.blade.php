<!-- Modal solicitudBajaAP with Tailwind -->
<div 
    x-show="solicitudAPModalOpen"
    class="fixed inset-0 z-[1060] flex items-center justify-center px-4 overflow-hidden" 
    x-cloak>
    <x-backdrop-modal name="solicitudAPModalOpen"/>
    <div 
        x-show="solicitudAPModalOpen" 
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
        class="relative bg-slate-50 dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg border-1 border-slate-100 dark:border-slate-800 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4 shrink-0 shadow-lg relative z-10 rounded-t-[1rem]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20">
                        <template x-if="requireData.state_ap < 4">
                            <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                        </template>
                        <template x-if="requireData.state_ap >= 4">
                            <i class="bi bi-unlock-fill text-lg"></i>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-white text-base font-black tracking-tight leading-none" x-text="requireData.state_ap < 4 ? 'Gestionar {{ $cargo }}' : 'Habilitar {{ $cargo }}'"></h3>
                        <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Sistema de Gestión Académica</p>
                    </div>
                </div>
                <button @click="solicitudAPModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <!-- Body -->
        <div class="p-6 overflow-y-auto custom-scrollbar" x-data="{ selectedOption: 1 }" x-init="$watch('requireData.state_ap', value => selectedOption = (value >= 4 ? 3 : 1))">
            
            <!-- Alerts -->
            <div x-show="requireData.state_ap < 4" class="mb-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border-1 border-amber-100 dark:border-amber-500/20 flex gap-3">
                <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg shrink-0"></i>
                <div>
                    <h4 class="text-amber-800 dark:text-amber-400 font-bold text-xs uppercase tracking-wide mb-1">Advertencia</h4>
                    <p class="text-amber-700/80 dark:text-amber-400/80 text-xs leading-relaxed">El {{ $cargo }} debe ser aprobado por el administrador antes de ser deshabilitado o eliminado del sistema.</p>
                </div>
            </div>

            <div x-show="requireData.state_ap >= 4" class="mb-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-500/10 border-1 border-blue-100 dark:border-blue-500/20 flex gap-3">
                <i class="bi bi-info-circle-fill text-blue-500 text-lg shrink-0"></i>
                <div>
                    <h4 class="text-blue-800 dark:text-blue-400 font-bold text-xs uppercase tracking-wide mb-1">Información</h4>
                    <p class="text-blue-700/80 dark:text-blue-400/80 text-xs leading-relaxed">Para habilitar nuevamente a un {{ $cargo }}, la solicitud deberá ser aprobada por el administrador.</p>
                </div>
            </div>

            <form action="{{ route('solicitud_ap') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id_ap" :value="requireData.id_ap">
                <input type="hidden" name="id_sa" :value="requireData.id_sa">
                
                <!-- Student Name -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-1.5">{{ $cargo }} Seleccionado</label>
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700">
                        <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shadow-sm">
                            <i class="bi bi-person-fill text-lg"></i>
                        </div>
                        <span class="font-bold text-sm text-slate-700 dark:text-slate-200" x-text="requireData.nombre_ap"></span>
                    </div>
                </div>

                <!-- Options -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-2">Acción a Realizar</label>
                    
                    <!-- Options for Disable/Delete (state < 4) -->
                    <div x-show="requireData.state_ap < 4" class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="opcion" value="1" class="peer sr-only" x-model="selectedOption">
                            <div class="p-3 rounded-2xl border-2 transition-all text-center flex flex-col items-center gap-2 h-full
                                peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:text-blue-700 dark:peer-checked:text-blue-400 peer-checked:shadow-lg peer-checked:shadow-blue-500/10
                                border-slate-200 dark:border-slate-700 hover:border-blue-300 bg-slate-50 dark:bg-slate-800 text-slate-500 group-hover:-translate-y-1">
                                <i class="bi bi-lock-fill text-xl mb-0.5"></i>
                                <span class="text-xs font-black uppercase tracking-wider">Deshabilitar</span>
                                <span class="text-[10px] font-medium opacity-70 leading-tight">Podrá habilitar nuevamente</span>
                            </div>
                        </label>

                        <label class="cursor-pointer relative group">
                            <input type="radio" name="opcion" value="2" class="peer sr-only" x-model="selectedOption">
                            <div class="p-3 rounded-2xl border-2 transition-all text-center flex flex-col items-center gap-2 h-full
                                peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:text-red-700 dark:peer-checked:text-red-400 peer-checked:shadow-lg peer-checked:shadow-red-500/10
                                border-slate-200 dark:border-slate-700 hover:border-red-300 bg-slate-50 dark:bg-slate-800 text-slate-500 group-hover:-translate-y-1">
                                <i class="bi bi-trash-fill text-xl mb-0.5"></i>
                                <span class="text-xs font-black uppercase tracking-wider">Eliminar</span>
                                <span class="text-[10px] font-medium opacity-70 leading-tight">Eliminación permanente</span>
                            </div>
                        </label>
                    </div>

                    <!-- Option for Enable (state >= 4) -->
                    <div x-show="requireData.state_ap >= 4">
                        <label class="cursor-pointer relative block group">
                            <input type="radio" name="opcion" value="3" class="peer sr-only" x-model="selectedOption">
                            <div class="p-4 rounded-2xl border-2 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-center flex flex-col items-center gap-2 shadow-lg shadow-emerald-500/10 transition-transform group-hover:-translate-y-1">
                                <i class="bi bi-unlock-fill text-2xl mb-0.5"></i>
                                <span class="text-sm font-black uppercase tracking-wider">Habilitar Estudiante</span>
                                <span class="text-xs font-medium opacity-80">Restaurar acceso al sistema</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-1.5">Comentario / Justificación</label>
                    <textarea name="comentario" required rows="2" 
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm font-medium text-slate-700 dark:text-slate-200 placeholder:text-slate-400 resize-none"
                        placeholder="Ingrese el motivo detallado de la solicitud..."></textarea>
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="solicitudAPModalOpen = false" class="flex-1 px-4 py-2.5 rounded-xl border-1 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:shadow-lg hover:shadow-blue-500/30 hover:scale-[1.02] active:scale-95 transition-all">
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal management with Tailwind -->
<div 
    x-show="managementAPModalOpen"
    class="fixed inset-0 z-[1060] flex items-center justify-center px-4 overflow-hidden"
    x-cloak
    @keydown.escape="managementAPModalOpen = false">
    <div x-show="managementAPModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
            class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="managementAPModalOpen = false"></div>
    <div x-show="managementAPModalOpen" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
            class="relative bg-slate-50 dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg border-1 border-slate-100 dark:border-slate-800 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 shrink-0 shadow-lg relative z-10 rounded-t-[1rem]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border border-white/20">
                        <i class="bi bi-person-gear text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-white text-base font-black tracking-tight leading-none">Gestionar {{ $cargo }}</h3>
                        <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Sistema de Gestión Académica</p>
                    </div>
                </div>
                <button @click="managementAPModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <!-- Body -->
        <div class="p-6 overflow-y-auto custom-scrollbar" x-data="{ selectedGestion: 1 }">
            
            <!-- Loading State -->
            <div x-show="loading" class="flex flex-col items-center justify-center py-8 text-slate-400">
                <div class="w-10 h-10 border-4 border-blue-500/30 border-t-blue-600 rounded-full animate-spin mb-3"></div>
                <span class="text-xs font-bold uppercase tracking-widest">Cargando información...</span>
            </div>

            <form x-show="!loading" action="{{ route('solicitud.ap') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id_sol" :value="solicitudData.id">

                <!-- Student Name -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-1.5">{{ $cargo }}</label>
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700">
                        <div class="w-8 h-8 rounded-full bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shadow-sm">
                            <i class="bi bi-person-fill text-lg"></i>
                        </div>
                        <span class="font-bold text-sm text-slate-700 dark:text-slate-200" x-text="requireData.nombre_ap"></span>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="grid grid-cols-1 gap-3">
                    <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 border-1 border-indigo-100 dark:border-indigo-500/20">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400 mb-1">Acción Solicitada</label>
                        <p class="text-sm font-bold text-indigo-900 dark:text-indigo-300" x-text="solicitudData.accion"></p>
                    </div>
                    
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700">
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Justificación</label>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300" x-text="solicitudData.justificacion"></p>
                    </div>
                </div>

                <!-- Approval Options -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-2">Decisión</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="estado" value="1" class="peer sr-only" x-model="selectedGestion">
                            <div class="p-3 rounded-2xl border-2 transition-all text-center flex flex-col items-center gap-2 h-full
                                peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-400 peer-checked:shadow-lg peer-checked:shadow-emerald-500/10
                                border-slate-200 dark:border-slate-700 hover:border-emerald-300 bg-slate-50 dark:bg-slate-800 text-slate-500 group-hover:-translate-y-1">
                                <i class="bi bi-check-circle-fill text-xl mb-0.5"></i>
                                <span class="text-xs font-black uppercase tracking-wider">Aprobar</span>
                            </div>
                        </label>

                        <label class="cursor-pointer relative group">
                            <input type="radio" name="estado" value="2" class="peer sr-only" x-model="selectedGestion">
                            <div class="p-3 rounded-2xl border-2 transition-all text-center flex flex-col items-center gap-2 h-full
                                peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:text-red-700 dark:peer-checked:text-red-400 peer-checked:shadow-lg peer-checked:shadow-red-500/10
                                border-slate-200 dark:border-slate-700 hover:border-red-300 bg-slate-50 dark:bg-slate-800 text-slate-500 group-hover:-translate-y-1">
                                <i class="bi bi-x-circle-fill text-xl mb-0.5"></i>
                                <span class="text-xs font-black uppercase tracking-wider">Rechazar</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-1.5">Comentario</label>
                    <textarea name="comentario" required rows="2" 
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all text-sm font-medium text-slate-700 dark:text-slate-200 placeholder:text-slate-400 resize-none"
                        placeholder="Ingrese un comentario sobre la decisión..."></textarea>
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="managementAPModalOpen = false" class="flex-1 px-4 py-2.5 rounded-xl border-1 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-xs uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-xs uppercase tracking-wider hover:shadow-lg hover:shadow-blue-500/30 hover:scale-[1.02] active:scale-95 transition-all">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Edit Persona with Tailwind -->
<div
    x-show="editAPModalOpen"
    class="fixed inset-0 z-[1060] flex items-center justify-center px-4 overflow-hidden"
    x-cloak>
    <x-backdrop-modal name="editAPModalOpen" />
    
    <div x-show="editAPModalOpen" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
            class="relative bg-slate-50 dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-4xl border-1 border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh] overflow-hidden">
        
        <div class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                        <i class="bi bi-clipboard-data-fill text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-white text-lg font-black tracking-tight leading-none">Editar {{ $cargo }}</h3>
                        <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Sistema de Gestión de Practicantes</p>
                    </div>
                </div>
                <button @click="editAPModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto custom-scrollbar relative" x-data="{ isEditing: false, previewUrl: null }" x-init="$watch('editAPModalOpen', val => { if(!val) { isEditing = false; previewUrl = null; } })">
            
            <!-- Loading Overlay -->
            <div x-show="loadingEdit" class="absolute inset-0 z-50 bg-slate-50/90 dark:bg-slate-900/90 backdrop-blur-sm flex items-center justify-center">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-8 h-8 border-3 border-blue-500/30 border-t-blue-600 rounded-full animate-spin"></div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Cargando...</span>
                </div>
            </div>

            <form x-show="editData.id" method="POST" action="{{ route('persona.editar') }}" enctype="multipart/form-data" class="h-full flex flex-col">
                @csrf
                <input type="hidden" name="persona_id" :value="editData.id">
                
                <div class="p-4 grid grid-cols-1 lg:grid-cols-12 gap-4">
                    
                    <!-- Left Column: Photo & Institutional Info -->
                    <div class="lg:col-span-4 space-y-4">
                        <!-- Photo Card -->
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 border-1 border-slate-200 dark:border-slate-700 shadow-sm relative group overflow-hidden">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-32 h-32 rounded-lg mb-3 relative group cursor-pointer overflow-hidden shadow-md ring-2 ring-slate-100 dark:ring-slate-700 transition-all bg-slate-100 dark:bg-slate-700">
                                    <template x-if="previewUrl">
                                        <img :src="previewUrl" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!previewUrl && editData.ruta_foto">
                                        <img :src="'/' + editData.ruta_foto" class="w-full h-full object-cover object-center">
                                    </template>
                                    <template x-if="!previewUrl && !editData.ruta_foto">
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-slate-500">
                                            <i class="bi bi-person-fill text-5xl"></i>
                                        </div>
                                    </template>
                                    
                                    <!-- Overlay for Edit -->
                                    <div x-show="isEditing" class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 backdrop-blur-sm">
                                        <i class="bi bi-camera-fill text-xl text-white mb-1"></i>
                                        <span class="text-[9px] text-white/90 font-bold uppercase tracking-wider">Cambiar</span>
                                    </div>
                                    <input type="file" name="ruta_foto" x-ref="photoInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" @change="previewUrl = URL.createObjectURL($event.target.files[0])" :disabled="!isEditing">
                                </div>

                                <div x-show="isEditing" class="w-full">
                                    <button type="button" @click="$refs.photoInput.click()" class="w-full py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase tracking-widest border-1 border-blue-100 dark:border-blue-800 cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-all flex items-center justify-center gap-2">
                                        <i class="bi bi-cloud-arrow-up-fill"></i>
                                        <span>Subir Foto</span>
                                    </button>
                                    <div class="mt-1.5 text-[8px] text-slate-400 font-bold uppercase tracking-wider text-center">
                                        JPG, PNG • Máx 2MB
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Read-only Institutional Info -->
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border-1 border-slate-200 dark:border-slate-700">
                            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                <i class="bi bi-building-fill text-slate-300"></i>
                                Datos Académicos
                            </h4>
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[8px] uppercase font-bold text-slate-400 block mb-0.5">Código</label>
                                        <div class="font-mono font-bold text-slate-700 dark:text-slate-200 text-[10px] bg-slate-50 dark:bg-slate-900 px-2 py-1 rounded border-1 border-slate-200 dark:border-slate-700 truncate" x-text="editData.codigo"></div>
                                    </div>
                                    <div>
                                        <label class="text-[8px] uppercase font-bold text-slate-400 block mb-0.5">Correo</label>
                                        <div class="font-bold text-slate-700 dark:text-slate-200 text-[10px] bg-slate-50 dark:bg-slate-900 px-2 py-1 rounded border-1 border-slate-200 dark:border-slate-700 truncate" :title="editData.correo_inst" x-text="editData.correo_inst"></div>
                                    </div>
                                </div>
                                
                                <div class="pt-2 border-t border-slate-200 dark:border-slate-700 space-y-2">
                                    <div>
                                        <label class="text-[8px] uppercase font-bold text-slate-400 block mb-0.5">Facultad</label>
                                        <div class="text-[10px] font-bold text-slate-600 dark:text-slate-300 leading-tight" x-text="requireData.facultad || '...'"></div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="text-[8px] uppercase font-bold text-slate-400 block mb-0.5">Escuela</label>
                                            <div class="text-[10px] font-bold text-slate-600 dark:text-slate-300 leading-tight" x-text="requireData.escuela || '...'"></div>
                                        </div>
                                        <div>
                                            <label class="text-[8px] uppercase font-bold text-slate-400 block mb-0.5">Sección</label>
                                            <div class="text-[10px] font-bold text-slate-600 dark:text-slate-300 leading-tight" x-text="requireData.seccion || '...'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Editable Forms -->
                    <div class="lg:col-span-8">
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-4 border-1 border-slate-200 dark:border-slate-700 shadow-sm h-full">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                                    <i class="bi bi-person-lines-fill text-blue-500"></i>
                                    Datos Personales
                                </h4>
                                <div x-show="!isEditing">
                                    <button type="button" @click="isEditing = true" class="px-3 py-1.5 rounded-lg bg-slate-900 dark:bg-blue-600 text-white text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all flex items-center gap-1.5 shadow-md shadow-blue-500/10">
                                        <i class="bi bi-pencil-square"></i>
                                        Editar
                                    </button>
                                </div>
                                <div x-show="isEditing" class="flex items-center gap-2" style="display: none;">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Editando</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                                
                                <!-- DNI -->
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">DNI</label>
                                    <input type="text" name="dni" required
                                        class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        :class="{'bg-slate-50 dark:bg-slate-950': isEditing, 'opacity-60 bg-slate-100': !isEditing}"
                                        :value="editData.dni" :disabled="!isEditing">
                                </div>

                                <!-- Celular -->
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Celular</label>
                                    <div class="relative">
                                        <i class="bi bi-whatsapp absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="tel" name="celular" 
                                            class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg pl-8 pr-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            :class="{'bg-slate-50 dark:bg-slate-950': isEditing, 'opacity-60 bg-slate-100': !isEditing}"
                                            :value="editData.celular" :disabled="!isEditing">
                                    </div>
                                </div>

                                <!-- Nombres -->
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Nombres</label>
                                    <input type="text" name="nombres" required
                                        class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        :class="{'bg-slate-50 dark:bg-slate-950': isEditing, 'opacity-60 bg-slate-100': !isEditing}"
                                        :value="editData.nombres" :disabled="!isEditing">
                                </div>

                                <!-- Apellidos -->
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Apellidos</label>
                                    <input type="text" name="apellidos" required
                                        class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        :class="{'bg-slate-50 dark:bg-slate-950': isEditing, 'opacity-60 bg-slate-100': !isEditing}"
                                        :value="editData.apellidos" :disabled="!isEditing">
                                </div>

                                <!-- Género -->
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Género</label>
                                    <select name="sexo" class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        :class="{'bg-slate-50 dark:bg-slate-950': isEditing, 'opacity-60 bg-slate-100 appearance-none': !isEditing}"
                                        :disabled="!isEditing">
                                        <option value="M" :selected="editData.sexo == 'M'">Masculino</option>
                                        <option value="F" :selected="editData.sexo == 'F'">Femenino</option>
                                    </select>
                                </div>
                                
                                <!-- Departamento -->
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Dpto. Perú</label>
                                    <input type="text" name="departamento" required
                                        class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        :class="{'bg-slate-50 dark:bg-slate-950': isEditing, 'opacity-60 bg-slate-100': !isEditing}"
                                        :value="editData.departamento" :disabled="!isEditing">
                                </div>

                                <!-- Provincia (Dynamic Select) -->
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Provincia</label>
                                    <div class="relative">
                                        <select name="provincia" x-model="selectedProvincia" @change="updateDistritos()"
                                            :required="true"
                                            class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            :class="{'bg-slate-50 dark:bg-slate-950': isEditing, 'opacity-60 bg-slate-100 appearance-none bg-none': !isEditing}"
                                            :disabled="!isEditing">
                                            <option value="">Seleccione</option>
                                            <template x-for="prov in provincias" :key="prov.id">
                                                <option :value="prov.id" x-text="prov.nombre"></option>
                                            </template>
                                        </select>
                                        <div x-show="isEditing" class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                            <i class="bi bi-chevron-down text-[10px]"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Distrito (Dynamic Select) -->
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 ml-1">Distrito</label>
                                    <div class="relative">
                                        <select name="distrito" x-model="selectedDistrito"
                                            class="w-full bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition-all focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            :class="{'bg-slate-50 dark:bg-slate-950': isEditing, 'opacity-60 bg-slate-100 appearance-none bg-none': !isEditing}"
                                            :disabled="!isEditing || !selectedProvincia">
                                            <option value="">Seleccione</option>
                                            <template x-for="dist in distritos_options" :key="dist.id">
                                                <option :value="dist.id" x-text="dist.nombre"></option>
                                            </template>
                                        </select>
                                        <div x-show="isEditing" class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                            <i class="bi bi-chevron-down text-[10px]"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3 mt-auto">
                    <button type="button" @click="editAPModalOpen = false" class="px-4 py-2 rounded-lg border-1 border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 font-bold text-[10px] uppercase tracking-widest hover:bg-white dark:hover:bg-slate-800 transition-all">
                        Cerrar
                    </button>
                    
                    <div x-show="isEditing" class="flex gap-2" style="display: none;">
                        <button type="button" @click="isEditing = false; fetchEditPersona(editData.id);" class="px-4 py-2 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-bold text-[10px] uppercase tracking-widest hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-all">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-[10px] uppercase tracking-widest shadow-md shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-1.5">
                            <i class="bi bi-check-lg"></i>
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>