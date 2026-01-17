@props([
    'title' => 'Docentes',
    'subtitle' => 'Docentes',
    'icon' => 'bi-mortarboard-fill',
    'enableButton' => false,
    'typeButton' => 1,
    'msj' => 'Registrar Docente',
    'icon_msj' => 'bi-mortarboard-fill',
    'route' => 'registrar',
    'function' => 'newModal = true',
])

<!-- Header Section -->
<div class="flex items-center gap-3 mb-6 justify-between">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
            <i class="{{ $icon }} text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">{{ $title }}</h2>
            <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">{{ $subtitle }}</p>
        </div>
    </div>
    @if($enableButton && $typeButton == 1)
    <a href="{{ route($route) }}" class="px-3 py-2 bg-primary text-white font-black rounded-xl hover:bg-primary-dark transition-all shadow-xl shadow-primary/20 flex items-center gap-3 active:scale-95">
        <i class="bi bi-plus-lg text-lg"></i>
        <span class="tracking-widest uppercase text-xs">{{ $msj }}</span>
    </a>
    @elseif($enableButton && $typeButton == 2)
        <button @click="{{ $function }}" class="px-3 py-2 bg-primary text-white font-black rounded-xl hover:bg-primary-dark transition-all shadow-xl shadow-primary/20 flex items-center gap-3 active:scale-95">
            <i class="bi bi-plus-lg text-lg"></i>
            <span class="tracking-widest uppercase text-xs">{{ $msj }}</span>
        </button>
    @endif
</div>