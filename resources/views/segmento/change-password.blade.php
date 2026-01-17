@extends('template')

@section('title', 'Seguridad')

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    {{-- Alerts Container --}}
    <div class="space-y-2 mb-4">
        @if (session('info'))
            <div class="flex items-center p-3 bg-blue-50/50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/30 rounded-xl animate-fade-in-down">
                <i class="bi bi-info-circle-fill text-blue-500 mr-2 text-sm"></i>
                <p class="text-[11px] font-bold text-blue-700 dark:text-blue-300">{{ session('info') }}</p>
            </div>
        @endif

        @if (session('success'))
            <div class="flex items-center p-3 bg-emerald-50/50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 rounded-xl animate-fade-in-down">
                <i class="bi bi-check-circle-fill text-emerald-500 mr-2 text-sm"></i>
                <p class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center p-3 bg-red-50/50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 rounded-xl animate-fade-in-down">
                <i class="bi bi-exclamation-triangle-fill text-red-500 mr-2 text-sm"></i>
                <p class="text-[11px] font-bold text-red-700 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif
    </div>

    {{-- Password Card - Explicit Backgrounds --}}
    <div class="relative bg-white dark:!bg-[#0f172a] border border-slate-200 dark:border-white/5 rounded-[2rem] overflow-hidden shadow-2xl transition-all duration-300 shadow-slate-200/50 dark:shadow-none">
        
        <div class="relative p-6 md:p-8">
            {{-- Form Header --}}
            <div class="flex items-center gap-4 mb-6 border-b border-slate-100 dark:border-white/5 pb-4">
                <div class="w-10 h-10 shrink-0 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20 shadow-inner">
                    <i class="bi bi-shield-lock-fill text-lg text-primary"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Seguridad</h2>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-white/30 truncate">Cambio de Contraseña</p>
                </div>
            </div>

            <form action="{{ route('persona.update.password') }}" method="POST" class="space-y-4">
                @csrf
                
                {{-- Current Password --}}
                <div class="space-y-1.5">
                    <label for="current_password" class="block text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-white/20 ml-1">
                        Contraseña Actual
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-white/20 group-focus-within:text-primary transition-colors">
                            <i class="bi bi-key text-base"></i>
                        </div>
                        <input type="password" id="current_password" name="current_password" required
                            class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-bold placeholder:text-slate-300 dark:placeholder:text-white/5"
                            placeholder="Contraseña actual">
                    </div>
                </div>

                <div class="space-y-4 pt-2 border-t border-slate-100 dark:border-white/5">
                    {{-- New Password --}}
                    <div class="space-y-1.5">
                        <label for="new_password" class="block text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-white/20 ml-1">
                            Nueva Contraseña
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-white/20 group-focus-within:text-emerald-500 transition-colors">
                                <i class="bi bi-lock text-base"></i>
                            </div>
                            <input type="password" id="new_password" name="new_password" required
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-bold placeholder:text-slate-300 dark:placeholder:text-white/5"
                                placeholder="Mínimo 8 caracteres">
                        </div>
                    </div>

                    {{-- Confirmation --}}
                    <div class="space-y-1.5">
                        <label for="new_password_confirmation" class="block text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-white/20 ml-1">
                            Confirmar Contraseña
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-white/20 group-focus-within:text-emerald-500 transition-colors">
                                <i class="bi bi-shield-check text-base"></i>
                            </div>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-bold placeholder:text-slate-300 dark:placeholder:text-white/5"
                                placeholder="Repite la nueva">
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="pt-4">
                    <button type="submit" 
                        class="w-full py-3.5 bg-primary text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-xl transition-all duration-300 hover:bg-primary-dark hover:scale-[1.01] active:scale-95 shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
                        <i class="bi bi-shield-check-fill text-base"></i>
                        Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-5px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down {
        animation: fade-in-down 0.4s ease-out forwards;
    }
</style>
@endsection