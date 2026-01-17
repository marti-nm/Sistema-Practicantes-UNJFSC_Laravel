<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de gestión de practicantes">
    <meta name="author" content="DavidJA">
    <title>UNJFSC - @yield('title')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- AQUÍ ESTÁ LA REGLA CONFLICTIVA: Bootstrap incluye .bg-white { ... !important }. Si comentas esta línea, solucionarás el conflicto de colores, pero perderás los estilos de Bootstrap restantes. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Theme Checker (Prevent Flash of Unstyled Theme) -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <!-- TALL Stack Integration -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    @stack('css')
</head>
<style>
    [x-cloak] { display: none !important; }
    :root {
        --primary-color: #2563eb;
        --primary-light: #3b82f6;
        --secondary-color: #64748b;
        --background-color: #f8fafc;
        --surface-color: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .dark {
        --background-color: #020617; /* Slate 950 - Deepest Dark */
        --surface-color: #0f172a;    /* Slate 900 - Lighter for cards */
        --text-primary: #f8fafc;
        --text-secondary: #94a3b8;
        --border-color: rgba(255, 255, 255, 0.05);
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.5);
    }

    .navbar-scrolled {
        background-color: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    }
    .dark .navbar-scrolled {
        background-color: rgba(2, 6, 23, 0.8) !important; /* #020617 Slate 950 */
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 16px;
        line-height: 1.6;
        font-weight: 400;
        background-color: var(--background-color);
        color: var(--text-primary);
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    body.preload {
        transition: none !important;
    }



    .sidebar::-webkit-scrollbar {
        width: 6px; /* Un poco más ancho para visibilidad */
    }

    .sidebar::-webkit-scrollbar-track {
        background: transparent;
        margin-block: 0.5rem;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.4); /* Más visible por defecto */
        border-radius: 100vh;
        transition: background 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.1); /* Sutil borde para contraste */
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.6); /* Muy claro al pasar el mouse */
    }

    /* Header del Sidebar */
    .sidebar-header {
        padding: 2rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    .sidebar-logo {
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        text-decoration: none;
        letter-spacing: -0.025em;
    }

    .sidebar-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 400;
    }

    /* Navegación del Sidebar */
    .sidebar-nav {
        padding: 1.5rem 0;
    }

    .nav-section {
        margin-bottom: 2rem;
    }

    .nav-section-title {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0 1.5rem 0.75rem;
        margin-bottom: 0.5rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.875rem 1.5rem;
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        margin: 0 0.75rem;
        border-radius: 0.5rem;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(4px);
    }

    .nav-link.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .nav-link span,
    .nav-sublink span {
        transition: all 0.2s ease-in-out;
    }

    .nav-link:hover span,
    .nav-sublink:hover span {
        font-size: 1.5rem; /* Puedes ajustar a 1.15rem si deseas más grande */
        color: #f1f1f1;     /* O el color que prefieras para resaltar */
    }

    .nav-link i {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.875rem;
        font-size: 1.125rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease-in-out;
    }

    .nav-sublink i {
        transition: transform 0.2s ease-in-out;
    }

    .nav-link:hover i,
    .nav-sublink:hover i {
        transform: scale(1.2);
    }

    /* Dropdown Navigation */
    .nav-dropdown {
        margin: 0 0.2rem;
    }

    .nav-dropdown-toggle {
        position: relative;
        justify-content: space-between;
    }

    .nav-arrow {
        margin-left: auto;
        margin-right: 0;
        transition: transform 0.2s ease;
        font-size: 0.875rem !important;
        width: auto !important;
    }

    .nav-dropdown-toggle[aria-expanded="true"] .nav-arrow {
        transform: rotate(180deg);
    }

    .nav-submenu {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 0.5rem;
        margin: 0.5rem 0;
        overflow: hidden;
    }

    .nav-sublink {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 400;
        transition: all 0.2s ease;
        border: none;
        margin: 0;
        border-radius: 0;
    }

    .nav-sublink:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(8px);
    }

    .nav-sublink.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
    }

    .nav-sublink i {
        width: 1rem;
        height: 1rem;
        margin-right: 0.75rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-subheader {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.6875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 1.5rem 0.25rem;
        margin-top: 0.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .nav-subheader:first-child {
        border-top: none;
        margin-top: 0;
    }


    /* Header Superior */
    .topbar {
        background: var(--surface-color);
        padding: 1rem 2rem;
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
        z-index: 1029;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 80px;
    }

    .topbar-left h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.025em;
    }

    .topbar-subtitle {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    /* Área de Contenido */
    .main-content {
        flex: 1;
        padding: 2rem;
        max-width: 100%;
        transition: background-color 0.3s ease, color 0.3s ease;
        position: relative;
        background-color: transparent;
    }

    #wrapper {
        background: radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.03) 0%, transparent 50%),
                    radial-gradient(circle at 100% 100%, rgba(59, 130, 246, 0.03) 0%, transparent 50%),
                    var(--background-color);
        transition: background 0.3s ease;
    }

    .dark #wrapper {
        background: radial-gradient(circle at 10% 10%, rgba(37, 99, 235, 0.08) 0%, transparent 40%),
                    radial-gradient(circle at 90% 90%, rgba(59, 130, 246, 0.05) 0%, transparent 40%),
                    var(--background-color);
    }

    /* Tarjetas Modernas */
    .card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .card:hover {
        box-shadow: var(--shadow-md);
    }

    .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Botones Mejorados */
    .btn {
        font-family: inherit;
        font-weight: 500;
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--secondary-color);
        color: white;
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-warning {
        background: var(--warning-color);
        color: white;
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    /* Formularios Accesibles */
    .form-control {
        font-family: inherit;
        font-size: 1rem;
        padding: 0.875rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: var(--surface-color);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        outline: none;
    }

    .form-label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    /* Estados y Badges */
    .badge {
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Usuario Dropdown */
    .user-dropdown {
        position: relative;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border: 1px solid var(--border-color);
    }

    .user-info:hover {
        background: var(--background-color);
    }

    .user-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
    }

    .user-details {
        text-align: right;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
        margin: 0;
    }

    .user-role {
        color: var(--text-secondary);
        font-size: 0.75rem;
        margin: 0;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .main-content {
            padding: 1rem;
        }
    }

    /* Animaciones suaves */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease;
    }

    /* Focus visible para accesibilidad */
    .btn:focus-visible,
    .nav-link:focus-visible {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }
</style>

<style>
    /* DataTables Custom Styling - mimicking Tailwind Premium Design without @apply */
    .dataTables_wrapper .dataTables_length {
        color: #64748b; /* text-slate-500 */
        font-size: 10px;
        font-weight: 900; /* font-black */
        text-transform: uppercase;
        letter-spacing: 0.1em; /* tracking-widest */
        padding-bottom: 1rem;
    }
    .dark .dataTables_wrapper .dataTables_length {
        color: #94a3b8; /* dark:text-slate-400 */
    }
    
    .dataTables_wrapper .dataTables_length select {
        background-color: #fff;
        border: 1px solid #e2e8f0; /* border-slate-200 */
        border-radius: 0.75rem; /* rounded-xl */
        padding: 0.4rem 0.75rem;
        outline: none;
        transition: all 0.2s;
        margin: 0 0.5rem;
        cursor: pointer;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
    }
    .dark .dataTables_wrapper .dataTables_length select {
        background-color: #1e293b;
        border-color: rgba(255,255,255,0.1);
        color: #e2e8f0;
    }
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .dataTables_wrapper .dataTables_filter {
        position: relative;
        padding-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.5rem 1rem;
        outline: none;
        width: 100%;
        max-width: 16rem; /* slightly smaller */
        font-weight: 600;
        font-size: 13px;
        color: #334155;
        transition: all 0.2s;
    }
    .dark .dataTables_wrapper .dataTables_filter input {
        background-color: #1e293b;
        border-color: rgba(255,255,255,0.1);
        color: #e2e8f0;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .dataTables_wrapper .dataTables_info {
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding-top: 2rem !important;
    }
    .dark .dataTables_wrapper .dataTables_info {
        color: #64748b;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
    }
    @media (min-width: 768px) {
        .dataTables_wrapper .dataTables_paginate {
            justify-content: flex-end;
        }
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background-color: #f1f5f9 !important; /* bg-slate-100 */
        color: #475569 !important; /* text-slate-600 */
        font-weight: 900 !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        border-radius: 0.75rem !important;
        border: none !important;
        padding: 0.5rem 1rem !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
    }
    .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
        background-color: #1e293b !important;
        color: #94a3b8 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #2563eb !important; /* blue-600 */
        color: white !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #2563eb !important;
        color: white !important;
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5;
        cursor: not-allowed !important;
        background-color: #f8fafc !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        background-color: #f8fafc !important;
    }

    /* FIX: Force DataTables rows to be transparent so Tailwind bg classes work */
    .dark table.dataTable tbody {
        background-color: rgb(15 23 42 / 0.5) !important;
    }
    
    /* Prevent white flash on load in dark mode - hide table until ready */
    .dark table.dataTable tbody tr {
        background-color: transparent !important;
    }
    
    /* Skeleton Loader - Simple gray silhouettes */
    .skeleton-loader {
        display: block;
    }
    
    .skeleton-loader.hidden {
        display: none;
    }
    
    .skeleton-box {
        background-color: #e5e7eb; /* gray-200 */
        border-radius: 0.5rem;
    }
    
    .dark .skeleton-box {
        background-color: #374151; /* gray-700 */
    }
    
    /* Generic Table Hiding - Use .table-skeleton-ready on any table you want to hide until DataTables is ready */
    .table-skeleton-ready {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    
    .table-skeleton-ready.dt-ready {
        opacity: 1;
    }
    
    table.dataTable.no-footer {
        border-bottom: 1px solid #e2e8f0; 
    }
    .dark table.dataTable.no-footer {
        border-bottom-color: rgba(255,255,255,0.05);
    }
    
    /* Force dark background for table wrapper to prevent flash */
    .dark .dataTables_wrapper {
        background-color: transparent !important;
    }
</style>

<body 
    x-data="{ 
        darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }" 
    x-init="
        $watch('darkMode', val => {
            if (val) {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            }
        });
        if (darkMode) document.documentElement.classList.add('dark');
    "
>

    @include('layouts.navbar')
    
    <!-- Semestre Finalizado Modal (Alpine.js) -->
    @if(session('show_semestre_finalizado_modal'))
    <div x-data="{ showModal: true }" 
         x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[1060] flex items-center justify-center px-4 overflow-hidden">
        <!-- Backdrop -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        
        <!-- Modal Content -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100">
            
            <!-- Header -->
            <div class="bg-amber-400 px-6 py-4 flex items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill text-slate-900 text-xl"></i>
                <h3 class="text-slate-900 font-black text-sm uppercase tracking-wider m-0">Semestre Finalizado</h3>
            </div>

            <div class="p-8 text-center">
                <!-- Icon -->
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500 border border-rose-100 shadow-inner">
                    <i class="bi bi-calendar-x-fill text-4xl"></i>
                </div>

                <h2 class="text-xl font-black text-slate-800 mb-4 leading-tight">
                    El semestre académico ha sido finalizado por el administrador.
                </h2>

                <div class="bg-cyan-50 border border-cyan-100 rounded-2xl p-5 mb-8 flex gap-4 text-left">
                    <div class="w-8 h-8 rounded-full bg-cyan-100 flex items-center justify-center text-cyan-600 shrink-0 mt-0.5">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <p class="text-xs font-medium text-cyan-800 m-0 leading-relaxed">
                        A partir de este momento, el sistema se encuentra en <strong class="font-black">modo solo lectura</strong>. 
                        Podrás consultar información, pero no podrás realizar cambios ni registrar nuevos datos.
                    </p>
                </div>

                <button @click="showModal = false" 
                        class="w-full bg-[#1e3a8a] text-white font-black py-4 rounded-2xl hover:bg-blue-800 transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center gap-3 active:scale-95">
                    <i class="bi bi-check2-circle text-xl"></i>
                    Entendido
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="main-content fade-in">
        <!-- Persistent Warning for Bloqueado (Mode Only Read) -->
        @if(session('warning_semestre'))
        <div class="max-w-7xl mx-auto mb-6 px-4">
            <div class="bg-white dark:bg-slate-900/50 border-l-4 border-amber-400 p-4 rounded-xl shadow-sm dark:shadow-none flex items-center justify-between group hover:shadow-md transition-all border dark:border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-full flex items-center justify-center text-amber-500 animate-pulse">
                        <i class="bi bi-exclamation-circle-fill text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800 dark:text-white m-0 leading-none">Aviso del Sistema</p>
                        <p class="text-[11px] text-slate-400 mt-1 font-bold m-0">{{ session('warning_semestre') }}</p>
                    </div>
                </div>
                <!-- Mini Pill Toggle Style -->
                <div class="px-3 py-1 bg-amber-100 rounded-full">
                    <span class="text-[9px] font-black text-amber-700 uppercase tracking-widest">Solo Lectura</span>
                </div>
            </div>
        </div>
        @endif

        @yield('content')
    </main>
    @include('layouts.footer')
    @stack('js')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        </script>
    @endif
    @if(session('warning_semestre'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: '{{ session('warning_semestre') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        </script>
    @endif
    <script>
        document.body.classList.add('preload');
        setTimeout(() => {
            document.body.classList.remove('preload');
        }, 300);
    </script>
</body>
</html>