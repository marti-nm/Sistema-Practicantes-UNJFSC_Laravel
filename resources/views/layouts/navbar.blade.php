<div id="wrapper" class="flex min-h-screen" 
     x-data="{ 
        sidebarOpen: false, 
        scrolled: false,
        openMenu: '{{ (request()->routeIs('usuarios') || request()->routeIs('usuarios.*') || request()->routeIs('docente') || request()->routeIs('docente.*') || request()->routeIs('supervisor') || request()->routeIs('supervisor.*') || request()->routeIs('estudiante') || request()->routeIs('estudiante.*') || request()->routeIs('registrar') || request()->routeIs('subadmin')) ? 'usuarios' : (request()->routeIs('Acreditar.*') || request()->routeIs('Validacion.Matricula') ? 'acreditar' : (request()->routeIs('semestre.*') || request()->routeIs('facultad.*') || request()->routeIs('escuela.*') || request()->routeIs('seccion.*') ? 'academico' : (request()->routeIs('asignacion_index') || request()->routeIs('estudiante_index') ? 'asignaciones' : (request()->routeIs('supervision') || request()->routeIs('Validacion.Matricula') || request()->routeIs('revisar.index') || request()->routeIs('evaluacionPractica.index') ? 'seguimiento' : '')))) }}' 
     }"
     @scroll.window="scrolled = (window.pageYOffset > 10)">
    


    <!-- Backdrop for Mobile -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[1029] md:hidden" 
         x-cloak></div>

    <!-- Sidebar - Estructura Flex para Scroll Interno -->
    <nav class="fixed inset-y-0 left-0 w-[260px] max-w-[80vw] bg-gradient-to-b from-[#1e3a8a] via-[#1e3a8a] to-[#1e40af] dark:from-[#0f172a] dark:via-[#0f172a] dark:to-[#020617] text-white z-[1030] transition-all duration-300 md:translate-x-0 -translate-x-full border-r flex flex-col" 
         :class="{ 
            'translate-x-0': sidebarOpen, 
            '-translate-x-full': !sidebarOpen,
            'border-white/10 dark:border-white/5 shadow-[10px_0_30px_rgba(0,0,0,0.1)] dark:shadow-[10px_0_30px_rgba(0,0,0,0.3)]': scrolled,
            'border-transparent shadow-none': !scrolled
         }"
         id="sidebar">
        
        <!-- 1. Header Fijo (Logo) -->
        <div class="flex-none px-6 pt-8 pb-4 relative z-20">
            <!-- Close Button (Mobile Only) -->
            <button @click="sidebarOpen = false" class="md:hidden absolute right-4 top-6 text-white/50 hover:text-white transition-colors">
                <i class="bi bi-x-lg text-lg"></i>
            </button>

            <div class="flex items-center gap-3.5 group">
                <div class="w-10 h-10 rounded-xl bg-white/5 p-2 flex items-center justify-center border-1 border-white/5 shadow-[inner_0_0_15px_rgba(255,255,255,0.05)] shrink-0 transition-transform group-hover:scale-105 backdrop-blur-sm">
                    <img src="{{ asset('img/ins-UNJFSC.png') }}" alt="Logo" class="w-full h-full object-contain filter drop-shadow-[0_0_8px_rgba(255,255,255,0.2)]">
                </div>
                <div class="overflow-hidden">
                    <a href="{{ route('panel') }}" class="text-[15px] font-black text-white tracking-tight decoration-none hover:text-blue-300 transition-colors block leading-tight">
                        UNJFSC
                    </a>
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-[0.2em] mt-0.5 truncate">Prácticas</p>
                </div>
            </div>
        </div>

        <!-- 2. Área Scrollable (Menú + Semestre) -->
        <div class="flex-1 overflow-y-auto sidebar px-2 pb-8 space-y-6">
            <!-- Navegación Refinada -->
            <div class="space-y-2">
                <div class="px-3 mb-3"><span class="text-[11px] font-black text-white/30 uppercase tracking-[0.15em]">Navegación</span></div>
                
                @if (Auth::user()->hasAnyRoles([1, 2, 3, 4, 5]))
                    <a href="{{ route('panel') }}" class="w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl transition-all group {{ request()->routeIs('panel') ? 'bg-white text-primary shadow-lg shadow-white/5 font-bold' : 'hover:bg-white/5 text-white/70 hover:text-white' }}">
                        <i class="bi bi-grid-fill text-xl {{ request()->routeIs('panel') ? 'text-primary' : 'text-white/70 group-hover:text-white' }}"></i>
                        <span class="text-[15px]">Dashboard</span>
                    </a>
                @endif

                <!-- Sección: Mi Espacio (Personal) -->
                @if(Auth::user()->hasAnyRoles([4]))
                    <a href="{{ route('grupo_estudiante') }}" class="w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl transition-all group {{ request()->routeIs('grupo_estudiante') ? 'bg-white text-primary shadow-lg shadow-white/5 font-bold' : 'hover:bg-white/5 text-white/70 hover:text-white' }}">
                        <i class="bi bi-people-fill text-xl {{ request()->routeIs('grupo_estudiante') ? 'text-primary' : 'text-white/70 group-hover:text-white' }}"></i>
                        <span class="text-[15px]">Mi Grupo</span>
                    </a>
                @endif
                @if(Auth::user()->hasAnyRoles([5]))
                     <a href="{{ route('matricula.estudiante') }}" class="w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl transition-all group {{ request()->routeIs('matricula.estudiante') ? 'bg-white text-primary shadow-lg shadow-white/5 font-bold' : 'hover:bg-white/5 text-white/70 hover:text-white' }}">
                        <i class="bi bi-file-earmark-check-fill text-xl {{ request()->routeIs('matricula.estudiante') ? 'text-primary' : 'text-white/70 group-hover:text-white' }}"></i>
                        <span class="text-[15px]">Mi Matrícula</span>
                    </a>
                    <a href="{{ route('practicas.estudiante') }}" class="w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl transition-all group {{ request()->routeIs('practicas.estudiante') ? 'bg-white text-primary shadow-lg shadow-white/5 font-bold' : 'hover:bg-white/5 text-white/70 hover:text-white' }}">
                        <i class="bi bi-briefcase-fill text-xl {{ request()->routeIs('practicas.estudiante') ? 'text-primary' : 'text-white/70 group-hover:text-white' }}"></i>
                        <span class="text-[15px]">Mis Prácticas</span>
                    </a>
                @endif
                <!-- Formatos (Accesible para Estudiantes/Admins) -->
                @if (Auth::user()->getRolId() == 4)
                    <a href="{{ route('evaluacionPractica.index') }}" class="w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl transition-all group {{ request()->routeIs('evaluacionPractica.index') ? 'bg-white text-primary shadow-lg shadow-white/5 font-bold' : 'hover:bg-white/5 text-white/70 hover:text-white' }}">
                        <i class="bi bi-file-text-fill text-xl {{ request()->routeIs('evaluacionPractica.index') ? 'text-primary' : 'text-white/70 group-hover:text-white' }}"></i>
                        <span class="text-[15px]">Fichas de Práctica</span>
                    </a>
                @endif
            </div>

            <!-- Sección: Supervisión (Docentes/Supervisores/Admins) -->
            @if (Auth::user()->hasAnyRoles([1, 2, 3]))
                <div class="space-y-1 pt-6 border-t border-white/5">
                    <div class="px-2 mb-3"><span class="text-[11px] font-black text-white/30 uppercase tracking-[0.15em]">Supervisión</span></div>
                    
                    <!-- Validaciones Accordion -->
                    <button @click="openMenu = (openMenu === 'acreditar' ? '' : 'acreditar')" 
                             class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl text-white/70 hover:text-white hover:bg-white/5 transition-all group outline-none">
                        <div class="flex items-center gap-3.5">
                            <i class="bi bi-patch-check-fill text-xl text-white/70 group-hover:text-white"></i>
                            <span class="text-[15px] font-semibold">Validaciones</span>
                        </div>
                        <i class="bi bi-chevron-down text-[10px] transition-transform duration-300 opacity-50 px-2" :class="{ 'rotate-180': openMenu === 'acreditar' }"></i>
                    </button>
                    <div x-show="openMenu === 'acreditar'" x-cloak class="pl-11 pr-2 space-y-1">
                        @if(Auth::user()->getRolId() == 4)
                             <a href="{{ route('acreditar') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('acreditar') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Mi Acreditación</a>
                        @endif
                        @if (Auth::user()->hasAnyRoles([1, 2]))
                            <a href="{{ route('Acreditar.Docente') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('Acreditar.Docente') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Docente Titular</a>
                        @endif
                        <a href="{{ route('Acreditar.Supervisor') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('Acreditar.Supervisor') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Docente Supervisor</a>
                        <a href="{{ route('Validacion.Matricula') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('Validacion.Matricula') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Matrícula Estudiante</a>
                    </div>
                    
                    <!-- Grupos Accordion -->
                    @if (Auth::user()->getRolId() == 1 || Auth::user()->hasAnyRoles([1, 2, 3]))
                        <button @click="openMenu = (openMenu === 'asignaciones' ? '' : 'asignaciones')" 
                                class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl text-white/70 hover:text-white hover:bg-white/5 transition-all group outline-none">
                            <div class="flex items-center gap-3.5">
                                <i class="bi bi-people-fill text-xl text-white/70 group-hover:text-white"></i>
                                <span class="text-[15px] font-semibold">Grupos</span>
                            </div>
                            <i class="bi bi-chevron-down text-[10px] transition-transform duration-300 opacity-50" :class="{ 'rotate-180': openMenu === 'asignaciones' }"></i>
                        </button>
                        <div x-show="openMenu === 'asignaciones'" x-cloak class="pl-11 pr-2 space-y-1">
                            <a href="{{ route('asignacion_index') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('asignacion_index') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Por Práctica</a>
                            <a href="{{ route('estudiante_index') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('estudiante_index') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Por Estudiante</a>
                        </div>
                    @endif

                    <!-- Seguimiento Accordion -->
                    <button @click="openMenu = (openMenu === 'seguimiento' ? '' : 'seguimiento')" 
                            class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl text-white/70 hover:text-white hover:bg-white/5 transition-all group outline-none">
                        <div class="flex items-center gap-3.5">
                            <i class="bi bi-clipboard-data-fill text-xl text-white/70 group-hover:text-white"></i>
                            <span class="text-[15px] font-semibold">Seguimiento</span>
                        </div>
                        <i class="bi bi-chevron-down text-[10px] transition-transform duration-300 opacity-50" :class="{ 'rotate-180': openMenu === 'seguimiento' }"></i>
                    </button>
                    <div x-show="openMenu === 'seguimiento'" x-cloak class="pl-11 pr-2 space-y-1">
                         <a href="{{ route('supervision') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('supervision') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Prácticas</a>
                        <a href="{{ route('evaluacionPractica.index') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('evaluacionPractica.index') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Evaluación Prácticas</a>
                        <a href="{{ route('revisar.index') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('revisar.index') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Revisión Prácticas</a>
                    </div>
                </div>
            @endif

            <!-- Sección: Administración (Gestión Global) -->
            @if (Auth::user()->hasAnyRoles([1, 2, 3]))
                <div class="space-y-1 pt-6 border-t border-white/5">
                    <div class="px-2 mb-3"><span class="text-[11px] font-black text-white/30 uppercase tracking-[0.15em]">Gestión</span></div>

                    <button @click="openMenu = (openMenu === 'usuarios' ? '' : 'usuarios')" 
                            class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl text-white/70 hover:text-white hover:bg-white/5 transition-all group outline-none">
                        <div class="flex items-center gap-3.5">
                            <i class="bi bi-person-badge-fill text-xl text-white/70 group-hover:text-white"></i>
                            <span class="text-[15px] font-semibold">Usuarios</span>
                        </div>
                        <i class="bi bi-chevron-down text-[10px] transition-transform duration-300 opacity-50" :class="{ 'rotate-180': openMenu === 'usuarios' }"></i>
                    </button>
                    <div x-show="openMenu === 'usuarios'" x-cloak class="pl-11 pr-2 space-y-1">
                        @if (Auth::user()->hasAnyRoles([1, 2, 3]))
                            <a href="{{ route('registrar') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('registrar') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Nuevo Usuario</a>
                        @endif
                        @if (Auth::user()->getRolId() == 1)
                            <a href="{{ route('usuarios') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('usuarios') || request()->routeIs('usuarios.*') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Lista General</a>
                            <a href="{{ route('subadmin') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('subadmin') || request()->routeIs('docente.facultad') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Admin. Facultades</a>
                        @endif
                         <a href="{{ route('docente') }}" class="block py-2 px-3 transition-all {{ (request()->routeIs('docente') || request()->routeIs('docente.*')) && !request()->routeIs('docente.facultad') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Docentes</a>
                        <a href="{{ route('supervisor') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('supervisor') || request()->routeIs('supervisor.*') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Supervisores</a>
                        <a href="{{ route('estudiante') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('estudiante') || request()->routeIs('estudiante.*') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Estudiantes</a>
                    </div>

                    @if (Auth::user()->getRolId() == 1)
                        <button @click="openMenu = (openMenu === 'academico' ? '' : 'academico')" 
                                class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl text-white/70 hover:text-white hover:bg-white/5 transition-all group outline-none">
                            <div class="flex items-center gap-3.5">
                                <i class="bi bi-building-fill text-xl text-white/70 group-hover:text-white"></i>
                                <span class="text-[15px] font-semibold">Académico</span>
                            </div>
                            <i class="bi bi-chevron-down text-[10px] transition-transform duration-300 opacity-50" :class="{ 'rotate-180': openMenu === 'academico' }"></i>
                        </button>
                        <div x-show="openMenu === 'academico'" x-cloak class="pl-11 pr-2 space-y-1">
                            <a href="{{ route('semestre.index') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('semestre.index') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Semestres</a>
                            <a href="{{ route('facultad.index') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('facultad.index') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Facultades</a>
                            <a href="{{ route('escuela.index') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('escuela.index') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Escuelas</a>
                            <a href="{{ route('seccion.index') }}" class="block py-2 px-3 transition-all {{ request()->routeIs('seccion.index') ? 'bg-white text-primary rounded-lg font-bold shadow-sm' : 'text-white/50 hover:text-white text-[12.5px]' }}">Secciones</a>
                        </div>
                    @endif
                </div>
            @endif
            <div class="space-y-2">
                <div class="px-3 mb-3"><span class="text-[11px] font-black text-white/30 uppercase tracking-[0.15em]">Recursos</span></div>
                 <a href="{{ route('recursos') }}" class="w-full flex items-center gap-3.5 px-4 py-3.5 rounded-xl transition-all group {{ request()->routeIs('recursos') ? 'bg-white text-primary shadow-lg shadow-white/5 font-bold' : 'hover:bg-white/5 text-white/70 hover:text-white' }}">
                    <i class="bi bi-folder-fill text-xl {{ request()->routeIs('recursos') ? 'text-primary' : 'text-white/70 group-hover:text-white' }}"></i>
                    <span class="text-[15px]">Recursos</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="flex-1 min-w-0 transition-all duration-300 md:pl-[260px] flex flex-col">
            <!-- Topbar - Full Tailwind & Alpine.js -->
            <header class="sticky top-0 z-[1029] px-4 md:px-6 py-2.5 flex items-center justify-between min-h-[70px] transition-all duration-300 border-b border-transparent"
                    :class="scrolled ? 'navbar-scrolled shadow-sm' : 'bg-transparent border-transparent'">
                <!-- Left Section: Title & Mobile Toggle -->
                <div class="flex items-center gap-3">
                    <button class="md:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors" 
                            @click="sidebarOpen = !sidebarOpen" type="button">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                    <div class="hidden sm:block">
                        <h1 class="text-xl font-black text-slate-800 dark:text-white leading-tight tracking-tight">@yield('title', 'Dashboard')</h1>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">@yield('subtitle', 'Sistema de Gestión de Prácticas')</p>
                    </div>
                </div>

                <!-- Right Section: Semestre, Notifications & Profile -->
                <div class="flex items-center gap-2 md:gap-5">
                    
                    <!-- Semestre Pill (Compact) -->
                     @php
                        $semestre_activo = session('semestre_actual_id');
                        $semestre = App\Models\Semestre::find($semestre_activo);
                    @endphp
                    <div class="hidden lg:flex items-center gap-4 bg-slate-100 dark:bg-white/5 rounded-2xl px-1 py-1 pr-5 transition-all hover:bg-slate-200 dark:hover:bg-white/10">
                         <div class=" dark:bg-white/5 px-4 py-2 rounded-xl shadow-sm flex items-center gap-2.5">
                             <div class="w-2 h-2 rounded-full {{ !$semestre_bloqueado ? 'bg-emerald-500' : 'bg-rose-500' }} animate-pulse shadow-[0_0_8px_currentColor]"></div>
                             <span class="text-sm font-black text-slate-700 dark:text-white leading-none tracking-tight">{{ $semestre->codigo ?? '---' }}</span>
                         </div>
                         
                         @if(Auth::user()->getRolId() == 1 || Auth::user()->getRolId() == 2)
                             <div class="relative group flex items-center">
                                 <select id="semestreSelect" onchange="saveSemestre(this.value)" 
                                         class="bg-transparent text-sm font-bold text-slate-600 dark:text-slate-300 focus:outline-none cursor-pointer hover:text-blue-500 transition-colors pr-3 appearance-none">
                                     <option value="" disabled selected class="text-slate-400">Cambiar...</option>
                                 </select>
                                 <i class="bi bi-chevron-down absolute right-0 text-[9px] text-slate-400 pointer-events-none group-hover:text-blue-500"></i>
                             </div>
                         @else
                            <span class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ !$semestre_bloqueado ? 'ACTIVO' : 'CERRADO' }}</span>
                         @endif
                    </div>
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode" 
                            class="p-2.5 rounded-xl text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all group focus:outline-none"
                            title="Cambiar Tema">
                        <i x-show="!darkMode" x-cloak class="bi bi-moon-stars-fill text-xl"></i>
                        <i x-show="darkMode" x-cloak class="bi bi-sun-fill text-xl text-amber-500"></i>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button class="relative p-2.5 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all group" 
                                @click="open = !open" type="button">
                            <i class="bi bi-bell-fill text-xl group-hover:scale-110 transition-transform"></i>
                            <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
                        </button>
                        
                        <div x-show="open" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-3 w-[320px] bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-xl border-1 border-slate-100 dark:border-white/5 py-3 overflow-hidden z-20">
                            <h6 class="px-5 py-2 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Notificaciones</h6>
                            <div class="my-2 border-t border-slate-50 dark:border-white/5"></div>
                            <div class="max-h-[300px] overflow-y-auto">
                                <a href="#" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0"><i class="bi bi-file-earmark-check-fill"></i></div>
                                    <div><p class="text-sm font-bold text-slate-700 dark:text-slate-200 m-0">Nueva práctica registrada</p><p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Hace 5 minutos</p></div>
                                </a>
                                <a href="#" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0"><i class="bi bi-star-fill"></i></div>
                                    <div><p class="text-sm font-bold text-slate-700 dark:text-slate-200 m-0">Evaluación pendiente</p><p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Hace 20 minutos</p></div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <div class="flex items-center gap-3 p-1.5 md:pl-4 rounded-2xl dark:bg-white/5 dark:hover:bg-white/10 dark:border-transparent dark:hover:border-transparent cursor-pointer transition-all" 
                             @click="open = !open">
                            <div class="hidden md:block text-right">
                                <div class="text-sm font-black text-slate-700 dark:text-slate-200 leading-none">{{ Auth::user()->persona->nombres ?? 'Usuario' }}</div>
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-1.5 leading-none">{{ Auth::user()->getRolName() ?? 'Invitado' }}</div>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center text-white shadow-lg shadow-blue-600/20 overflow-hidden shrink-0">
                                @if (Auth::user()->persona->ruta_foto)
                                    <img src="{{ asset(Auth::user()->persona->ruta_foto) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <span class="font-black text-lg">{{ substr(Auth::user()->persona->nombres ?? 'U', 0, 1) }}</span>
                                @endif
                            </div>
                            <i class="bi bi-chevron-down text-[10px] text-slate-400 hidden sm:block transition-transform" :class="{ 'rotate-180': open }"></i>
                        </div>
                        
                        <div x-show="open" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-3 w-[250px] bg-slate-50 dark:bg-slate-900 rounded-2xl shadow-xl border-1 border-slate-100 dark:border-white/5 py-3 z-20 overflow-hidden">
                            <a href="{{ route('perfil') }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-300 transition-colors group">
                                <i class="bi bi-person-fill text-xl opacity-50 group-hover:opacity-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-all"></i>
                                <span class="text-sm font-bold">Mi Perfil</span>
                            </a>
                            <a href="{{ route('persona.change.password.view') }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-white/5 text-slate-600 dark:text-slate-300 transition-colors group">
                                <i class="bi bi-shield-lock-fill text-xl opacity-50 group-hover:opacity-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-all"></i>
                                <span class="text-sm font-bold">Contraseña</span>
                            </a>
                            <div class="my-2 border-t border-slate-50 dark:border-white/5"></div>
                            <a href="{{ route('cerrarSecion') }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-rose-50 dark:hover:bg-rose-900/20 text-rose-600 dark:text-rose-400 transition-colors group">
                                <i class="bi bi-box-arrow-right text-xl opacity-70 group-hover:opacity-100"></i>
                                <span class="text-sm font-bold">Cerrar Sesión</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
<!-- Agregar el script para cargar los semestres dinámicamente -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/semestres')
            .then(response => response.json())
            .then(data => {
                const semestreSelect = document.getElementById('semestreSelect');
                const semestreActiveId = {{ session('semestre_actual_id', 'null') }};
                data.forEach(semestre => {
                    const option = document.createElement('option');
                    option.value = semestre.id; // Ajusta la URL según sea necesario
                    option.textContent = semestre.codigo;

                    if(semestreActiveId && semestre.id === semestreActiveId) {
                        option.selected = true;
                    }
                    semestreSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar los semestres:', error));
    });

    function saveSemestre(semestreId) {
        if (!semestreId) return;

        // probar la seleecion
        //alert('Semestre seleccionado: ' + semestreId);
        // Route::get('/semestre/set-active/{id}' la ruta para actualizar el semestre en la session
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/semestre/set-active/${semestreId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                // Recargar la página para reflejar el cambio
                window.location.reload();
            } else {
                console.error('Error al actualizar el semestre actual.');
                alert('Error al actualizar el semestre actual.');
            }
        })
        .catch(error => {
            console.error('Error al actualizar el semestre actual:', error);
            alert('Error al actualizar el semestre actual.');
        });
    }
</script>