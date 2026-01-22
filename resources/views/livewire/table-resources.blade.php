<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-8 px-2">
        <div class="dataTables_length">
            <label class="font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest text-[10px]">
                Mostrar 
                <select wire:model="perPage" class="bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-white/10 rounded-xl px-3 py-1.5 outline-none mx-2 text-slate-700 dark:text-slate-300 font-bold text-xs cursor-pointer focus:border-blue-500 transition-colors">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </label>
        </div>
        <div class="relative w-full md:w-auto">
             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="bi bi-search text-slate-400"></i>
            </div>
            <input type="text" 
                   wire:model.debounce.500ms="search" 
                   class="pl-10 pr-4 py-2 border-1 border-slate-200 dark:border-white/10 rounded-xl text-sm font-bold text-slate-700 dark:text-white bg-slate-50 dark:bg-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 w-full md:w-64 transition-all" 
                   placeholder="Buscar recurso...">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-xl border-1 border-slate-100 dark:border-slate-800 overflow-hidden relative">
        <!-- Loading Overlay -->
        <div wire:loading.flex wire:target="search, perPage, page" class="absolute inset-0 z-50 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm items-center justify-center">
             <div class="flex flex-col items-center">
                <div class="w-10 h-10 border-4 border-blue-500/30 border-t-blue-600 rounded-full animate-spin"></div>
             </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/5">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Nombre del Recurso</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Tipo</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Nivel / Dirigido a</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Fecha Subida</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Subido Por</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recursos as $recurso)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 border-1 border-blue-100 dark:border-blue-500/10 shadow-sm group-hover:scale-110 transition-transform">
                                        <i class="bi bi-file-earmark-text-fill text-xl"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-slate-700 dark:text-white text-sm leading-tight truncate max-w-[200px]" title="{{ $recurso->nombre }}">{{ $recurso->nombre }}</h4>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                            {{ pathinfo($recurso->ruta, PATHINFO_EXTENSION) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-1 border-slate-200 dark:border-slate-700 shadow-sm">
                                    {{ $tipoLabels[$recurso->tipo] ?? $recurso->tipo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                 <div class="flex flex-col items-center gap-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-50 text-blue-600 border border-blue-100">{{ $nivelLabels[$recurso->nivel] ?? 'N/A' }}</span>
                                    @if($recurso->id_rol)
                                        @php $rolName = \App\Models\type_users::find($recurso->id_rol)->name ?? 'Rol Desconocido'; @endphp
                                        <span class="text-[10px] text-slate-400 font-medium">Solo: {{ $rolName }}</span>
                                    @else
                                        <span class="text-[10px] text-slate-400 font-medium">Todos</span>
                                    @endif
                                 </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400 tracking-tight">{{ $recurso->created_at->format('d/m/Y') }}</span>
                                    <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">{{ $recurso->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($recurso->uploader && $recurso->uploader->persona)
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase">
                                            {{ substr($recurso->uploader->persona->nombres, 0, 1) }}{{ substr($recurso->uploader->persona->apellidos, 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400 tracking-tight">
                                            {{ $recurso->uploader->persona->nombres }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ asset($recurso->ruta) }}" target="_blank"
                                        class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white transition-all border-1 border-blue-100 dark:border-blue-500/20 active:scale-95 flex items-center justify-center group/btn shadow-sm"
                                        title="Descargar">
                                        <i class="bi bi-cloud-download text-lg group-hover/btn:animate-bounce"></i>
                                    </a>
                                    @if(Auth::user()->hasAnyRoles([1, 2]))
                                        <button type="button" 
                                            wire:click="deleteRecurso({{ $recurso->id }})"
                                            wire:loading.attr="disabled"
                                            wire:confirm="¿Estás seguro de eliminar este recurso?"
                                            class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-all border-1 border-rose-100 dark:border-rose-500/20 active:scale-95 flex items-center justify-center group/btn shadow-sm"
                                            title="Eliminar">
                                            <i class="bi bi-trash-fill text-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-300 dark:text-slate-600 mb-4">
                                        <i class="bi bi-folder-x text-3xl"></i>
                                    </div>
                                    <h3 class="text-slate-500 dark:text-slate-400 font-bold">No se encontraron recursos</h3>
                                    <p class="text-slate-400 text-xs mt-1">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5">
            {{ $recursos->links() }}
        </div>
    </div>
</div>
