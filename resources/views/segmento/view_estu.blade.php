<!-- Modal Principal de Perfil -->
<div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="modalPerfilLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPerfilLabel">
                    <i class="bi bi-person-circle me-2"></i> Gestión de Perfil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <!-- DATOS PERSONALES -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="document-card p-4 border rounded-3 mb-4" style="background: linear-gradient(145deg, #f8fafc, #f1f5f9);">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="bi bi-person-lines-fill me-2"></i> 
                                    Datos Personales
                                </h6>
                                <div class="col-md-6 text-end">
                                    <button type="button" class="btn btn-info btn-sm" id="perfEdit">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button type="submit" form="formEditPerfil" class="btn btn-success btn-sm d-none ms-2" id="perfUpdate">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                </div>
                            </div>
                            <form id="formEditPerfil" method="POST" action="{{ route('persona.editar') }}" enctype="multipart/form-data">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="persona_id" value="{{ $ap->persona->id }}">

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="codigo" class="form-label">Código</label>
                                        <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $persona->codigo ?? '' }}" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="dni" class="form-label">DNI</label>
                                        <input type="text" class="form-control" id="dni" name="dni" value="{{ $persona->dni ?? '' }}" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="celular" class="form-label">Celular</label>
                                        <input type="text" class="form-control" id="celular" name="celular" value="{{ $persona->celular ?? '' }}" disabled>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nombres" class="form-label">Nombres</label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" value="{{ $persona->nombres ?? '' }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellidos" class="form-label">Apellidos</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ $persona->apellidos ?? '' }}" disabled>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="correo_inst" class="form-label">Correo Institucional</label>
                                        <input type="email" class="form-control" id="correo_inst" name="correo_inst" value="{{ $persona->correo_inst ?? '' }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="departamento" class="form-label">Departamento</label>
                                        <input type="text" class="form-control" id="departamento" name="departamento" value="{{ $persona->departamento ?? '' }}" disabled>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="provincia" class="form-label">Provincia</label>
                                        <select class="form-control" id="provincia" name="provincia" data-valor="{{ $persona->provincia ?? '' }}" disabled>
                                            <option value="">Seleccione una provincia</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="distrito" class="form-label">Distrito</label>
                                        <select class="form-control" id="distrito" name="distrito" data-valor="{{ $persona->distrito ?? '' }}" disabled>
                                            <option value="">Seleccione un distrito</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- FOTO -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="document-card p-4 border rounded-3 mb-4" style="background: linear-gradient(145deg, #f8fafc, #f1f5f9);">
                            <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="bi bi-camera me-2"></i> 
                                    Fotografía
                            </h6>
                            <div class="card-body text-center">
                                @if ($ap->persona->ruta_foto)
                                    <img src="{{ asset($ap->persona->ruta_foto) }}" alt="Foto" class="img-fluid rounded-circle mb-3"
                                        style="width: 200px; height: 200px; object-fit: cover; border: 3px solid #c3dafe;">
                                @else
                                         <i class="img-fluid bi bi-person-fill mb-3" style="font-size: 200px; color: var(--primary-blue);"></i>
                                @endif
                                <hr>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalFoto">
                                    <i class="bi bi-camera me-1"></i> Cambiar Foto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INFO -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> Los campos marcados como solo lectura no pueden ser modificados. Para cambios, contacta al administrador.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Subir Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1" aria-labelledby="modalFotoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalFotoLabel">
                        <i class="bi bi-camera me-2"></i>
                        Subir Foto de Perfil
                    </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    @if ($ap->persona->ruta_foto)
                        <img src="{{ asset($ap->persona->ruta_foto) }}" alt="Vista previa" class="img-fluid rounded-circle" 
                            style="width: 150px; height: 150px; object-fit: cover;" 
                            id="previewImage">
                    @else
                        <i class="img-fluid bi bi-person-fill mb-3" style="font-size: 150px; color: var(--primary-blue);"></i>
                    @endif
                </div>
                <form action="{{ route('store.foto') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="persona_id" value="{{ $ap->persona->id }}">
                    <div class="upload-area-modal text-center p-4 border-2 border-dashed rounded-3" style="border-color: var(--border-gray);">
                        <i class="bi bi-camera text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="mb-3">Selecciona una imagen</p>
                        <input type="file" name="foto" accept="image/*" class="form-control" id="fotoInput">
                    </div>
                    <div class="mt-2">
                        <small class="text-muted d-block">
                            <i class="bi bi-info-circle me-1"></i> 
                            Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-upload me-1"></i>
                            Subir Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
