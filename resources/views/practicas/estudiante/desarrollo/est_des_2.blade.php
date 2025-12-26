@php
    $practicaData = $practicas;
@endphp
<!-- Segunda Etapa -->
<div class="section-card">
    <h3 id="subtitle" class="section-title text-center mb-4">
        <i class="bi bi-2-circle me-2"></i>
        Segunda Etapa - Documentación
    </h3>
    <div class="row mb-4">
        <!-- Formulario de Trámite (FUT) -->
        <div class="col-md-6 mb-4">
            <div class="practice-stage-card text-center h-100">
                <div class="stage-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h5 class="text-primary font-weight-bold text-uppercase mb-3">Formulario de Trámite (FUT)</h5>
                <div id="futStatus">
                    <span id="status-file-fut" class="status-badge status-completed">Completo</span>
                    <button class="btn btn-primary-custom btn-sm btn-view-archivo"
                        data-type="fut"
                        data-bs-target="#archivoModal">Visualizar</button>
                </div>
            </div>
        </div>

        <!-- Carta de Presentación -->
        <div class="col-md-6 mb-4">
            <div class="practice-stage-card text-center h-100">
                <div class="stage-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                    <i class="bi bi-envelope"></i>
                </div>
                <h5 class="text-primary font-weight-bold text-uppercase mb-3">Carta de Presentación</h5>
                <div id="futStatus">
                    <span id="status-file-fut" class="status-badge status-completed">Completo</span>
                    <button class="btn btn-primary-custom btn-sm btn-view-archivo"
                        data-type="carta_presentacion"
                        data-bs-target="#archivoModal">Visualizar</button>
                </div>
            </div>
        </div>
        <!-- Carta de Aceptación -->
        <div class="col-md-12 mb-4" style="display: {{ $practicas->tipo_practica == 'convalidacion' ? 'block' : 'none' }}">
            <div class="practice-stage-card text-center h-100">
                <div class="stage-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                    <i class="bi bi-envelope-check"></i>
                </div>
                <h5 class="text-primary font-weight-bold text-uppercase mb-3">Carta de Aceptación</h5>
                <div id="cartaAceptacionStatus">
                    <span id="status-file-cart" class="status-badge status-completed">Completo</span>
                    <button class="btn btn-primary-custom btn-sm btn-view-archivo"
                        data-type="carta_aceptacion"
                        data-bs-target="#archivoModal">Visualizar</button>
                </div>
            </div>
        </div>
    </div>
</div>