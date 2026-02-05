<div class="flex flex-1 overflow-hidden gap-4 px-4">
    <!-- ASIDE: LISTA DE ESTUDIANTES -->
    <aside class="flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm transition-all duration-300 h-full overflow-hidden"
           :class="viewMode === 'evaluate' ? 'w-80 hidden xl:flex' : 'w-full lg:w-1/3 flex'">
        {{ $lista }}
    </aside>

    <section
        class="flex-1 flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden"
        x-show="selectedItem"
        x-cloak
    >
        <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 pt-4 px-4 flex flex-col shrink-0">
            {{ $hContent }}
        </div>

        {{ $bContent }}
    </section>

    <!-- Estado vacío -->
    <div class="hidden lg:flex flex-1 flex-col items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-10 text-center" x-show="!selectedItem">
        <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800/50 rounded-3xl shadow-sm flex items-center justify-center mb-4 border border-slate-100 dark:border-slate-800">
            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h3 class="text-sm font-black text-slate-700 dark:text-white mb-1 uppercase tracking-wider">Sin selección</h3>
        <p class="text-xs text-slate-400 mt-1 max-w-[200px] dark:text-slate-300">Selecciona un usuario de la lista para gestionar sus documentos.</p>
    </div>
</div>
