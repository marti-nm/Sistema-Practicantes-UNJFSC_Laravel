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
        --indigo-color: #4f46e5;
        --rose-color: #e11d48;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .supervision-e4-container {
        background: var(--background-color);
        padding: 1rem;
        border-radius: 1rem;
    }

    .supervision-e4-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .supervision-e4-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .supervision-e4-header {
        background: linear-gradient(135deg, var(--indigo-color), #3730a3);
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .supervision-e4-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .supervision-e4-title {
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

    .supervision-e4-body {
        padding: 2rem;
    }

    .final-document-card {
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
        min-height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .final-document-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        transition: all 0.3s ease;
    }

    .final-document-card.constancia::before {
        background: linear-gradient(90deg, var(--rose-color), #be123c);
    }

    .final-document-card.informe-final::before {
        background: linear-gradient(90deg, var(--indigo-color), #3730a3);
    }

    .final-document-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .final-document-card:hover::before {
        height: 6px;
    }

    .final-document-card-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        width: 100%;
    }

    .final-document-icon {
        font-size: 3.5rem;
        transition: all 0.3s ease;
    }

    .final-document-card.constancia .final-document-icon {
        color: var(--rose-color);
    }

    .final-document-card.informe-final .final-document-icon {
        color: var(--indigo-color);
    }

    .final-document-card:hover .final-document-icon {
        transform: scale(1.2) rotate(10deg);
    }

    .final-document-details {
        text-align: center;
        flex: 1;
    }

    .final-document-details h5 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        line-height: 1.2;
    }

    .btn-ver-pdf-e4 {
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
        position: relative;
        overflow: hidden;
    }

    .btn-ver-pdf-e4::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-ver-pdf-e4:hover::before {
        left: 100%;
    }

    .supervision-e4-footer {
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        border-top: 1px solid #c7d2fe;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .supervision-e4-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--indigo-color), #3730a3);
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
        border-color: var(--indigo-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .btn-guardar-e4 {
        background: linear-gradient(135deg, var(--indigo-color), #3730a3);
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
        position: relative;
        overflow: hidden;
    }

    .btn-guardar-e4::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-guardar-e4:hover::before {
        left: 100%;
    }

    .btn-guardar-e4:hover {
        background: linear-gradient(135deg, #3730a3, #312e81);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
    }

    .completion-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        box-shadow: var(--shadow-sm);
        opacity: 0;
        animation: slideInRight 0.5s ease 0.5s forwards;
    }

    @media (max-width: 768px) {
        .supervision-e4-body {
            padding: 1rem;
        }

        .final-document-card-content {
            flex-direction: column;
            gap: 1rem;
        }

        .final-document-icon {
            font-size: 3rem;
        }

        .supervision-e4-footer {
            padding: 1rem;
        }

        .final-document-card {
            min-height: 120px;
        }

        .final-document-details h5 {
            font-size: 1rem;
        }

        .completion-badge {
            position: static;
            margin-top: 1rem;
            align-self: center;
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

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .pulse {
        animation: pulse 2s infinite;
    }
</style>

<div class="supervision-e4-container fade-in">
    <div class="supervision-e4-card">
        <div class="supervision-e4-header">
            <h6 class="supervision-e4-title">
                <i class="bi bi-file-earmark-check-fill"></i>
                Cuarta Etapa - Presentación de Informes
            </h6>
        </div>
        
        <div class="supervision-e4-body">
            <div class="row">
                <!-- Constancia de Cumplimiento -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="final-document-card constancia">
                        <div class="final-document-card-content">
                            <i class="bi bi-award final-document-icon"></i>
                            <div class="final-document-details">
                                <h5>Constancia de Cumplimiento</h5>
                                <a href="#" class="btn-ver-pdf constancia btn-review-doc" id="btn-ruta-constancia-cumplimiento" data-doctype="constancia_cumplimiento">
                                    <i class="bi bi-file-pdf"></i>
                                    Ver PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informe Final de PPP -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="final-document-card informe-final">
                        <div class="final-document-card-content">
                            <i class="bi bi-file-earmark-text final-document-icon"></i>
                            <div class="final-document-details">
                                <h5>Informe Final de PPP</h5>
                                <a href="#" class="btn-ver-pdf informe-final btn-review-doc" id="btn-ruta-informe-final" data-doctype="informe_final_ppp">
                                    <i class="bi bi-file-pdf"></i>
                                    Ver PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="supervision-e4-footer">
            <!--<form id="formProcesoE4" class="form-etapa" action="{{ route('proceso') }}" method="POST" data-estado="4">
                @csrf
                <input type="hidden" name="id" id="idE4">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="estado" class="form-label">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Estado Final de Aprobación
                            </label>
                            <select class="form-select" id="estadoE4" name="estado" required>
                                <option value="" selected disabled>Seleccione el estado final</option>
                                <option value="rechazado">Rechazado</option>
                                <option value="aprobado">Aprobado - Práctica Completada</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <button type="submit" form="formProcesoE4" class="btn-guardar-e4 w-100 pulse">
                                <i class="bi bi-check-circle-fill"></i>
                                Finalizar
                            </button>
                        </div>
                    </div>
                </div>
            </form>-->
        </div>
    </div>
</div>