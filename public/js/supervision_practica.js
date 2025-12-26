document.addEventListener("DOMContentLoaded", function () {
    const state = {
        currentPracticeId: null,
        currentStage: 1,
        currentType: null,
    };

    const modalProceso = document.getElementById('modalProceso');

    const reviewFormContainer = document.getElementById('review-form-container');
    const supervisionTabContent = document.getElementById('supervisionTabContent');

    modalProceso.addEventListener('show.bs.modal', async function (event) {
        const id_practica = event.relatedTarget.getAttribute('data-id-practica');

        if (reviewFormContainer) reviewFormContainer.style.display = 'none';
        if (supervisionTabContent) supervisionTabContent.style.display = 'block';

        const etapa1Empresa = document.getElementById('empresa-container');
        const etapa1Jefe = document.getElementById('jefe-container');
        const etapa1Content = document.getElementById('supervision-container');

        if (etapa1Empresa) etapa1Empresa.style.display = 'none';
        if (etapa1Jefe) etapa1Jefe.style.display = 'none';
        if (etapa1Content) etapa1Content.style.display = 'block';

        console.log('Abriendo modal para práctica:', id_practica);

        try {
            const response = await fetch(`/practica/${id_practica}`);
            const data = await response.json();

            state.currentPracticeId = data.id;
            state.currentStage = data.state;
            state.currentType = data.tipo_practica;

            await updateStageAccess(data.state);

        } catch (error) {
            console.error('Error al obtener datos iniciales:', error);
        }
    });

    modalProceso.addEventListener('hidden.bs.modal', function () {
        state.currentPracticeId = null;
        const displayNota = document.getElementById('display-nota-final');
        const inputCalif = document.getElementById('calificacion-input');

        if (displayNota) displayNota.textContent = '...';
        if (inputCalif) inputCalif.value = '';

        updateStepper(1);
    });

    const btnEtapaEmpresa = document.getElementById('btnEtapaEmpresa');
    const btnEtapaJefe = document.getElementById('btnEtapaJefe');

    btnEtapaEmpresa.addEventListener('click', async (e) => {
        e.preventDefault();
        document.getElementById('supervision-container').style.display = 'none';
        try {
            const response = await fetch(`/api/empresa/${state.currentPracticeId}`);
            if (!response.ok) {
                console.error('Error en la respuesta del servidor.');
                document.getElementById('supervision-container').style.display = 'block';
                return;
            }
            const data = await response.json();
            console.log('data: ', data);


            document.getElementById('modal-nombre-empresa').textContent = data?.nombre || '';
            document.getElementById('modal-ruc-empresa').textContent = data?.ruc || '';
            document.getElementById('modal-razon_social-empresa').textContent = data?.razon_social || '';
            document.getElementById('modal-direccion-empresa').textContent = data?.direccion || '';
            document.getElementById('modal-telefono-empresa').textContent = data?.telefono || '';
            document.getElementById('modal-email-empresa').textContent = data?.correo || '';
            document.getElementById('modal-sitio_web-empresa').textContent = data?.web || '';

            document.getElementById('idEmpresa').value = data.id;


            if (data.state === 2) {
                console.log('Estado de la práctica:', data.state);
                document.getElementById('formProcesoEmpresa').style.display = 'none';
            } else if (data.state === 3) {
                document.getElementById('correction-data-empresa').style.display = 'block';
                document.getElementById('formProcesoEmpresa').style.display = 'none';
            } else {
                document.getElementById('formProcesoEmpresa').style.display = 'block';
            }
            document.getElementById('empresa-container').style.display = 'block';
            document.getElementById('jefe-container').style.display = 'none';
            document.getElementById('supervision-container').style.display = 'none';
        } catch (error) {
            console.error('Error al obtener datos:', error);
            document.getElementById('supervision-container').style.display = 'block';
        }
    });

    btnEtapaJefe.addEventListener('click', async (e) => {
        e.preventDefault();
        document.getElementById('supervision-container').style.display = 'none';

        try {
            const response = await fetch(`/api/jefeinmediato/${state.currentPracticeId}`);
            if (!response.ok) {
                console.error('Error en la respuesta del servidor.');
                document.getElementById('supervision-container').style.display = 'block';
                return;
            }
            const data = await response.json();

            document.getElementById('modal-name-jefe').textContent = data?.nombres || '';
            document.getElementById('modal-area-jefe').textContent = data?.area || '';
            document.getElementById('modal-cargo-jefe').textContent = data?.cargo || '';
            document.getElementById('modal-dni-jefe').textContent = data?.dni || '';
            document.getElementById('modal-sitio_web-jefe').textContent = data?.web || '';
            document.getElementById('modal-telefono-jefe').textContent = data?.telefono || '';
            document.getElementById('modal-email-jefe').textContent = data?.correo || '';

            //document.getElementById('etapa1-jefe').style.display = 'block';

            // limpar antes de nada
            document.getElementById('correction-data-jefe').style.display = 'none';

            if (data.state === 2) {
                console.log('Estado de la práctica:', data.state);
                document.getElementById('formProcesoJefe').style.display = 'none';
            } else if (data.state === 3) {
                document.getElementById('correction-data-jefe').style.display = 'block';
                document.getElementById('formProcesoJefe').style.display = 'none';
            } else {
                document.getElementById('formProcesoJefe').style.display = 'block';
            }

            document.getElementById('idJefe').value = data.id;

            document.getElementById('empresa-container').style.display = 'none';
            document.getElementById('jefe-container').style.display = 'block';
            document.getElementById('supervision-container').style.display = 'none';
        } catch (error) {
            console.error('Error al obtener datos:', error);
            document.getElementById('supervision-container').style.display = 'block';
        }
    });

    // function controller stepper 2 - 4
    document.addEventListener('click', async function (e) {
        if (e.target.closest('.btn-review-doc')) {
            e.preventDefault();

            const btn = e.target.closest('.btn-review-doc');
            const docType = btn.getAttribute('data-doctype');
            console.log(state.currentPracticeId, docType);
            // primero verificar el estado documento
            try {

            } catch (error) {
                console.error('Error al obtener datos:', error);
            }


            openReviewForm(state.currentPracticeId, docType);
        }
    });

    async function openReviewForm(practiceId, docType) {
        try {
            const response = await fetch(`/api/documento/${practiceId}/${docType}`);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            console.log('data: ', data);

            // Style Header
            const headerStyle = document.getElementById('review-form-header').classList;
            headerStyle.remove(...headerStyle);
            headerStyle.add('etapa-header');
            headerStyle.add('bg-header');
            headerStyle.add(docType);

            // Title
            document.getElementById('review-form-title').textContent = `Revisión de ${formatDocType(docType)}`;

            if (data && data.length > 0) {
                const docData = data[0]; // Assuming the API returns an array

                // Populate Form
                document.getElementById('review_file_id').value = docData.id;
                document.getElementById('review_file_name').textContent = docData.nombre_archivo || `${docType}.pdf`; // Fallback name
                document.getElementById('review_file_date').textContent = `Fecha: ${formatDate(docData.created_at)}`;

                // Status Badge
                const badge = document.getElementById('review_file_status_badge');
                badge.textContent = docData.estado_archivo || 'Pendiente';
                badge.className = 'badge ' + (docData.estado_archivo === 'Aprobado' ? 'bg-success' : 'bg-secondary');

                // Link
                const link = document.getElementById('review_file_link');
                link.href = docData.ruta;

                document.getElementById('approved_file_name').textContent = docData.nombre_archivo || `${docType}.pdf`; // Fallback name
                document.getElementById('approved_file_date').textContent = `Fecha: ${formatDate(docData.created_at)}`;

                // Status Badge
                const badgeApproved = document.getElementById('approved_file_status_badge');
                badgeApproved.textContent = docData.estado_archivo || 'Pendiente';
                badgeApproved.className = 'badge ' + (docData.estado_archivo === 'Aprobado' ? 'bg-success' : 'bg-secondary');

                // Link
                const linkApproved = document.getElementById('approved_file_link');
                linkApproved.href = docData.ruta;


                document.getElementById('no-file-container').style.display = 'none';
                if (docData.estado_archivo === 'Aprobado') {
                    console.log('estado_archivo APRO: ', docData.estado_archivo);
                    document.getElementById('approved-file-container').style.display = 'block';
                    document.getElementById('genericReviewForm').style.display = 'none';
                } else if (docData.estado_archivo === 'Corregir') {
                    document.getElementById('no-file-container').style.display = 'block';
                    document.getElementById('genericReviewForm').style.display = 'none';
                    document.getElementById('approved-file-container').style.display = 'none';
                } else {
                    console.log('estado_archivo DA: ', docData.estado_archivo);
                    document.getElementById('approved-file-container').style.display = 'none';
                    document.getElementById('genericReviewForm').style.display = 'block';
                }

                // Switch View
                supervisionTabContent.style.display = 'none';
                reviewFormContainer.style.display = 'block';
                reviewFormContainer.classList.add('fade-in');

            } else {
                // Handle no document found (maybe show a toast)
                console.warn('No document found for this type');
                document.getElementById('no-file-container').style.display = 'block';
                document.getElementById('genericReviewForm').style.display = 'none';
                document.getElementById('approved-file-container').style.display = 'none';
            }

            // Switch View
            supervisionTabContent.style.display = 'none';
            reviewFormContainer.style.display = 'block';
            reviewFormContainer.classList.add('fade-in');

        } catch (error) {
            console.error('Error fetching document data:', error);
        }
    }

    function formatDocType(type) {
        // Helper to format "carta_presentacion" -> "Carta Presentación"
        return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function formatDate(dateString) {
        if (!dateString) return 'No disponible';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-PE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    async function viewEtapaCalificacion() {
        const response = await fetch(`/api/practica/getCalificacion/${state.currentPracticeId}`);
        if (!response.ok) throw new Error('Network response was not ok');

        const data = await response.json();

        console.log('data del getCalificacion: ', data);

        // idE5

        const seccionCalificarForm = document.getElementById('seccion-calificar-form');
        const seccionYaCalificado = document.getElementById('seccion-ya-calificado');
        const displayNota = document.getElementById('display-nota-final');
        const inputCalif = document.getElementById('calificacion-input');
        const estado = parseInt(data.state) || 1;

        if (seccionCalificarForm && seccionYaCalificado) {
            // Lógica: 
            // - Si state = 5 (sin calificar aún): mostrar formulario
            // - Si state = 6 (ya calificado): mostrar nota y opción de editar
            if (estado >= 6 && data.calificacion !== null && data.calificacion !== undefined) {
                // Ya calificado (state = 6)
                seccionCalificarForm.classList.add('d-none');
                seccionYaCalificado.classList.remove('d-none');
                if (displayNota) displayNota.textContent = parseFloat(data.calificacion).toFixed(2);
            } else {
                // Aún puede calificar (state = 5 o recién habilitado por admin)
                document.getElementById('idE5').value = data.id;
                seccionCalificarForm.classList.remove('d-none');
                seccionYaCalificado.classList.add('d-none');
                if (inputCalif) inputCalif.value = '';
            }

            const btnSolicitarRevision = document.getElementById('btn-solicitar-revision');
            const btnSolicitarEdicion = document.getElementById('btn-solicitar-edicion');

            const alertSolicitudEnviada = document.getElementById('alert-solicitud-enviada');

            if (estado === 7) {
                console.log('estado === 7', estado);
                if (btnSolicitarRevision) btnSolicitarRevision.classList.remove('d-none');
                // desabilitar btnSolicitarEdicion
                if (btnSolicitarEdicion) btnSolicitarEdicion.classList.add('d-none');
                if (alertSolicitudEnviada) alertSolicitudEnviada.classList.remove('d-none');
            } else {
                if (btnSolicitarRevision) btnSolicitarRevision.classList.add('d-none');
                if (btnSolicitarEdicion) btnSolicitarEdicion.classList.remove('d-none');
                if (alertSolicitudEnviada) alertSolicitudEnviada.classList.add('d-none');
            }
        }
    }

    // Delegación para el botón de solicitar edición (Etapa 5)
    document.addEventListener('click', async function (e) {
        if (e.target.closest('#btn-solicitar-edicion')) {
            const modalEl = document.getElementById('modalSolicitarEdicion');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                document.getElementById('id_solicitud_nota').value = state.currentPracticeId;
                modal.show();
            }
        }
        if (e.target.closest('#btn-solicitar-revision')) {
            const modalEl = document.getElementById('modalSolicitarRevision');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                //document.getElementById('id_solicitud_nota').value = state.currentPracticeId;
                try {
                    const response = await fetch(`/api/solicitud/getSolicitudNota/${state.currentPracticeId}`);
                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();
                    console.log('data del getSolicitudNota: ', data);
                    document.getElementById('id_practica').value = data.solicitudable_id;
                    document.getElementById('motivo_sol').textContent = data.motivo + ' -  id ' + data.solicitudable_id;
                } catch (error) {
                    console.error('Error fetching document data:', error);
                }

                // Resetear a "aprobar" por defecto cada vez que abre
                selectGestion('aprobar');
                modal.show();
            }
        }
    });

    window.selectGestion = function (tipo) {
        const radioId = 'gestion' + tipo.charAt(0).toUpperCase() + tipo.slice(1);
        const radioBtn = document.getElementById(radioId);
        if (radioBtn) radioBtn.checked = true;

        updateGestionStyles(tipo);
    };

    function updateGestionStyles(selectedTipo) {
        const cellAprobar = document.getElementById('aprobar-management');
        const cellRechazar = document.getElementById('rechazar-management');

        // Reset
        [cellAprobar, cellRechazar].forEach(cell => {
            if (cell) {
                cell.classList.remove('bg-success', 'bg-danger', 'text-white', 'border-success', 'border-danger');
                cell.classList.add('border-secondary', 'text-secondary');
                cell.style.backgroundColor = '#f8f9fa';
            }
        });

        // Active
        const activeCell = document.getElementById(selectedTipo + '-management');
        if (activeCell) {
            activeCell.classList.remove('border-secondary', 'text-secondary');
            activeCell.style.backgroundColor = '';

            if (selectedTipo === 'aprobar') {
                activeCell.classList.add('bg-success', 'text-white', 'border-success');
            } else {
                activeCell.classList.add('bg-danger', 'text-white', 'border-danger');
            }
        }
    }

    // Back to list
    const btnBackToList = document.getElementById('btn-back-to-list');
    btnBackToList.addEventListener('click', function () {
        reviewFormContainer.style.display = 'none';
        supervisionTabContent.style.display = 'block';

        // Add fade-in animation
        supervisionTabContent.classList.add('fade-in');
        setTimeout(() => supervisionTabContent.classList.remove('fade-in'), 500);
    });

    document.querySelectorAll('.btn-regresar-etapa1').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('supervision-container').style.display = 'block';
            document.getElementById('empresa-container').style.display = 'none';
            document.getElementById('jefe-container').style.display = 'none';
        });
    });

    function updateStepper(selectedStage) {
        const items = document.querySelectorAll('.stepper-item');

        items.forEach(item => {
            const stage = parseInt(item.getAttribute('data-stage'));
            const circle = item.querySelector('.stepper-circle');

            // Reset classes
            item.classList.remove('completed', 'current', 'locked');

            if (stage < selectedStage) {
                // Past relative to selection
                item.classList.add('completed');
                circle.innerHTML = '<i class="bi bi-check-lg"></i>';
            } else if (stage === selectedStage) {
                // Selected
                item.classList.add('current');
                circle.innerHTML = stage;
            } else {
                // Future relative to selection
                if (stage <= globalMaxStage) {
                    // Unlocked but future (show as completed style but with number, or just unlocked)
                    // The user wants it to look like est_des.blade.php which uses 'completed' class but keeps the number
                    item.classList.add('completed');
                    circle.innerHTML = stage;
                } else {
                    // Locked
                    item.classList.add('locked');
                    circle.innerHTML = '<i class="bi bi-lock-fill"></i>';
                }
            }

            // Click handlers
            if (stage <= globalMaxStage) {
                item.style.cursor = 'pointer';
                item.onclick = () => switchTab(stage);
            } else {
                item.style.cursor = 'not-allowed';
                item.onclick = null;
            }
        });
    }

    async function switchTab(stage) {
        const panes = document.querySelectorAll('.tab-pane');
        panes.forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        const targetPane = document.getElementById(`content-stage-${stage}`);
        if (targetPane) {
            targetPane.classList.add('show', 'active');
        }

        // Also hide review form if open
        const reviewFormContainer = document.getElementById('review-form-container');
        const supervisionTabContent = document.getElementById('supervisionTabContent');

        document.getElementById('empresa-container').style.display = 'none';
        document.getElementById('jefe-container').style.display = 'none';
        document.getElementById('supervision-container').style.display = 'block';
        if (reviewFormContainer && supervisionTabContent) {
            reviewFormContainer.style.display = 'none';
            supervisionTabContent.style.display = 'block';
        }

        updateStepper(stage);

        if (stage >= 5) await viewEtapaCalificacion();
        document.getElementById('seccion-convalidacion-E2').style.display = (stage == 2 && state.currentType == 'convalidacion') ? 'block' : 'none';
        document.getElementById('seccion-desarrollo-E3').style.display = (stage == 3 && state.currentType == 'convalidacion') ? 'none' : 'block';
        document.getElementById('seccion-convalidacion-E3').style.display = (stage == 3 && state.currentType == 'convalidacion') ? 'block' : 'none';
    }

    async function updateStageAccess(estado) {
        // Estado 6 significa calificado, pero la etapa visible máxima es 5
        const stageForNav = estado >= 6 ? 5 : estado;
        globalMaxStage = Math.min(Math.max(parseInt(stageForNav) || 1, 1), 5);

        // Show the current stage (max stage) by default when opening
        await switchTab(globalMaxStage);
    }
});