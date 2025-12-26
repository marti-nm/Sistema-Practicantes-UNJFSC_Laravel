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
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .supervision-container {
        background: var(--background-color);
        padding: 1rem;
        border-radius: 1rem;
    }

    .supervision-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .supervision-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .supervision-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .supervision-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .supervision-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-align: center;
    }

    .supervision-body {
        padding: 2rem;
    }

    .info-card {
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
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        transition: all 0.3s ease;
    }

    .info-card.empresa::before {
        background: linear-gradient(90deg, var(--success-color), #047857);
    }

    .info-card.jefe::before {
        background: linear-gradient(90deg, var(--warning-color), #b45309);
    }

    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    .info-card-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
    }

    .info-icon {
        font-size: 3rem;
        transition: all 0.3s ease;
    }

    .info-card.empresa .info-icon {
        color: var(--success-color);
    }

    .info-card.jefe .info-icon {
        color: var(--warning-color);
    }

    .info-card:hover .info-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .info-details {
        text-align: center;
    }

    .info-details h5 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-visualizar {
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: inline-block;
    }

    .btn-visualizar.empresa {
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
    }

    .btn-visualizar.empresa:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .btn-visualizar.jefe {
        background: linear-gradient(135deg, var(--warning-color), #b45309);
        color: white;
    }

    .btn-visualizar.jefe:hover {
        background: linear-gradient(135deg, #b45309, #92400e);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
        text-decoration: none;
    }

    .supervision-footer {
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
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        outline: none;
    }

    .btn-guardar {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-guardar:hover {
        background: linear-gradient(135deg, var(--primary-light), #2563eb);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: white;
    }

    @media (max-width: 768px) {
        .supervision-body {
            padding: 1rem;
        }

        .info-card-content {
            flex-direction: column;
            gap: 1rem;
        }

        .info-icon {
            font-size: 2.5rem;
        }

        .supervision-footer {
            padding: 1rem;
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
</style>

<div id="supervision-container" class="supervision-container fade-in">
    <div class="supervision-card">
        <div class="supervision-header">
            <h6 class="supervision-title">Primera Etapa - Información General</h6>
        </div>
        
        <div class="supervision-body">
            <div class="row">
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="info-card empresa">
                        <div class="info-card-content">
                            <i class="bi bi-building info-icon"></i>
                            <div class="info-details">
                                <h5>Empresa</h5>
                                <a href="#" class="btn-visualizar empresa" id="btnEtapaEmpresa">
                                    Visualizar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="info-card jefe">
                        <div class="info-card-content">
                            <i class="bi bi-person-badge info-icon"></i>
                            <div class="info-details">
                                <h5>Jefe Inmediato</h5>
                                <a href="#" class="btn-visualizar jefe" id="btnEtapaJefe">
                                    Visualizar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="supervision-footer">
        </div>
    </div>
</div>

<style>
    .etapa-container {
        background: var(--background-color);
        padding: 1rem;
        border-radius: 1rem;
    }

    .etapa-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .etapa-header {
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .etapa-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .etapa-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .etapa-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-control.readonly {
        background: #f8fafc;
        border: 2px solid var(--border-color);
        color: var(--text-primary);
        font-weight: 500;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-family: 'Inter', sans-serif;
    }

    .data-field {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.875rem 1rem;
        color: var(--text-primary);
        font-weight: 500;
        min-height: 44px;
        display: flex;
        align-items: center;
        position: relative;
        transition: all 0.3s ease;
    }

    .data-field:hover {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .data-field::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--success-color), #047857);
        border-radius: 0.5rem 0 0 0.5rem;
    }

    .etapa-footer {
        background: #f8fafc;
        border-top: 1px solid var(--border-color);
        padding: 1.5rem 2rem;
    }

    .btn-regresar {
        background: linear-gradient(135deg, var(--secondary-color), #475569);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-regresar:hover {
        background: linear-gradient(135deg, #475569, #334155);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
    }
</style>

<div id="empresa-container" class="fade-in" style="display: none;">
    <div class="etapa-card">
        <div class="etapa-header d-flex justify-content-between">
            <h5 class="etapa-title">
                <i class="bi bi-building"></i>
                Datos de la Empresa
            </h5>
            <button type="button" class="btn-regresar btn-regresar-etapa1">
                <i class="bi bi-arrow-left me-2"></i>
            </button>
        </div>
        
        <div class="etapa-body">
            <div class="form-group">
                <label class="form-label">Nombre de la Empresa</label>
                <div class="data-field">
                    <span id="modal-nombre-empresa"></span>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ruc" class="form-label">RUC</label>
                        <div class="data-field">
                            <span id="modal-ruc-empresa"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="razon_social" class="form-label">Razón Social</label>
                        <div class="data-field">
                            <span id="modal-razon_social-empresa"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="direccion" class="form-label">Dirección</label>
                <div class="data-field">
                    <span id="modal-direccion-empresa"></span>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <div class="data-field">
                            <span id="modal-telefono-empresa"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="data-field">
                            <span id="modal-email-empresa"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="sitio_web" class="form-label">Sitio web (opcional)</label>
                <div class="data-field">
                    <span id="modal-sitio_web-empresa"></span>
                </div>
            </div>

            <div id="correction-data-empresa" class="col-md-12" style="display: none;">
                <div class="alert alert-warning">
                    Enviado a Corregir
                </div>
            </div>
            <form id="formProcesoEmpresa" class="form-etapa" action="{{ route('empresa.actualizar.estado') }}" method="POST">
                @csrf
                <div class="col-md-12">
                    <hr>
                </div>
                <input type="hidden" name="id" id="idEmpresa">
                <div class="form-group">
                    <label for="comentarioEmpresa" class="form-weight-bold mt-2">Comentario</label>
                    <textarea class="form-control" id="comentarioEmpresa" name="comentario" rows="2"></textarea>
                </div>
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <div class="col-md-4 form-group mt-3">
                        <label for="estadoEmpresa" class="font-weight-bold mt-2">
                            <i class="bi bi-gear"></i> Estado del Documento
                        </label>
                        <select class="form-select" id="estadoEmpresa" name="estado">
                            <option value="" selected disabled>Seleccione un estado</option>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Corregir">Corregir</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="font-weight-bold mt-2">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </label>
                            <button type="submit" form="formProcesoEmpresa" class="btn-guardar-e2 w-100">
                                <i class="bi bi-check-circle"></i>
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="etapa-footer">
            <div class="text-end">
                <button type="button" class="btn-regresar btn-regresar-etapa1">
                    <i class="bi bi-arrow-left me-2"></i>
                    Regresar
                </button>
            </div>
        </div>
    </div>
</div>


<style>
    .jefe-container {
        background: var(--background-color);
        padding: 1rem;
        border-radius: 1rem;
    }

    .jefe-card {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .jefe-header {
        background: linear-gradient(135deg, var(--warning-color), #b45309);
        color: white;
        padding: 1.5rem 2rem;
        position: relative;
    }

    .jefe-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    }

    .jefe-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .jefe-body {
        padding: 2rem;
    }

    .data-field-jefe {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.875rem 1rem;
        color: var(--text-primary);
        font-weight: 500;
        min-height: 44px;
        display: flex;
        align-items: center;
        position: relative;
        transition: all 0.3s ease;
    }

    .data-field-jefe:hover {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .data-field-jefe::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--warning-color), #b45309);
        border-radius: 0.5rem 0 0 0.5rem;
    }

    .jefe-footer {
        background: #fffbeb;
        border-top: 1px solid #fbbf24;
        padding: 1.5rem 2rem;
    }

    .btn-regresar-jefe {
        background: linear-gradient(135deg, var(--secondary-color), #475569);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-regresar-jefe:hover {
        background: linear-gradient(135deg, #475569, #334155);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        color: white;
    }
</style>

<div id="jefe-container" class="jefe-container fade-in" style="display: none;">
    <div class="jefe-card">
        <div class="jefe-header">
            <h5 class="jefe-title">
                <i class="bi bi-person-tie"></i>
                Datos del Jefe Inmediato
            </h5>
        </div>
        
        <div class="jefe-body">
            <div class="form-group">
                <label for="name" class="form-label">Apellidos y Nombres</label>
                <div class="data-field-jefe">
                    <span id="modal-name-jefe"></span>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="dni" class="form-label">DNI</label>
                        <div class="data-field-jefe">
                            <span id="modal-dni-jefe"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sitio_web" class="form-label">Sitio web (opcional)</label>
                        <div class="data-field-jefe">
                            <span id="modal-sitio_web-jefe"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="area" class="form-label">Área o Departamento</label>
                        <div class="data-field-jefe">
                            <span id="modal-area-jefe"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cargo" class="form-label">Cargo o Puesto</label>
                        <div class="data-field-jefe">
                            <span id="modal-cargo-jefe"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <div class="data-field-jefe">
                            <span id="modal-telefono-jefe"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="data-field-jefe">
                            <span id="modal-email-jefe"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="correction-data-jefe" class="col-md-12" style="display: none;">
                <div class="alert alert-secondary">
                    Enviado a Corregir
                </div>
            </div>

            <form id="formProcesoJefe" class="form-etapa" action="{{ route('jefe_inmediato.actualizar.estado') }}" method="POST">
                @csrf
                <div class="col-md-12">
                    <hr>
                </div>
                <input type="hidden" name="id" id="idJefe">
                <div class="form-group">
                    <label for="comentarioJefe" class="form-weight-bold mt-2">Comentario</label>
                    <textarea class="form-control" id="comentarioJefe" name="comentario" rows="2"></textarea>
                </div>
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <div class="col-md-4 form-group mt-3">
                        <label for="estadoJefe" class="font-weight-bold mt-2">
                            <i class="bi bi-gear"></i> Estado del Documento
                        </label>
                        <select class="form-select" id="estadoJefe" name="estado" required>
                            <option value="" selected disabled>Seleccione un estado</option>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Corregir">Corregir</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="font-weight-bold mt-2">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </label>
                            <button type="submit" form="formProcesoJefe" class="btn-guardar-e2 w-100">
                                <i class="bi bi-check-circle"></i>
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="jefe-footer">
            <div class="text-end">
                <button type="button" class="btn-regresar-jefe btn-regresar-etapa1">
                    <i class="bi bi-arrow-left me-2"></i>
                    Regresar
                </button>
            </div>
        </div>
    </div>
</div>