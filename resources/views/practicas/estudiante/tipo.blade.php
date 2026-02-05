<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4" id="practicas">
    
    <!-- Header Section -->
    <div class="flex items-center gap-3 mb-4">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
            <i class="bi bi-briefcase-fill text-lg"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Selección de Práctica</h2>
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Elige el tipo de práctica que deseas realizar</p>
        </div>
    </div>

    <!-- Verificación de requisitos -->
    <div class="mb-4" id="requirementsCheck">
        <!-- Success Alert -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 shadow-md shadow-emerald-500/10" id="requirementsOk">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center text-white shrink-0 shadow-md shadow-emerald-500/30">
                    <i class="bi bi-check-circle-fill text-lg"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-black text-emerald-900 dark:text-emerald-100 tracking-tight mb-0.5">¡Requisitos Completados!</h3>
                    <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 leading-snug">
                        Tu matrícula está completa. Puedes proceder a seleccionar el tipo de práctica que mejor se adapte a tus necesidades.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Error Alert -->
        <div class="hidden bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 shadow-md shadow-red-500/10" id="requirementsError">
            <div class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 mx-auto mb-3">
                    <i class="bi bi-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="text-lg font-black text-red-900 dark:text-red-100 tracking-tight mb-2">¡Atención!</h3>
                <p class="text-sm font-semibold text-red-700 dark:text-red-300 leading-relaxed max-w-md mx-auto">
                    Primero debes completar tu matrícula para acceder a estas opciones.
                </p>
            </div>
        </div>
    </div>

    <!-- Selección de tipo de práctica -->
    <div id="practiceSelection">
        <!-- Section Title -->
        <div class="mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                    <i class="bi bi-arrow-right-circle text-base"></i>
                </div>
                <h3 class="text-sm font-black text-slate-800 dark:text-white tracking-tight">
                    Selecciona el tipo de práctica que deseas realizar
                </h3>
            </div>
        </div>
        
        <!-- Practice Options Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            
            <!-- Desarrollo Card -->
            <div class="practice-option group relative bg-slate-50 dark:bg-slate-900 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-blue-500 dark:hover:border-blue-500 transition-all duration-300 overflow-hidden cursor-pointer shadow-md hover:shadow-xl hover:shadow-blue-500/20 hover:-translate-y-0.5" 
                data-practice-type="desarrollo"
                onclick="selectPracticeType('desarrollo')">
                
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-indigo-50/50 dark:from-blue-900/10 dark:to-indigo-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <div class="relative p-5">
                    <!-- Icon -->
                    <div class="mb-3 flex justify-center">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform duration-300">
                            <i class="bi bi-code-slash text-3xl"></i>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="text-center space-y-2">
                        <h4 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">Desarrollo</h4>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 leading-snug">
                            Realiza tu práctica en el área de desarrollo de software, trabajando en proyectos reales con tecnologías actuales.
                        </p>
                        
                        <!-- Button -->
                        <div class="pt-2">
                            <button type="button" 
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-black text-[11px] uppercase tracking-wider rounded-lg shadow-md shadow-blue-500/30 hover:shadow-lg hover:shadow-blue-500/40 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2"
                                onclick="selectPracticeType('desarrollo')">
                                <i class="bi bi-laptop text-base"></i>
                                Seleccionar Desarrollo
                            </button>
                        </div>
                    </div>
                    
                    <!-- Features List -->
                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <ul class="space-y-1 text-[10px] font-bold text-slate-600 dark:text-slate-400">
                            <li class="flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-blue-600 text-xs"></i>
                                <span>Proyectos reales de desarrollo</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-blue-600 text-xs"></i>
                                <span>Tecnologías actuales</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-blue-600 text-xs"></i>
                                <span>Experiencia profesional</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Convalidación Card -->
            <div class="practice-option group relative bg-slate-50 dark:bg-slate-900 rounded-xl border-2 border-slate-200 dark:border-slate-700 hover:border-teal-500 dark:hover:border-teal-500 transition-all duration-300 overflow-hidden cursor-pointer shadow-md hover:shadow-xl hover:shadow-teal-500/20 hover:-translate-y-0.5" 
                data-practice-type="convalidacion"
                onclick="selectPracticeType('convalidacion')">
                
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-teal-50/50 to-emerald-50/50 dark:from-teal-900/10 dark:to-emerald-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <div class="relative p-5">
                    <!-- Icon -->
                    <div class="mb-3 flex justify-center">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-teal-500/30 group-hover:scale-105 transition-transform duration-300">
                            <i class="bi bi-file-earmark-check text-3xl"></i>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="text-center space-y-2">
                        <h4 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">Convalidación</h4>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 leading-snug">
                            Convalida tu experiencia laboral previa como práctica pre-profesional mediante documentación y evaluación.
                        </p>
                        
                        <!-- Button -->
                        <div class="pt-2">
                            <button type="button" 
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-700 text-white font-black text-[11px] uppercase tracking-wider rounded-lg shadow-md shadow-teal-500/30 hover:shadow-lg hover:shadow-teal-500/40 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2"
                                onclick="selectPracticeType('convalidacion')">
                                <i class="bi bi-file-text text-base"></i>
                                Seleccionar Convalidación
                            </button>
                        </div>
                    </div>
                    
                    <!-- Features List -->
                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <ul class="space-y-1 text-[10px] font-bold text-slate-600 dark:text-slate-400">
                            <li class="flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-teal-600 text-xs"></i>
                                <span>Experiencia laboral previa</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-teal-600 text-xs"></i>
                                <span>Documentación validada</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-teal-600 text-xs"></i>
                                <span>Evaluación académica</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 shadow-md shadow-amber-500/10">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center text-white shrink-0 shadow-md shadow-amber-500/30">
                    <i class="bi bi-info-circle-fill text-base"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-[11px] font-black text-amber-900 dark:text-amber-100 uppercase tracking-wider mb-1">Importante</h4>
                    <p class="text-xs font-semibold text-amber-800 dark:text-amber-200 leading-snug">
                        Una vez seleccionado el tipo de práctica, <span class="font-black">no podrás cambiarlo</span>. 
                        Asegúrate de elegir la opción que mejor se adapte a tu situación académica y profesional.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

