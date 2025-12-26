<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Prácticas Pre Profesionales - Universidad Nacional José Faustino Sánchez Carrión">
    <title>Sistema de Prácticas | UNJFSC</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- AOS Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#2563eb',
                            dark: '#1e40af',
                            light: '#eff6ff',
                        },
                        secondary: '#0f172a',
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        jakarta: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @livewireStyles

    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply font-outfit text-slate-900 bg-white dark:bg-slate-950 dark:text-slate-100 transition-colors duration-300;
            }
            h1, h2, h3, h4, h5, h6 {
                @apply font-jakarta font-bold;
            }
        }
        
        @layer components {
            .nav-link {
                @apply text-sm font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 hover:text-primary transition-colors cursor-pointer;
            }
            .btn-access {
                @apply px-8 py-2.5 rounded-lg bg-secondary text-white font-bold text-sm hover:bg-primary dark:bg-primary dark:hover:bg-primary-dark transition-all transform hover:-translate-y-0.5 shadow-lg hover:shadow-primary/30;
            }
            .section-padding {
                @apply py-24;
            }
            .glass-nav {
                @apply bg-white/80 dark:bg-slate-950/80 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-800/50 shadow-sm;
            }
        }
    </style>
</head>
<body x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Navbar -->
    <nav 
        :class="{ 'glass-nav py-3': scrolled, 'bg-transparent py-6': !scrolled }"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-4 group">
                    <img src="{{ asset('img/ins-UNJFSC.png') }}" alt="UNJFSC" class="h-12 w-auto transition-transform group-hover:scale-110">
                    <div class="leading-tight">
                        <p class="text-lg font-extrabold text-secondary dark:text-white tracking-tighter">Sistema de Prácticas</p>
                        <p class="text-[10px] font-bold text-primary uppercase tracking-widest">U.N. José Faustino Sánchez Carrión</p>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#inicio" class="nav-link">Inicio</a>
                    <a href="#nosotros" class="nav-link">Nosotros</a>
                    <a href="#roles" class="nav-link">Roles</a>
                    <a href="#preguntas" class="nav-link">Ayuda</a>
                    
                    <!-- Theme Toggle -->
                    <div class="flex items-center gap-3 px-4 border-l border-slate-200 dark:border-slate-800 ml-4">
                        <i class="bi bi-sun text-amber-500"></i>
                        <button 
                            @click="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors focus:outline-none"
                        >
                            <span 
                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                :class="document.documentElement.classList.contains('dark') ? 'translate-x-6' : 'translate-x-1'"
                            ></span>
                        </button>
                        <i class="bi bi-moon text-indigo-400"></i>
                    </div>

                    <a href="{{ route('login') }}" class="btn-access uppercase tracking-widest text-[11px]">Acceder</a>
                </div>

                <!-- Mobile Toggle -->
                <div class="lg:hidden flex items-center gap-4">
                    <button @click="mobileMenu = !mobileMenu" class="text-secondary dark:text-white text-2xl">
                        <i class="bi" :class="mobileMenu ? 'bi-x-lg' : 'bi-list'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div 
            x-show="mobileMenu" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="lg:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-6 shadow-2xl"
        >
            <div class="flex flex-col gap-4 text-center">
                <a href="#inicio" @click="mobileMenu = false" class="nav-link py-2">Inicio</a>
                <a href="#nosotros" @click="mobileMenu = false" class="nav-link py-2">Nosotros</a>
                <a href="#roles" @click="mobileMenu = false" class="nav-link py-2">Roles</a>
                <a href="#preguntas" @click="mobileMenu = false" class="nav-link py-2">Ayuda</a>
                <div class="h-px bg-slate-100 dark:bg-slate-800 my-2"></div>
                <a href="{{ route('login') }}" class="btn-access py-3">Acceder</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="inicio" class="relative pt-40 pb-20 overflow-hidden bg-white dark:bg-slate-950">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-1/2 h-full bg-primary/5 blur-[120px] rounded-full -translate-y-1/2 translate-x-1/4"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2" data-aos="fade-right">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-primary-light dark:bg-primary/10 text-primary font-bold text-xs uppercase tracking-widest mb-6">
                        Universidad Nacional José Faustino Sánchez Carrión
                    </span>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-secondary dark:text-white leading-[1.1] mb-6 tracking-tight">
                        Sistema de <br>
                        <span class="text-primary italic">Prácticas Pre Profesionales</span>
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-10 max-w-xl leading-relaxed">
                        Plataforma centralizada para la optimización administrativa y el seguimiento académico 
                        de todas las facultades de la UNJFSC. Un ecosistema diseñado para 
                        conectar talento estudiantil con el sector empresarial.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#roles" class="px-8 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-all shadow-xl shadow-primary/20">
                            Explorar Funcionalidades
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-secondary dark:text-white font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                            Iniciar Sesión
                        </a>
                    </div>
                </div>

                <!-- Hero Illustration (SVG) -->
                <div class="lg:w-1/2" data-aos="zoom-in" data-aos-delay="200">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-primary/10 rounded-[4rem] blur-2xl animate-pulse"></div>
                        <svg viewBox="0 0 500 500" class="w-full h-auto drop-shadow-2xl relative z-10 animate-[floating_4s_ease-in-out_infinite]">
                            <defs>
                                <linearGradient id="heroGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#2563eb;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <rect x="100" y="100" width="300" height="300" rx="60" fill="url(#heroGrad)" />
                            <rect x="150" y="180" width="200" height="20" rx="10" fill="white" opacity="0.9" />
                            <rect x="150" y="220" width="140" height="20" rx="10" fill="white" opacity="0.6" />
                            <rect x="150" y="260" width="100" height="20" rx="10" fill="white" opacity="0.4" />
                            <circle cx="340" cy="300" r="40" fill="white" opacity="0.8" />
                            <path d="M325 300 L335 310 L355 290" fill="none" stroke="#2563eb" stroke-width="6" stroke-linecap="round" />
                        </svg>
                        
                        <!-- Floating Card -->
                        <div class="absolute top-10 right-0 bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-800 translate-x-1/4" data-aos="fade-left" data-aos-delay="600">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-500 text-white p-2 rounded-full"><i class="bi bi-shield-check"></i></div>
                                <div class="text-left">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Estado</p>
                                    <p class="text-sm font-bold dark:text-white">Sistema Oficial</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mision & Vision Section -->
    <section id="nosotros" class="section-padding bg-slate-50 dark:bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="bg-white dark:bg-slate-800 p-12 rounded-[3rem] shadow-xl border border-slate-100 dark:border-slate-700" data-aos="fade-up">
                    <div class="h-14 w-14 bg-primary text-white rounded-2xl flex items-center justify-center text-2xl mb-8 shadow-lg shadow-primary/30">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h2 class="text-3xl font-extrabold mb-6 dark:text-white tracking-tight">Misión Académica</h2>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-lg">
                        Formar profesionales líderes en sus respectivas disciplinas, con sólidos valores éticos y compromiso social, capaces de generar conocimiento científico y tecnológico para el desarrollo sostenible de la región y el país.
                    </p>
                </div>

                <div class="bg-secondary p-12 rounded-[3rem] shadow-2xl text-white relative overflow-hidden group" data-aos="fade-up" data-aos-delay="200">
                    <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all"></div>
                    <div class="h-14 w-14 bg-white/10 text-primary rounded-2xl flex items-center justify-center text-2xl mb-8 border border-white/20">
                        <i class="bi bi-globe-americas"></i>
                    </div>
                    <h2 class="text-3xl font-extrabold mb-6 tracking-tight">Visión Consolidada</h2>
                    <p class="text-slate-300 leading-relaxed text-lg">
                        Ser una universidad referente nacional e internacional en calidad educativa, investigación e innovación, reconocida por su contribución al bienestar de la sociedad y la formación de ciudadanos del mundo.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles & Features -->
    <section id="roles" class="section-padding bg-white dark:bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-secondary dark:text-white mb-4 tracking-tight">Estructura de la Plataforma</h2>
                <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto italic">Servicios inteligentes diseñados para facilitar la gestión académica.</p>
            </div>

            <div x-data="{ role: 'est' }" class="bg-slate-50 dark:bg-slate-900 rounded-[3rem] p-8 lg:p-12 shadow-inner border border-slate-200/50 dark:border-slate-800/50">
                <!-- Tab Headers -->
                <div class="flex justify-center mb-12">
                    <div class="bg-white dark:bg-slate-800 p-2 rounded-2xl shadow-sm flex gap-2">
                        <button @click="role = 'est'" :class="role === 'est' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'" class="px-8 py-3 rounded-xl font-bold transition-all">
                            Estudiante
                        </button>
                        <button @click="role = 'doc'" :class="role === 'doc' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'" class="px-8 py-3 rounded-xl font-bold transition-all">
                            Docente
                        </button>
                        <button @click="role = 'emp'" :class="role === 'emp' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'" class="px-8 py-3 rounded-xl font-bold transition-all">
                            Empresa
                        </button>
                    </div>
                </div>

                <!-- Tab Panels -->
                <div class="grid lg:grid-cols-1 gap-12 items-center">
                    <div class="space-y-6">
                        <div x-show="role === 'est'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
                            <h3 class="text-3xl font-extrabold text-secondary dark:text-white mb-8 tracking-tight">Servicios para el Estudiante</h3>
                            <ul class="grid md:grid-cols-2 gap-4">
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-person-check text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Registro de datos personales y académicos</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-file-earmark-arrow-up text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Carga de documentos de acreditación</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-calendar-event text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Seguimiento de la etapa de desarrollo</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-journal-check text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Registro de informes y evidencias</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-award text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Consulta de calificación y estado final</span>
                                </li>
                            </ul>
                        </div>
                        <div x-show="role === 'doc'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
                            <h3 class="text-3xl font-extrabold text-secondary dark:text-white mb-8 tracking-tight">Herramientas del Docente</h3>
                            <ul class="grid md:grid-cols-2 gap-4">
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-people text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Gestión de grupos de práctica asignados</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-shield-check text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Validación de documentos de estudiantes</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-eye text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Monitoreo presencial y remoto (Supervisión)</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-clipboard-data text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Calificación de etapas y resultados</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-file-earmark-medical text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Generación de actas de evaluación</span>
                                </li>
                            </ul>
                        </div>
                        <div x-show="role === 'emp'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
                            <h3 class="text-3xl font-extrabold text-secondary dark:text-white mb-8 tracking-tight">Vinculación Empresarial</h3>
                            <ul class="grid md:grid-cols-2 gap-4">
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-building text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Registro de Razón Social y datos de sede</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-person-badge text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Identificación de tutores y jefes directos</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-file-earmark-richtext text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Validación de convenios institucionales</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-geo-alt text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Control de ubicación de centros de práctica</span>
                                </li>
                                <li class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:translate-x-2 transition-transform">
                                    <i class="bi bi-briefcase text-primary text-xl"></i>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">Banco de datos de empresas receptoras</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Stages Section -->
    <section class="section-padding bg-slate-50 dark:bg-slate-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-extrabold text-secondary dark:text-white mb-4 tracking-tight">Etapas del Proceso de Prácticas</h2>
            <p class="text-slate-500 mb-16">Estructura administrativa oficial de la UNJFSC.</p>
            
            <div class="grid md:grid-cols-3 gap-8 text-center">
                <div class="p-8 bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-700" data-aos="fade-up">
                    <div class="h-16 w-16 bg-blue-100 dark:bg-blue-900/30 text-primary rounded-full flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-4 dark:text-white">Acreditación</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Registro y validación de los tres pilares: Estudiantes, Docentes y Empresas (Razón Social).</p>
                </div>
                <div class="p-8 bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="100">
                    <div class="h-16 w-16 bg-blue-100 dark:bg-blue-900/30 text-primary rounded-full flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-4 dark:text-white">Desarrollo</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Gestión técnica de prácticas, organización de grupos, monitoreo académico y carga delegada de evidencias.</p>
                </div>
                <div class="p-8 bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-700" data-aos="fade-up" data-aos-delay="200">
                    <div class="h-16 w-16 bg-blue-100 dark:bg-blue-900/30 text-primary rounded-full flex items-center justify-center text-3xl mx-auto mb-6">
                        <i class="bi bi-file-earmark-lock2"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-4 dark:text-white">Finalización</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Revisión integral de informes finales, evaluación de calificación de nota y cierre del expediente académico.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section (Tailwind + Alpine) -->
    <section id="preguntas" class="section-padding bg-white dark:bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-16 items-start">
                <div class="lg:w-5/12" data-aos="fade-right">
                    <h2 class="text-4xl font-extrabold text-secondary dark:text-white mb-6 tracking-tight">Preguntas Frecuentes</h2>
                    <p class="text-slate-500 dark:text-slate-400 mb-10 text-lg">Resolvemos tus dudas principales sobre el proceso de prácticas en la universidad.</p>
                    <div class="bg-primary/5 dark:bg-primary/10 p-8 rounded-[2rem] border border-primary/10">
                        <h5 class="text-primary font-bold mb-3 flex items-center gap-2">
                            <i class="bi bi-info-circle text-xl"></i>
                            ¿Necesitas más ayuda?
                        </h5>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                            Si no encuentras lo que buscas, puedes contactar con la oficina de tu facultad o revisar los manuales en la sección de recursos.
                        </p>
                    </div>
                </div>

                <div class="lg:w-7/12 w-full" data-aos="fade-left">
                    <div x-data="{ active: 0 }" class="space-y-4">
                        <template x-for="(q, i) in [
                            { t: '¿Qué tiempo duran las prácticas pre profesionales?', a: 'Tienen una duración mínima de 4 meses (320 horas) hasta un máximo de 6 meses, dependiendo del plan de estudios de cada escuela profesional.' },
                            { t: '¿Qué tipo de prácticas existen?', a: 'Existen dos modalidades: Prácticas por Desarrollo (durante el semestre) y Prácticas por Convalidación (experiencia laboral previa).' },
                            { t: '¿Cuáles son los requisitos para iniciar prácticas?', a: 'Estar matriculado en el semestre actual, haber aprobado los cursos prerequisitos según el plan de estudios y contar con el visto bueno del docente supervisor.' },
                            { t: '¿Cómo obtengo mis credenciales de acceso?', a: 'Son proporcionadas por la oficina de prácticas de cada facultad. El usuario es el correo institucional y la contraseña inicial es el DNI.' }
                        ]">
                            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-900/20 transition-all shadow-sm" :class="active === i ? 'ring-2 ring-primary/20 border-primary/30' : ''">
                                <button 
                                    @click="active = (active === i ? null : i)"
                                    class="w-full px-6 py-5 text-left flex justify-between items-center group"
                                >
                                    <span class="font-bold text-slate-800 dark:text-slate-200 group-hover:text-primary transition-colors pr-8 leading-snug" x-text="q.t"></span>
                                    <div class="h-8 w-8 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 shadow-sm transition-transform" :class="active === i ? 'rotate-180 bg-primary text-white' : 'text-slate-400'">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </button>
                                <div 
                                    x-show="active === i" 
                                    x-collapse
                                    class="px-6 pb-6 text-slate-600 dark:text-slate-400 leading-relaxed"
                                    x-text="q.a"
                                ></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 bg-slate-950 text-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <img src="{{ asset('img/ins-UNJFSC.png') }}" alt="UNJFSC" class="h-16 mx-auto mb-8 bg-white rounded-full p-2">
            <p class="text-lg font-bold mb-2 tracking-tight uppercase">Universidad Nacional José Faustino Sánchez Carrión</p>
            <p class="text-slate-500 text-sm mb-10 tracking-widest uppercase">Sede Central - Huacho, Perú</p>
            <div class="flex justify-center gap-6 mb-12">
                <a href="#" class="text-slate-400 hover:text-white text-2xl transition-all"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-slate-400 hover:text-white text-2xl transition-all"><i class="bi bi-globe"></i></a>
            </div>
            <div class="border-t border-slate-800 pt-8">
                <p class="text-slate-600 text-[10px] font-bold tracking-[0.2em] uppercase">Sistema de Prácticas &copy; {{ date('Y') }} Todos los derechos reservados</p>
            </div>
        </div>
    </footer>

    <!-- AOS & Custom Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    @livewireScripts
    <script>
        AOS.init({ once: true, duration: 800 });
        
        // Theme Loader
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>
