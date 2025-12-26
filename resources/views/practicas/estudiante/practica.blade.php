    <!-- Que ya no sea modal, que sea un plano un div normal -->
    <div class=" container mt-5" id="practicas" tabindex="-1" aria-labelledby="practicasLabel" aria-hidden="true">
        <div class="">
            <div class="">
                    <div class="p-4">
                        <!-- Verificación de requisitos -->
                        <div class="row mb-4" id="requirementsCheck">
                            <div class="col-12">
                                <div class="alert alert-success d-flex align-items-center" id="requirementsOk">
                                    <i class="bi bi-check-circle-fill me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h6 class="mb-1">¡Requisitos Completados!</h6>
                                        <p class="mb-0">Tu matrícula está completa. Puedes proceder a seleccionar el tipo de práctica.</p>
                                    </div>
                                </div>
                                
                                <div class="alert alert-danger d-none" id="requirementsError">
                                    <div class="text-center">
                                        <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #dc3545;"></i>
                                        <h4 class="mt-3 mb-3">¡Atención!</h4>
                                        <p class="mb-0" style="font-size: 1.1rem;">
                                            Primero debes completar tu matrícula para acceder a estas opciones.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Selección de tipo de práctica -->
                        <div class="row" id="practiceSelection">
                            <div class="col-12 mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-arrow-right-circle me-2"></i>
                                    Selecciona el tipo de práctica que deseas realizar:
                                </h6>
                            </div>
                            
                            <!-- Desarrollo -->
                            <div class="col-md-6 mb-4">
                                <div class="practice-option document-card p-4 border rounded-3 h-100" 
                                    style="background: linear-gradient(145deg, #f8fafc, #f1f5f9); cursor: pointer; transition: all 0.3s ease;"
                                    data-practice-type="desarrollo">
                                    <div class="text-center">
                                        <i class="bi bi-code-slash text-primary mb-3" style="font-size: 4rem;"></i>
                                        <h4 class="text-primary font-weight-bold mb-3">Desarrollo</h4>
                                        <p class="text-muted mb-4">
                                            Realiza tu práctica en el área de desarrollo de software, 
                                            trabajando en proyectos reales con tecnologías actuales.
                                        </p>
                                        <div class="d-grid">
                                            <button class="btn btn-outline-primary btn-lg" onclick="selectPracticeType('desarrollo')">
                                                <i class="bi bi-laptop me-2"></i>
                                                Seleccionar Desarrollo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Convalidación -->
                            <div class="col-md-6 mb-4">
                                <div class="practice-option document-card p-4 border rounded-3 h-100" 
                                    style="background: linear-gradient(145deg, #f8fafc, #f1f5f9); cursor: pointer; transition: all 0.3s ease;"
                                    data-practice-type="convalidacion">
                                    <div class="text-center">
                                        <i class="bi bi-file-earmark-check text-primary mb-3" style="font-size: 4rem;"></i>
                                        <h4 class="text-primary font-weight-bold mb-3">Convalidación</h4>
                                        <p class="text-muted mb-4">
                                            Convalida tu experiencia laboral previa como práctica 
                                            pre-profesional mediante documentación y evaluación.
                                        </p>
                                        <div class="d-grid">
                                            <button class="btn btn-outline-primary btn-lg" onclick="selectPracticeType('convalidacion')">
                                                <i class="bi bi-file-text me-2"></i>
                                                Seleccionar Convalidación
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Importante:</strong> Una vez seleccionado el tipo de práctica, no podrás cambiarlo. 
                                    Asegúrate de elegir la opción que mejor se adapte a tu situación académica y profesional.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
