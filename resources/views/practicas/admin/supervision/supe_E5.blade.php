<div class="supervision-e2-container fade-in">
    <div class="supervision-e2-card">
            <div class="supervision-e2-header">
                <h6 class="supervision-e2-title">
                    <i class="bi bi-files"></i>
                    Evaluación Final de Prácticas
                </h6>
                <div class="completion-badge">
                    <i class="bi bi-check-circle-fill"></i>
                    Etapa Final
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <span class="fa-stack fa-2x">
                            <i class="fas fa-circle fa-stack-2x text-success"></i>
                            <i class="fas fa-star fa-stack-1x fa-inverse"></i>
                        </span>
                    </div>
                    <h5 class="text-dark font-weight-bold">Conclusión del Proceso</h5>
                    <p class="text-muted">
                        Revise que el expediente esté completo. Si todo es conforme, proceda a ingresar la calificación final.
                        <br>Al guardar, no podrá editar la calificación, si desea hacerlo debe solicitarlo al administrador.
                    </p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        {{-- Sección: Formulario de Calificación (visible solo cuando state = 5, aun no calificado) --}}
                        <div id="seccion-calificar-form" class="d-none">
                            <form action="{{ route('practica.calificar') }}" method="POST" id="form-calificacion-final">
                                @csrf
                                <input type="hidden" name="practica_id" id="idE5">
                                
                                <div class="form-group">
                                    <label for="calificacion-input" class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Nota Final (0 - 20)
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-text bg-success text-white border-success">
                                            <i class="bi bi-clipboard-check"></i>
                                        </div>
                                        <input type="number" step="0.01" min="0" max="20" 
                                               class="form-control border-success text-center font-weight-bold text-success" 
                                               id="calificacion-input"
                                               name="calificacion" 
                                               placeholder="0.00" required>
                                    </div>
                                    <small class="form-text text-muted mt-2">
                                        <i class="fas fa-info-circle"></i> La nota se enviará al estudiante inmediatamente.
                                    </small>
                                </div>

                                <hr>

                                <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm" id="btn-submit-calificacion">
                                    <i class="fas fa-save mr-2"></i> Registrar Calificación Final
                                </button>
                            </form>
                        </div>

                        {{-- Sección: Nota ya registrada (visible cuando state = 6, ya calificado) --}}
                        <div id="seccion-ya-calificado" class="d-none">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-5">
                                    <div class="mb-4">
                                        <span class="fa-stack fa-3x">
                                            <i class="fas fa-circle fa-stack-2x text-success"></i>
                                            <i class="fas fa-check fa-stack-1x fa-inverse"></i>
                                        </span>
                                    </div>
                                    <h4 class="text-success font-weight-bold mb-3">¡Práctica Calificada!</h4>
                                    <div class="mb-4">
                                        <span class="text-muted">Nota Final Registrada:</span>
                                        <div class="display-3 font-weight-bold text-success mt-2" id="display-nota-final">--</div>
                                        <small class="text-muted">/ 20.00</small>
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <div class="alert alert-info border-0 shadow-sm">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        La calificación ya fue registrada y notificada al estudiante.
                                    </div>

                                    <!-- una alerta de Espera de la confrimacion del amind para editar mota -->
                                    <div class="alert alert-warning border-0 shadow-sm d-none" id="alert-solicitud-enviada">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        La solicitud fue enviada y se espera la confirmación del administrador.
                                    </div>

                                    <button type="button" class="btn btn-outline-warning btn-lg mt-3" id="btn-solicitar-edicion">
                                        <i class="fas fa-edit mr-2"></i> Solicitar Edición
                                    </button>
                                    @if(Auth::user()->hasAnyRoles([1, 2]))
                                    <button type="button" class="btn btn-outline-primary btn-lg mt-3" id="btn-solicitar-revision">
                                        <i class="fas fa-edit mr-2"></i> Revisar Solcitud
                                    </button>
                                    @endif
                                    <small class="d-block text-muted mt-2">
                                        <i class="fas fa-lock"></i> Requiere aprobación del administrador
                                    </small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Modal de Solicitud de Edición --}}
<div class="modal fade" id="modalSolicitarEdicion" tabindex="-1" aria-labelledby="modalSolicitarEdicionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formSolicitarEdicion" action="{{ route('solicitud_nota') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="id_solicitud_nota">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title" id="modalSolicitarEdicionLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Solicitar Edición de Calificación
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-4">
                        <span class="fa-stack fa-3x">
                            <i class="fas fa-circle fa-stack-2x text-warning"></i>
                            <i class="fas fa-user-shield fa-stack-1x text-white"></i>
                        </span>
                    </div>
                    <h5 class="font-weight-bold mb-3">¿Necesita modificar la calificación?</h5>
                    <p class="text-muted mb-4">
                        Para editar una calificación ya registrada, debe solicitar autorización al <strong>Administrador del Sistema</strong>.
                    </p>
                    <div class="alert alert-secondary border-0">
                        <small>
                            <i class="fas fa-info-circle mr-1"></i>
                            El administrador habilitará la edición cambiando el estado de la práctica. 
                            Una vez autorizado, podrá ingresar nuevamente la calificación.
                        </small>
                    </div>
                    <div class="bg-light rounded p-3 mt-3">
                        <p class="mb-1 text-muted small">Escriba el motivo de la solicitud:</p>
                        <textarea name="motivo" id="motivo" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Solicitar Edición
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSolicitarRevision" tabindex="-1" aria-labelledby="modalSolicitarRevisionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formManagement" action="{{ route('solicitud.nota') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_practica" id="id_practica">
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="bi bi-person"></i> Nombre del Estudiante:</label>
                        <p id="nombre_m_ap" class="font-weight-bold align-items-center text-capitalize"></p>
                    </div>
                    <!-- mostrar el estado de la solicitud de baja -->
                    <div class="form-group mt-3" id="estado-solicitud-ap">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Acción a realizar:</label>
                        <p id="accion_ap" class="font-weight-bold align-items-center">Habilitar Edición</p>
                    </div>
                    <!-- justificacion de la solicitud -->
                    <div class="form-group mt-3">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Justificación:</label>
                        <p id="motivo_sol" class="font-weight-bold align-items-center"></p>
                    </div>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Seleccionar una opción:</label>
                        <div class="row g-2">
                            <div class="col">
                                <div class="correccion-cell h-100" id="aprobar-management" onclick="selectGestion('aprobar')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;">
                                    <i class="bi bi-check-circle" style="font-size: 1.5em;"></i><br>Aprobar
                                    <input type="radio" name="estado" id="gestionAprobar" value="1" class="d-none" checked>
                                </div>
                            </div>
                            <div class="col">
                                <div class="correccion-cell h-100" id="rechazar-management" onclick="selectGestion('rechazar')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;">
                                    <i class="bi bi-x-circle" style="font-size: 1.5em;"></i><br>Rechazar
                                    <input type="radio" name="estado" id="gestionRechazar" value="2" class="d-none">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="justificacion" class="font-weight-bold">
                            <i class="bi bi-chat-dots"></i> Justificación
                        </label>
                        <textarea name="justificacion" id="justificacion-management" required class="form-control" rows="3" placeholder="Ej: Documentación incompleta."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" form="formManagement" class="btn btn-primary" id="btnManagement">Guardar</button>
            </div>
        </div>
    </div>
</div>