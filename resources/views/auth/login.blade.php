<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Sistema de Prácticas UNJFSC</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
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

    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply font-outfit text-slate-900 bg-slate-950 h-full overflow-hidden flex items-center justify-center;
            }
        }

        .slideshow-img {
            @apply absolute inset-0 w-full h-full bg-cover bg-center opacity-0 scale-110 transition-all duration-[2000ms];
            filter: brightness(0.5) blur(2px);
            animation: slideAnimation 12s infinite;
        }

        @keyframes slideAnimation {
            0% { opacity: 0; transform: scale(1.1); }
            15% { opacity: 1; }
            45% { opacity: 1; }
            60% { opacity: 0; transform: scale(1); }
            100% { opacity: 0; }
        }

        .login-card {
            @apply relative z-10 w-full max-w-md p-8 sm:p-12 rounded-[2.5rem] bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border border-white/20 dark:border-slate-800/50 shadow-2xl;
        }
    </style>
</head>
<body x-data="{ showPass: false }">

    <!-- Background Slideshow -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="slideshow-img" style="background-image: url('{{ asset('img/login-background.jpg') }}'); animation-delay: 0s;"></div>
        <div class="slideshow-img" style="background-image: url('{{ asset('img/bg-UNJFSC-2.jpg') }}'); animation-delay: 6s;"></div>
    </div>

    <!-- Login Container -->
    <div class="login-card mx-4" x-data="{ loading: false }">
        <div class="text-center mb-10">
            <img src="{{ asset('img/ins-UNJFSC.png') }}" alt="UNJFSC" class="h-16 mx-auto mb-6 drop-shadow-lg">
            <h1 class="text-3xl font-extrabold text-secondary dark:text-white tracking-tight mb-1">Sistema de Prácticas</h1>
            <p class="text-[10px] font-bold text-primary uppercase tracking-[0.2em]">U.N. José Faustino Sánchez Carrión</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 space-y-2">
                @foreach ($errors->all() as $item)
                    <div class="flex items-center gap-3 p-3 text-sm text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400 rounded-xl border border-red-100 dark:border-red-900/30">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>{{ $item }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" @submit="loading = true">
            @csrf
            <div class="space-y-5">
                <!-- User Input -->
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 ml-1">Correo Institucional</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="bi bi-person text-lg"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                            placeholder="ejemplo@unjfsc.edu.pe"
                            class="block w-full pl-11 pr-4 py-3.5 bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all dark:text-white placeholder:text-slate-400"
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 ml-1">Contraseña</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                            <i class="bi bi-lock text-lg"></i>
                        </div>
                        <input 
                            :type="showPass ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="••••••••"
                            class="block w-full pl-11 pr-12 py-3.5 bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all dark:text-white placeholder:text-slate-400"
                        >
                        <button 
                            type="button" 
                            @click="showPass = !showPass"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-primary transition-colors"
                        >
                            <i class="bi" :class="showPass ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="relative w-full py-4 bg-secondary dark:bg-primary text-white font-bold rounded-2xl hover:bg-primary dark:hover:bg-primary-dark transition-all shadow-xl shadow-primary/20 transform hover:-translate-y-0.5 mt-2 flex items-center justify-center gap-2 group overflow-hidden"
                    :disabled="loading"
                >
                    <span x-show="!loading" class="flex items-center gap-2">
                        INGRESAR AL SISTEMA
                        <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        AUTENTICANDO...
                    </span>
                </button>
            </div>
        </form>

        <div class="mt-10 text-center">
            <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-primary transition-colors group">
                <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Volver a la página principal
            </a>
        </div>
    </div>

    <!-- Theme Loader -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @if(session('error'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Acceso denegado',
                text: "{{ session('error') }}",
                confirmButtonColor: '#2563eb',
                background: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a'
            });
        </script>
    @endif
</body>
</html>
