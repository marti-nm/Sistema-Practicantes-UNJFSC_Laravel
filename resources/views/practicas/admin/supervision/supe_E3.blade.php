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
        --info-color: #0891b2;
        --purple-color: #7c3aed;
        --emerald-color: #10b981;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .supervision-e3-container {
        background: var(--background-color);
        padding: 1rem;
        border-radius: 1rem;
    }

    .supervision-e3-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .supervision-e3-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .supervision-e3-header {
        background: linear-gradient(135deg, var(--emerald-color), #047857);
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .supervision-e3-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .supervision-e3-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .supervision-e3-body {
        padding: 2rem;
    }

    .document-section {
        width: 100%;
        transition: all 0.5s ease;
        opacity: 1;
    }

    .document-section[style*="display: none"] {
        opacity: 0;
        transform: translateY(20px);
        display: none !important;
    }

    .document-section:not([style*="display: none"]) {
        opacity: 1;
        transform: translateY(0);
    }

    .document-card-e3 {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        min-height: 130px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .document-card-e3::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        transition: all 0.3s ease;
    }

    /* Desarrollo - Documentos iniciales */
    .document-card-e3.carta-aceptacion-e3::before {
        background: linear-gradient(90deg, var(--success-color), #047857);
    }

    .document-card-e3.plan-actividades::before {
        background: linear-gradient(90deg, var(--info-color), #0e7490);
    }

    /* Convalidación - Documentos de seguimiento */
    .document-card-e3.registro-actividades::before {
        background: linear-gradient(90deg, var(--warning-color), #b45309);
    }

    .document-card-e3.control-mensual::before {
        background: linear-gradient(90deg, var(--purple-color), #5b21b6);
    }

    .document-card-e3:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .document-card-e3-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        width: 100%;
    }

    .document-icon-e3 {
        font-size: 3rem;
        transition: all 0.3s ease;
    }

    .document-card-e3.carta-aceptacion-e3 .document-icon-e3 {
        color: var(--success-color);
    }

    .document-card-e3.plan-actividades .document-icon-e3 {
        color: var(--info-color);
    }

    .document-card-e3.registro-actividades .document-icon-e3 {
        color: var(--warning-color);
    }

    .document-card-e3.control-mensual .document-icon-e3 {
        color: var(--purple-color);
    }

    .document-card-e3:hover .document-icon-e3 {
        transform: scale(1.15) rotate(5deg);
    }

    .document-details-e3 {
        text-align: center;
        flex: 1;
    }

    .document-details-e3 h5 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        line-height: 1.2;
    }

    .btn-ver-pdf-e3 {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-ver-pdf-e3.carta-aceptacion-e3 {
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
    }

    .btn-ver-pdf-e3.carta-aceptacion-e3:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .btn-ver-pdf-e3.plan-actividades {
        background: linear-gradient(135deg, var(--info-color), #0e7490);
        color: white;
    }

    .btn-ver-pdf-e3.plan-actividades:hover {
        background: linear-gradient(135deg, #0e7490, #0c4a6e);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .btn-ver-pdf-e3.registro-actividades {
        background: linear-gradient(135deg, var(--warning-color), #b45309);
        color: white;
    }

    .btn-ver-pdf-e3.registro-actividades:hover {
        background: linear-gradient(135deg, #b45309, #92400e);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .btn-ver-pdf-e3.control-mensual {
        background: linear-gradient(135deg, var(--purple-color), #5b21b6);
        color: white;
    }

    .btn-ver-pdf-e3.control-mensual:hover {
        background: linear-gradient(135deg, #5b21b6, #4c1d95);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .supervision-e3-footer {
        background: #f0fdf4;
        border-top: 1px solid #bbf7d0;
        margin-top: auto;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-select {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: var(--surface-color);
    }

    .form-select:focus {
        border-color: var(--emerald-color);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        outline: none;
    }

    .btn-guardar-e3 {
        background: linear-gradient(135deg, var(--emerald-color), #047857);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-guardar-e3:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--border-color);
        margin-bottom: 1rem;
        display: block;
    }

    .empty-state h5 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        font-size: 0.95rem;
        color: var(--text-secondary);
        margin: 0;
    }

    @media (max-width: 768px) {
        .supervision-e3-body {
            padding: 1rem;
        }

        .document-card-e3-content {
            flex-direction: column;
            gap: 1rem;
        }

        .document-icon-e3 {
            font-size: 2.5rem;
        }

        .supervision-e3-footer {
            padding: 1rem;
        }

        .document-card-e3 {
            min-height: 110px;
        }

        .document-details-e3 h5 {
            font-size: 0.9rem;
        }
    }

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

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .slide-in {
        animation: slideIn 0.4s ease;
    }
</style>

<div class="supervision-e3-container fade-in supervision-content">
    <div class="supervision-e3-card">
        <div class="supervision-e3-header">
            <h6 class="supervision-e3-title">
                <i class="bi bi-file-earmark-check"></i>
                Tercera Etapa - Documentación de Informes
            </h6>
        </div>
        
        <div class="supervision-e3-body">

            <!-- Sección Desarrollo -->
            <div class="document-section" id="seccion-desarrollo-E3">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 mb-4">
                        <div class="document-card-e3 carta-aceptacion-e3 slide-in">
                            <div class="document-card-e3-content">
                                <i class="bi bi-envelope-check document-icon-e3"></i>
                                <div class="document-details-e3">
                                    <h5>Carta de Aceptación</h5>
                                    <a href="#" class="btn-ver-pdf carta-aceptacion btn-review-doc" id="btn-ruta-carta-aceptacion-E3" data-doctype="carta_aceptacion">
                                        <i class="bi bi-file-pdf"></i>
                                        Ver PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 mb-4">
                        <div class="document-card-e3 plan-actividades slide-in">
                            <div class="document-card-e3-content">
                                <i class="bi bi-calendar-check document-icon-e3"></i>
                                <div class="document-details-e3">
                                    <h5>Plan de Actividades de las PPP</h5>
                                    <a href="#" class="btn-ver-pdf plan-actividades btn-review-doc" id="btn-ruta-plan-actividades" data-doctype="plan_actividades_ppp">
                                        <i class="bi bi-file-pdf"></i>
                                        Ver PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección Convalidación -->
            <div class="document-section" id="seccion-convalidacion-E3" style="display: none;">
                <!-- aqui mejor Plan de actividades -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 mb-4">
                        <div class="document-card-e3 plan-actividades slide-in">
                            <div class="document-card-e3-content">
                                <i class="bi bi-calendar-check document-icon-e3"></i>
                                <div class="document-details-e3">
                                    <h5>Plan de Actividades de las PPP</h5>
                                    <a href="#" class="btn-ver-pdf plan-actividades btn-review-doc" id="btn-ruta-plan-actividades" data-doctype="plan_actividades_ppp">
                                        <i class="bi bi-file-pdf"></i>
                                        Ver PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6 mb-4">
                        <div class="document-card-e3 registro-actividades slide-in">
                            <div class="document-card-e3-content">
                                <i class="bi bi-journal-text document-icon-e3"></i>
                                <div class="document-details-e3">
                                    <h5>Registro de Actividades</h5>
                                    <a href="#" class="btn-ver-pdf-e3 registro-actividades btn-review-doc" id="btn-ruta-registro-actividades" data-doctype="registro_actividades">
                                        <i class="bi bi-file-pdf"></i>
                                        Ver PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 mb-4">
                        <div class="document-card-e3 control-mensual slide-in">
                            <div class="document-card-e3-content">
                                <i class="bi bi-graph-up document-icon-e3"></i>
                                <div class="document-details-e3">
                                    <h5>Control Mensual de Actividades</h5>
                                    <a href="#" class="btn-ver-pdf-e3 control-mensual btn-review-doc" id="btn-ruta-control-mensual-actividades" data-doctype="control_actividades">
                                        <i class="bi bi-file-pdf"></i>
                                        Ver PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="supervision-e3-footer">
            <!--<form id="formProcesoE3" class="form-etapa" action="{{ route('proceso') }}" method="POST" data-estado="3">
                @csrf
                <input type="hidden" name="id" id="idE3">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado de Aprobación</label>
                            <select class="form-select" id="estadoE3" name="estado" required>
                                <option value="" selected disabled>Seleccione un estado</option>
                                <option value="rechazado">Rechazado</option>
                                <option value="aprobado">Aprobado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <button type="submit" form="formProcesoE3" class="btn-guardar-e3 w-100">
                                <i class="bi bi-check-circle"></i>
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </form>-->
        </div>
    </div>
</div>
