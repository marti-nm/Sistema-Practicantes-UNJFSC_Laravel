<div class="modal" id="modalDisabledAp">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deshabilitar Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- mensaje que primero tendra ser aprobado por el administrador -->
                <div id="alertDeshabilitarAp" class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Advertencia!</strong>
                    <p>El estudiante debe ser aprobado por el administrador antes de deshabilitarlo o eliminado.</p>
                </div>
                <!-- mensaje que tendra el docente para habilitar a un estudiante -->
                <div id="alertHabilitarAp" class="alert alert-info">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Advertencia!</strong>
                    <p>Para habilitar a un estudiante debe ser aprobado por el administrador.</p>
                </div>
                <form id="formEliminar" action="{{ route('solicitud_ap') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_ap" id="id_ap">
                    <input type="hidden" name="id_sa" id="id_sa">
                    <!-- mostrar el nombre del estudiante -->
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="bi bi-person"></i> Nombre del Estudiante:</label>
                        <p id="nombre_ap" class="font-weight-bold align-items-center"></p>
                    </div>
                    <div class="form-group mt-3" id="correccion-options-ap">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Seleccionar una opción:</label>
                        <div id="option-deshabilitar-ap">
                            <div class="row g-2">
                                <div class="col">
                                    <div class="correccion-cell h-100" id="deshabilitar-ap" onclick="selectCorreccion('deshabilitar', 'ap')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;" data-toggle="tooltip" data-placement="top" title="Podra deshabilitar y luego habilitar.">
                                        <i class="bi bi-lock" style="font-size: 1.5em;"></i><br>Deshabilitar
                                        <input type="radio" name="opcion" id="correccionDeshabilitar-ap" value="1" class="d-none" checked>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="correccion-cell h-100" id="eliminar-ap" onclick="selectCorreccion('eliminar', 'ap')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;" data-toggle="tooltip" data-placement="top" title="Tendrá que enviar otra nota y aprueba el archivo.">
                                        <i class="bi bi-trash" style="font-size: 1.5em;"></i><br>Eliminar
                                        <input type="radio" name="opcion" id="correccionEliminar-ap" value="2" class="d-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="option-habilitar-ap" class="row g-2">
                            <div class="col">
                                <div class="correccion-cell h-100" id="habilitar-ap" onclick="selectCorreccion('habilitar', 'ap')" style="cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; text-align: center;" data-toggle="tooltip" data-placement="top" title="Podra deshabilitar y luego habilitar.">
                                    <i class="bi bi-unlock" style="font-size: 1.5em;"></i><br>Habilitar
                                    <input type="radio" name="opcion" id="correccionHabilitar-ap" value="3" class="d-none" checked>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="comentario-ap" class="font-weight-bold">
                            <i class="bi bi-chat-dots"></i> Comentario
                        </label>
                        <textarea name="comentario" id="comentario-ap" class="form-control" required rows="3" placeholder="Ej: La firma no es visible, por favor, vuelva a escanear el documento."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" form="formEliminar" class="btn btn-danger" id="btnEliminar">Enviar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalManagementAp">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formManagement" action="{{ route('solicitud.ap') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_sol" id="id_sol">
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="bi bi-person"></i> Nombre del Estudiante:</label>
                        <p id="nombre_m_ap" class="font-weight-bold align-items-center text-capitalize"></p>
                    </div>
                    <!-- mostrar el estado de la solicitud de baja -->
                    <div class="form-group mt-3" id="estado-solicitud-ap">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Acción a realizar:</label>
                        <p id="accion_ap" class="font-weight-bold align-items-center"></p>
                    </div>
                    <!-- justificacion de la solicitud -->
                    <div class="form-group mt-3">
                        <label class="font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Justificación:</label>
                        <p id="justificacion_ap" class="font-weight-bold align-items-center"></p>
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
                        <label for="comentario-ap" class="font-weight-bold">
                            <i class="bi bi-chat-dots"></i> Comentario
                        </label>
                        <textarea name="comentario" id="comentario-management" required class="form-control" rows="3" placeholder="Ej: Documentación incompleta."></textarea>
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

<!-- modal para enabledAp -->
<div class="modal" id="modalEnabledAp">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Habilitar Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de habilitar al estudiante?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnEnabledAp">Habilitar</button>
            </div>
        </div>
    </div>
</div>