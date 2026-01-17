{{-- Componente de Filtros de Búsqueda - Rediseñado con Tailwind CSS --}}
@props([
    'route',
    'facultades'
])

<style>
    .dark select option {
        background-color: #0f172a;
        color: white;
    }
    /* Force border removal in dark mode to override potential Bootstrap conflicts */
    .dark #facultad, .dark #escuela, .dark #seccion {
        border-width: 0 !important;
        border-style: none !important;
        border-color: transparent !important;
        box-shadow: none !important;
        outline: none !important;
    }
</style>

<div x-data="{ 
    showFilters: window.innerWidth >= 768,
    init() {
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) this.showFilters = true;
        });
    }
}" 
     class="relative dark:bg-[#0f172a] rounded-2xl shadow-sm mb-6 transition-all duration-300 overflow-hidden">
    
    {{-- Header / Toggle Button for Mobile --}}
    <div @click="if (window.innerWidth < 768) showFilters = !showFilters" 
         class="flex items-center justify-between px-3 py-4 cursor-pointer md:cursor-default group border-b border-transparent tracking-tight"
         :class="{ 'border-slate-100 dark:border-transparent bg-slate-50/50 dark:bg-white/[0.02]': showFilters }">
        
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                <i class="bi bi-funnel-fill"></i>
            </div>
            <h6 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider m-0">
                Filtros de Búsqueda
            </h6>
        </div>

        <div class="flex items-center gap-2 md:hidden">
            <span class="text-[10px] font-black uppercase text-slate-400 dark:text-white/30" x-text="showFilters ? 'Ocultar' : 'Mostrar'"></span>
            <i class="bi bi-chevron-down text-slate-400 dark:text-white/30 transition-transform duration-300" :class="{ 'rotate-180': showFilters }"></i>
        </div>
    </div>

    {{-- Filters Form Body --}}
    <div x-show="showFilters" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="p-3 md:p-6">
        
        <form method="GET" action="{{ route($route) }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
                
                {{-- Facultad --}}
                <div class="space-y-1.5">
                    <label for="facultad" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-white/30 ml-1">
                        Facultad
                    </label>
                    <div class="relative group">
                        <select id="facultad" name="facultad" 
                            class="block w-full pl-4 pr-10 py-2.5 bg-slate-50/50 dark:bg-white/5 border border-slate-200 dark:!border-0 rounded-lg text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-bold appearance-none cursor-pointer">
                            <option value="">-- Todas --</option>
                            @foreach($facultades as $fac)
                                <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 dark:text-white/20">
                            <i class="bi bi-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>

                {{-- Escuela --}}
                <div class="space-y-1.5">
                    <label for="escuela" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-white/30 ml-1">
                        Escuela
                    </label>
                    <div class="relative group">
                        <select id="escuela" name="escuela" 
                            class="block w-full pl-4 pr-10 py-2.5 bg-slate-50/50 dark:bg-white/5 border border-slate-200 dark:!border-0 rounded-lg text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-bold appearance-none cursor-pointer">
                            <option value="">-- Todas --</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 dark:text-white/20">
                            <i class="bi bi-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>

                {{-- Seccion --}}
                <div class="space-y-1.5">
                    <label for="seccion" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-white/30 ml-1">
                        Sección
                    </label>
                    <div class="relative group">
                        <select id="seccion" name="seccion" 
                            class="block w-full pl-4 pr-10 py-2.5 bg-slate-50/50 dark:bg-white/5 border border-slate-200 dark:!border-0 rounded-lg text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-bold appearance-none cursor-pointer">
                            <option value="">-- Todos --</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 dark:text-white/20">
                            <i class="bi bi-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="pt-2 md:pt-0">
                    <button type="submit" 
                        class="w-full relative py-2 bg-primary text-white font-black text-[11px] uppercase tracking-[0.2em] rounded-xl transition-all duration-300 hover:bg-primary-dark hover:scale-[1.02] active:scale-95 shadow-lg shadow-primary/20 flex items-center justify-center gap-2 overflow-hidden group">
                        <i class="bi bi-filter-circle-fill text-base transition-transform group-hover:rotate-90 duration-500"></i>
                        Filtrar Datos
                        {{-- Button Shine Effect --}}
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full group-hover:animate-shimmer"></div>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const facultadSelect = document.getElementById('facultad');
    const escuelaSelect = document.getElementById('escuela');
    const seccionSelect = document.getElementById('seccion');
    const semestreActivoId = {{ session('semestre_actual_id') ?? 'null' }};

    facultadSelect.addEventListener('change', function () {
        const facultadId = this.value;
        
        // Reset dependants
        escuelaSelect.innerHTML = '<option value="">-- Todas --</option>';
        seccionSelect.innerHTML = '<option value="">-- Todos --</option>';

        if (!facultadId) {
            return;
        }

        escuelaSelect.innerHTML = '<option value="">Cargando...</option>';
        fetch(`/api/escuelas/${facultadId}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Todas --</option>';
                data.forEach(e => {
                    options += `<option value="${e.id}">${e.name}</option>`;
                });
                escuelaSelect.innerHTML = options;
            })
            .catch(() => {
                escuelaSelect.innerHTML = '<option value="">Error al cargar</option>';
            });
    });

    escuelaSelect.addEventListener('change', function () {
        const escuelaId = this.value;
        seccionSelect.innerHTML = '<option value="">-- Todos --</option>';

        if (!escuelaId || !semestreActivoId) {
            return;
        }

        seccionSelect.innerHTML = '<option value="">Cargando...</option>';
        fetch(`/api/secciones/${escuelaId}/${semestreActivoId}`) 
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">-- Todos --</option>';
                data.forEach(d => {
                    options += `<option value="${d.id}">${d.name}</option>`;
                });
                seccionSelect.innerHTML = options;
            })
            .catch(() => {
                seccionSelect.innerHTML = '<option value="">Error al cargar</option>';
            });
    });
});
</script>