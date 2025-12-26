<div id="review-form-container" style="display: none;" class="fade-in">
    <div class="etapa-card">
        <div class="etapa-header bg-header" id="review-form-header">
            <h5 class="etapa-title">
                <i class="bi bi-building"></i>
                <span id="review-form-title">Formulario de Revisión</span>
            </h5>
        </div>
        
        <div class="etapa-body">
            <div id="no-file-container" style="display: none;">
                <div class="alert alert-info text-center">
                    <i class="bi bi-file-earmark-x" style="font-size: 2rem;"></i>
                    <h5 class="alert-heading mt-2">No se ha enviado ningún archivo</h5>
                    <p>No se ha encontrado ningún archivo para revisar.</p>
                </div>
            </div>
            <div id="approved-file-container" style="display: none;">
                <div class="alert alert-info text-center">
                    <i class="bi bi-clipboard-check" style="font-size: 2rem;"></i>
                    <h5 class="alert-heading mt-2">Aprobado el Archivo</h5>
                    <p>El docente ya revisó y ha aprobado este anexo. No es posible modificarlo.</p>
                </div>
                <div class="col-md-12">                        
                    <div class="d-flex flex-column">
                        <label class="font-weight-bold"><i class="bi bi-paperclip"></i> Archivo enviado:</label>
                        <div class="alert alert-light p-2 d-flex justify-content-between align-items-center border flex-grow-1">
                            <span class="text-truncate">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                <span id="approved_file_name">Documento.pdf</span>
                            </span>
                            <span id="approved_file_date">Fecha: --/--/----</span>
                            <span id="approved_file_status_badge" class="badge bg-secondary">Pendiente</span>
                            <a href="#" id="approved_file_link" class="btn btn-sm btn-outline-primary flex-shrink-0 ms-2" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i> Ver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <form id="genericReviewForm" class="form-etapa" action="{{ route('actualizar.archivo') }}" method="POST">
                @csrf
                <!-- Hidden inputs populated by JS -->
                <input type="hidden" name="id" id="review_file_id">
                
                <div class="row align-items-end">
                    <div class="col-md-12">                        
                        <div class="d-flex flex-column">
                            <label class="font-weight-bold"><i class="bi bi-paperclip"></i> Archivo enviado:</label>
                            <div class="alert alert-light p-2 d-flex justify-content-between align-items-center border flex-grow-1">
                                <span class="text-truncate">
                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                    <span id="review_file_name">Documento.pdf</span>
                                </span>
                                <span id="review_file_date">Fecha: --/--/----</span>
                                <span id="review_file_status_badge" class="badge bg-secondary">Pendiente</span>
                                <a href="#" id="review_file_link" class="btn btn-sm btn-outline-primary flex-shrink-0 ms-2" target="_blank">
                                    <i class="bi bi-box-arrow-up-right"></i> Ver
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12 d-flex justify-content-between align-items-center mt-3">
                        <div class="col-md-4 form-group">
                            <label for="estado" class="font-weight-bold mt-2">
                                <i class="bi bi-gear"></i> Estado del Documento
                            </label>
                            <select class="form-select" name="estado" id="review_file_status_select" required>
                                <option value="" selected disabled>Seleccione un estado</option>
                                <option value="Corregir">Corregir</option>
                                <option value="Aprobado">Aprobado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="font-weight-bold mt-2">
                                    <i class="bi bi-check-circle"></i> Guardar Cambios
                                </label>
                                <button type="submit" class="btn-guardar-e2 w-100">
                                    <i class="bi bi-check-circle"></i>
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Linea horizontal -->
                    <div class="col-md-12">
                        <hr>
                    </div>
                    
                    <!-- Historial (Placeholder for now, can be populated via JS if API provides it) -->
                    <div class="col-md-12" id="review_history_container" style="display: none;">
                        <label class="font-weight-bold mt-2">
                            <i class="bi bi-clock-history"></i> Historial de Archivos
                        </label>
                        <div id="review_history_list" class="mt-2">
                            <!-- History items will be injected here -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="etapa-footer">
            <div class="text-end">
                <button type="button" class="btn-regresar" id="btn-back-to-list">
                    <i class="bi bi-arrow-left me-2"></i>
                    Regresar
                </button>
            </div>
        </div>
    </div>
</div>
