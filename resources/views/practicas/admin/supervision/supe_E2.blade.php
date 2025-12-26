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
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .supervision-e2-container {
        background: var(--background-color);
        padding: 1rem;
        border-radius: 1rem;
    }

    .supervision-e2-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .supervision-e2-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .supervision-e2-header {
        background: linear-gradient(135deg, var(--purple-color), #5b21b6);
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .supervision-e2-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .supervision-e2-title {
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

    .supervision-e2-body {
        padding: 2rem;
    }

    .document-card {
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
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .document-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        transition: all 0.3s ease;
    }

    .document-card.fut::before {
        background: linear-gradient(90deg, var(--info-color), #0e7490);
    }

    .document-card.carta-presentacion::before {
        background: linear-gradient(90deg, var(--warning-color), #b45309);
    }

    .document-card.carta-aceptacion::before {
        background: linear-gradient(90deg, var(--success-color), #047857);
    }

    .document-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .document-card-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        width: 100%;
    }

    .document-icon {
        font-size: 3rem;
        transition: all 0.3s ease;
    }

    .document-card.fut .document-icon {
        color: var(--info-color);
    }

    .document-card.carta-presentacion .document-icon {
        color: var(--warning-color);
    }

    .document-card.carta-aceptacion .document-icon {
        color: var(--success-color);
    }

    .document-card:hover .document-icon {
        transform: scale(1.15) rotate(5deg);
    }

    .document-details {
        text-align: center;
        flex: 1;
    }

    .document-details h5 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-ver-pdf {
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .supervision-e2-footer {
        background: #f8fafc;
        border-top: 1px solid var(--border-color);
        padding: 1.5rem 2rem;
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
        border-color: var(--purple-color);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        outline: none;
    }

    .btn-guardar-e2 {
        background: linear-gradient(135deg, var(--purple-color), #5b21b6);
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

    .btn-guardar-e2:hover {
        background: linear-gradient(135deg, #5b21b6, #4c1d95);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: white;
    }

    /* Secciones condicionales */
    .document-section {
        transition: all 0.5s ease;
        opacity: 1;
    }

    .document-section[style*="display: none"] {
        opacity: 0;
        transform: translateY(20px);
    }

    .document-section:not([style*="display: none"]) {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .supervision-e2-body {
            padding: 1rem;
        }

        .document-card-content {
            flex-direction: column;
            gap: 1rem;
        }

        .document-icon {
            font-size: 2.5rem;
        }

        .supervision-e2-footer {
            padding: 1rem;
        }

        .document-card {
            min-height: 100px;
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

<div class="supervision-e2-container fade-in supervision-content">
    <div class="supervision-e2-card">
        <div class="supervision-e2-header">
            <h6 class="supervision-e2-title">
                <i class="bi bi-files"></i>
                Segunda Etapa - Documentación
            </h6>
        </div>
        
        <div class="supervision-e2-body">
            <div class="row">
                <!-- Formulario (FUT) -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="document-card fut">
                        <div class="document-card-content">
                            <i class="bi bi-file-earmark-text document-icon"></i>
                            <div class="document-details">
                                <h5>Informe Formulario (FUT)</h5>
                                <a href="#" class="btn-ver-pdf fut btn-review-doc" id="btnEtapa22" data-doctype="fut">
                                    Visualizar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carta de Presentación -->
                <div class="col-xl-6 col-lg-6 mb-4" id="seccion-desarrollo-E2">
                    <div class="document-card carta-presentacion slide-in">
                        <div class="document-card-content">
                            <i class="bi bi-envelope document-icon"></i>
                            <div class="document-details">
                                <h5>Carta de Presentación</h5>
                                <a href="#" class="btn-ver-pdf carta-presentacion btn-review-doc" id="btnEtapa32" data-doctype="carta_presentacion">
                                    Visualizar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carta de Aceptación -->
                <div class="col-xl-6 col-lg-6 mb-4 document-section" id="seccion-convalidacion-E2" style="display: none;">
                    <div class="document-card carta-aceptacion slide-in">
                        <div class="document-card-content">
                            <i class="bi bi-envelope-check document-icon"></i>
                            <div class="document-details">
                                <h5>Carta de Aceptación</h5>
                                <a href="#" class="btn-ver-pdf carta-aceptacion btn-review-doc" id="btnEtapa32" data-doctype="carta_aceptacion">
                                    Visualizar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="supervision-e2-footer">
            <!--<form id="formProcesoE2" class="form-etapa" action="{{ route('proceso') }}" method="POST" data-estado="2">
                @csrf
                <input type="hidden" name="id" id="idE2">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado de Aprobación</label>
                            <select class="form-select" id="estadoE2" name="estado" required>
                                <option value="" selected disabled>Seleccione un estado</option>
                                <option value="rechazado">Rechazado</option>
                                <option value="aprobado">Aprobado</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <button type="submit" form="formProcesoE2" class="btn-guardar-e2 w-100">
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
