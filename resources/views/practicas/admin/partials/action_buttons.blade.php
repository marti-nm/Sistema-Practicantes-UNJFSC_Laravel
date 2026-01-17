<div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-end gap-4">
    <!-- Solo mostrar si es el estado actual de la práctica -->
    <template x-if="practicaState === currentStage">
        <div class="flex gap-4 w-full sm:w-auto">
            <form action="{{ route('proceso') }}" method="POST" class="flex-1 sm:flex-initial">
                @csrf
                <input type="hidden" name="id" value="{{ $practicaData->id }}">
                <input type="hidden" name="estado" value="rechazado">
                <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-red-50 text-red-600 font-bold text-xs uppercase tracking-widest hover:bg-red-100 hover:text-red-700 transition flex items-center justify-center gap-2">
                    <i class="bi bi-x-circle"></i> Observar
                </button>
            </form>

            <form action="{{ route('proceso') }}" method="POST" class="flex-1 sm:flex-initial">
                @csrf
                <input type="hidden" name="id" value="{{ $practicaData->id }}">
                <input type="hidden" name="estado" value="aprobado">
                <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-blue-600 text-white font-bold text-xs uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition flex items-center justify-center gap-2">
                    <i class="bi bi-check-lg"></i> Aprobar Etapa
                </button>
            </form>
        </div>
    </template>
    
    <template x-if="practicaState > currentStage">
        <div class="px-4 py-2 bg-green-50 text-green-600 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> Etapa Completada
        </div>
    </template>
     <template x-if="practicaState < currentStage">
        <div class="px-4 py-2 bg-slate-50 text-slate-400 rounded-lg text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <i class="bi bi-lock-fill"></i> Etapa Bloqueada
        </div>
    </template>
</div>
