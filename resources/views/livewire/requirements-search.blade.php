<div class="mt-8">
    <div class="mb-6">
        <label class="block text-sm font-semibold mb-2 text-slate-700 dark:text-slate-300">Buscador Reactivo de Requisitos (TALL Stack Demo)</label>
        <div class="relative">
            <input 
                wire:model="search" 
                type="text" 
                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none dark:text-white"
                placeholder="Escribe 'Acreditación', 'Plan' o cualquier requisito..."
            >
            <div class="absolute right-4 top-3.5 text-slate-400">
                <i class="bi bi-search"></i>
            </div>
        </div>
        <p class="mt-2 text-xs text-slate-500 italic">Escribe arriba y observa cómo la lista se filtra instantáneamente sin recargar la página.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($filtered as $req)
            <div class="p-4 rounded-xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-block px-2 py-1 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase tracking-wider mb-2">
                            {{ $req['stage'] }}
                        </span>
                        <h4 class="font-bold text-slate-800 dark:text-slate-100">{{ $req['title'] }}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $req['desc'] }}</p>
                    </div>
                </div>
                <!-- Decorative element -->
                <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:opacity-10 transition-opacity">
                    <i class="bi bi-file-earmark-text text-6xl"></i>
                </div>
            </div>
        @empty
            <div class="col-span-full p-8 text-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                <p class="text-slate-500">No se encontraron requisitos que coincidan con "{{ $search }}"</p>
            </div>
        @endforelse
    </div>
</div>
