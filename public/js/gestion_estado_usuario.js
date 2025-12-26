document.addEventListener('DOMContentLoaded', function () {
    const btnDisabledAp = document.querySelectorAll('.btn-disabled-ap');

    const MODAL_SELECTOR = '#modalDisabledAp';
    const modalElement = document.querySelector(MODAL_SELECTOR);
    const myModal = new bootstrap.Modal(modalElement);

    btnDisabledAp.forEach(btn => {
        btn.addEventListener('click', function () {
            const ID_AP = this.getAttribute('data-id-ap');
            document.getElementById('id_ap').value = ID_AP;
            document.getElementById('nombre_ap').textContent = this.getAttribute('data-nombre-ap');
            document.getElementById('id_sa').value = this.getAttribute('data-id-sa');
            const stateAp = this.getAttribute('data-state-ap');

            const optionHabilitarAp = document.getElementById('option-habilitar-ap');
            const optionDeshabilitarAp = document.getElementById('option-deshabilitar-ap');
            if (stateAp < 4) {
                document.getElementById('alertDeshabilitarAp').classList.remove('d-none');
                document.getElementById('alertHabilitarAp').classList.add('d-none');

                optionHabilitarAp.style.display = 'none';
                optionDeshabilitarAp.style.display = 'block';

                selectCorreccion('deshabilitar', 'ap');
            } else {
                document.getElementById('alertDeshabilitarAp').classList.add('d-none');
                document.getElementById('alertHabilitarAp').classList.remove('d-none');

                optionHabilitarAp.style.display = 'block';
                optionDeshabilitarAp.style.display = 'none';
                selectCorreccion('habilitar', 'ap');
            }

            myModal.show();
        });
    });

    // Inicializar estilo por defecto
    updateCorreccionStyles('deshabilitar', 'ap');
});

// Función global para seleccionar corrección
window.selectCorreccion = function (tipo, suffix) {
    // Actualizar Radio Button
    const radioId = 'correccion' + tipo.charAt(0).toUpperCase() + tipo.slice(1) + '-' + suffix;
    const radioParams = document.getElementById(radioId);
    if (radioParams) radioParams.checked = true;

    // Actualizar Estilos Visuales
    updateCorreccionStyles(tipo, suffix);
};

function updateCorreccionStyles(selectedTipo, suffix) {
    // IDs de las celdas
    const cellDeshabilitar = document.getElementById('deshabilitar-' + suffix);
    const cellEliminar = document.getElementById('eliminar-' + suffix);

    // Reset styles
    [cellDeshabilitar, cellEliminar].forEach(cell => {
        if (cell) {
            cell.classList.remove('bg-primary', 'text-white', 'border-primary');
            cell.classList.add('border-secondary', 'text-secondary');
            cell.style.backgroundColor = '#f8f9fa'; // Un gris muy claro por defecto
        }
    });

    // Apply active style
    const activeCell = document.getElementById(selectedTipo + '-' + suffix);
    if (activeCell) {
        activeCell.classList.remove('border-secondary', 'text-secondary');
        activeCell.classList.add('bg-primary', 'text-white', 'border-primary');
        activeCell.style.backgroundColor = ''; // Limpiar inline style para que tome la clase bg-primary
    }
}

const btnManagementAp = document.querySelectorAll('.btn-management-ap');
document.addEventListener('DOMContentLoaded', function () {
    const MODAL_SELECTOR = '#modalManagementAp';
    const modalElement = document.querySelector(MODAL_SELECTOR);
    const myModal = new bootstrap.Modal(modalElement);

    btnManagementAp.forEach(button => {
        button.addEventListener('click', async function () {
            const idAp = this.getAttribute('data-id-ap');
            const nombreAp = this.getAttribute('data-nombre-ap');
            const idSa = this.getAttribute('data-id-sa');
            document.getElementById('id_ap').value = idAp;
            document.getElementById('nombre_m_ap').textContent = nombreAp;

            const reponse = await fetch(`/api/solicitud/getSolicitudAp/${idAp}`);
            const data = await reponse.json();
            console.log(data);

            document.getElementById('id_sol').value = data.id;
            document.getElementById('accion_ap').textContent = data.data.opcion.toUpperCase() ?? 'Error';
            document.getElementById('accion_ap').classList.add('font-weight-bold');

            document.getElementById('justificacion_ap').textContent = data.motivo.toUpperCase() ?? 'Error';
            document.getElementById('justificacion_ap').classList.add('font-weight-bold');

            selectGestion('aprobar');
            myModal.show();
        });
    });

    if (document.getElementById('aprobar-management')) {
        updateGestionStyles('aprobar');
    }
})

// Función para seleccionar gestión (Aprobar/Rechazar)
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

const btnEnabledAp = document.querySelectorAll('.btn-enabled-ap');
document.addEventListener('DOMContentLoaded', function () {
    const MODAL_SELECTOR = '#modalEnabledAp';
    const modalElement = document.querySelector(MODAL_SELECTOR);
    const myModal = new bootstrap.Modal(modalElement);

    btnEnabledAp.forEach(button => {
        button.addEventListener('click', function () {
            console.log('Habilitar');
            myModal.show();
        });
    });
});