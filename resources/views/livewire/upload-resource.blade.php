<div>
    @if($uploadModalOpen)
    <div class="fixed inset-0 z-[1100] flex items-center justify-center px-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

        <!-- Modal -->
        <div class="relative bg-slate-50 dark:bg-slate-900 rounded-[1.5rem] shadow-2xl w-full max-w-lg overflow-hidden border-1 border-slate-100 dark:border-slate-800 transition-all zoom-in">
            
            <div class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-cloud-upload text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none">Subir Recurso</h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Repositorio de Documentos</p>
                        </div>
                    </div>
                    <button wire:click="closeModal" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-4">
                <form wire:submit.prevent="save">
                    <!-- Targeted Role -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                            Tipo de Usuario (Destinatario)
                        </label>
                        <select wire:model="id_rol" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer outline-none">
                            <option value="">Todos los usuarios</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hierarchy Selects -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Facultad</label>
                            <select wire:model="facultad" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-2 py-3 text-[11px] font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                                <option value="">Global</option>
                                @foreach($facultadesList as $fac)
                                    <option value="{{ $fac->id }}">{{ \Illuminate\Support\Str::limit($fac->name, 20) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Escuela</label>
                            <select wire:model="escuela" {{ empty($escuelas) ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-2 py-3 text-[11px] font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                                <option value="">Todas</option>
                                @foreach($escuelas as $esc)
                                    <option value="{{ $esc['id'] }}">{{ \Illuminate\Support\Str::limit($esc['name'], 20) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Sección</label>
                            <select wire:model="seccion" {{ empty($secciones) ? 'disabled' : '' }} class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-2 py-3 text-[11px] font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                                <option value="">Todas</option>
                                @foreach($secciones as $sec)
                                    <option value="{{ $sec['id'] }}">{{ \Illuminate\Support\Str::limit($sec['seccion'], 20) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @error('facultad') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror

                    <!-- Name & Type -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Nombre del Documento</label>
                        <input type="text" wire:model="nombre" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none" placeholder="Ej: Guía de Prácticas 2024">
                        @error('nombre') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Tipo de Recurso</label>
                            <select wire:model="tipo" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                                <option value="">Seleccione...</option>
                                @foreach($availableTypes as $t)
                                    <option value="{{ $t }}">{{ $tipoLabels[$t] ?? $t }}</option>
                                @endforeach
                            </select>
                            @error('tipo') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Archivo</label>
                            <label class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-400 cursor-pointer flex items-center justify-between shadow-sm hover:border-blue-500/50 transition-all">
                                <span class="truncate">{{ $archivo ? $archivo->getClientOriginalName() : 'Seleccionar...' }}</span>
                                <i class="bi bi-paperclip text-lg text-blue-500"></i>
                                <input type="file" wire:model="archivo" class="hidden">
                            </label>
                            @error('archivo') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="archivo" class="text-[10px] text-blue-500 font-bold animate-pulse">Subiendo archivo al servidor...</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Descripción (Opcional)</label>
                        <textarea wire:model="descripcion" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none" placeholder="Breve detalle..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/5">
                        <button type="button" wire:click="closeModal" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all flex items-center gap-2">
                            <span wire:loading.remove wire:target="save"><i class="bi bi-cloud-arrow-up-fill text-lg"></i> Subir Documento</span>
                            <span wire:loading wire:target="save"><div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div> Procesando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
