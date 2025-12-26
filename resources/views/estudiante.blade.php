<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Prácticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        :root {
            --primary-blue: #2563eb;
            --light-blue: #dbeafe;
            --soft-gray: #f8fafc;
            --border-gray: #e2e8f0;
            --text-gray: #64748b;
            --dark-gray: #334155;
        }

        body {
            background-color: var(--soft-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-gray);
        }

        .navbar-custom {
            background-color: #f1f5f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            border-bottom: 1px solid var(--border-gray);
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--primary-blue) !important;
            font-size: 1.25rem;
        }

        .main-content {
            padding: 2rem 0;
        }

        .welcome-header {
            background: linear-gradient(135deg, var(--primary-blue), #1d4ed8);
            color: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .section-card {
            background: linear-gradient(145deg, #f8fafc, #f1f5f9);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid var(--border-gray);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .section-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
        }

        .section-title {
            color: var(--primary-blue);
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-custom {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-outline-custom {
            color: var(--primary-blue);
            border-color: var(--primary-blue);
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-outline-custom:hover {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            transform: translateY(-1px);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--light-blue);
        }

        .info-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-gray);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-gray);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: var(--dark-gray);
            font-weight: 500;
            margin-top: 0.25rem;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background-color: var(--light-blue);
            color: var(--primary-blue);
        }

        .document-item {
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: between;
        }

        .upload-area {
            border: 2px dashed var(--border-gray);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--primary-blue);
            background-color: var(--light-blue);
        }

        .dropdown-toggle::after {
            display: none;
        }
    </style>

    <style>
    :root {
        --primary-color: #1e3a8a;
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

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        font-size: 16px;
        line-height: 1.6;
        color: var(--text-primary);
        background-color: var(--background-color);
        font-weight: 400;
    }

    /* Layout Principal */
    #wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar Moderno */
    .sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        width: 280px;
        height: 100vh;
        background: linear-gradient(180deg, var(--primary-color) 0%, #1e40af 100%);
        overflow-y: auto;
        z-index: 1030;
        box-shadow: var(--shadow-lg);
        transition: all 0.3s ease;
    }

    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
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

    /* Contenido Principal */
    #content-wrapper {
        margin-left: 280px;
        width: calc(100% - 280px);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: var(--background-color);
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
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
        min-height: calc(100vh - 80px);
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

    /* Tablas Modernas */
    .table {
        background: var(--surface-color);
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .table th {
        background: #f8fafc;
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        color: var(--text-primary);
        padding: 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
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

    /* Responsive Design */
    @media (max-width: 1024px) {
        .sidebar {
            width: 260px;
        }
        
        #content-wrapper {
            margin-left: 260px;
            width: calc(100% - 260px);
        }
        
        .main-content {
            padding: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            left: -280px;
            transition: left 0.3s ease;
            z-index: 1030;
        }
        
        .sidebar.show {
            left: 0;
            z-index: 1030;
        }

        #content-wrapper {
            margin-left: 0;
            width: 100%;
        }
        
        /* TOPBAR MÓVIL - Una sola fila compacta */
        .topbar {
            padding: 0.5rem 0.75rem;
            min-height: 56px;
            flex-wrap: nowrap;
            gap: 0.5rem;
        }
        
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            min-width: 0;
        }
        
        .topbar-left h1 {
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
            line-height: 1.2;
        }
        
        .topbar-subtitle {
            display: none;
        }
        
        .topbar-right {
            gap: 0.5rem;
            flex-shrink: 0;
        }
        
        /* Simplificar dropdown de usuario en móvil */
        .user-info {
            padding: 0.25rem 0.5rem;
            gap: 0.5rem;
        }
        
        .user-avatar {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }
        
        .user-avatar img {
            width: 32px !important;
            height: 32px !important;
        }
        
        .user-details {
            display: none;
        }
        
        .user-info > .bi-chevron-down {
            display: none;
        }
        
        /* Botón hamburguesa más visible */
        #sidebarToggle {
            padding: 0.375rem 0.5rem;
            font-size: 1.5rem;
            color: var(--text-primary);
            background: transparent;
            border: none;
        }
        
        /* Notificaciones más compactas */
        .topbar-right .btn-link {
            padding: 0.25rem;
        }
        
        .main-content {
            padding: 1rem;
        }
    }
    
    /* Para pantallas muy pequeñas */
    @media (max-width: 400px) {
        .topbar-left h1 {
            font-size: 0.875rem;
            max-width: 120px;
        }
        
        .topbar {
            padding: 0.5rem;
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
    @stack('css')
</head>
<body>
    @php
        $user = auth()->user();
        $persona = $user->persona;
        $nombreCompleto = $persona->nombres . ' ' . $persona->apellidos;
    @endphp
    @include('layouts.navbar')
    
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>