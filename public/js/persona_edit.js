function previewImagen(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById('previewFoto');
            const icon = document.getElementById('iconoDefault');
            img.src = e.target.result;
            img.style.display = 'block';
            if (icon) icon.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
}

async function cargarProvinciasEn(selectElement, selectedProvinciaId = null) {
    try {
        const response = await fetch('/data/provincias.json');
        const data = await response.json();

        selectElement.innerHTML = '<option value="">Seleccione</option>';
        data.provincias.forEach(provincia => {
            const option = document.createElement('option');
            option.value = provincia.id;
            option.textContent = provincia.nombre;
            if (selectedProvinciaId && provincia.id == selectedProvinciaId) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });

        selectElement.disabled = false;
    } catch (error) {
        console.error('Error al cargar provincias:', error);
    }
}

async function cargarDistritosEn(selectElement, provinciaId, selectedDistritoId = null) {
    try {
        const response = await fetch('/data/distritos.json');
        const data = await response.json();

        selectElement.innerHTML = '<option value="">Seleccione un distrito</option>';

        if (data.distritos[provinciaId]) {
            data.distritos[provinciaId].forEach(distrito => {
                const option = document.createElement('option');
                option.value = distrito.id;
                option.textContent = distrito.nombre;
                if (selectedDistritoId && distrito.id == selectedDistritoId) {
                    option.selected = true;
                }
                selectElement.appendChild(option);
            });

            selectElement.disabled = false;
        }
    } catch (error) {
        console.error('Error al cargar distritos:', error);
    }
}


// Iniciar cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', function () {
    // Configurar los eventos para cada modal
    $('.modal').on('show.bs.modal', async function (event) {
        const button = event.relatedTarget;
        const provinciaId = button.getAttribute('data-p');
        const distritoId = button.getAttribute('data-d');
        const facultadId = button.getAttribute('data-f');
        const escuelaId = button.getAttribute('data-e');

        const provinciaSelect = this.querySelector('select[name="provincia"]');
        const distritoSelect = this.querySelector('select[name="distrito"]');
        const facultadSelect = this.querySelector('select[name="facultad"]');
        const escuelaSelect = this.querySelector('select[name="escuela"]');

        // Cargar provincias
        await cargarProvinciasEn(provinciaSelect, provinciaId);

        // Cargar distritos
        if (provinciaId) {
            await cargarDistritosEn(distritoSelect, provinciaId, distritoId);
        }

        // Lógica de facultad y escuela
        if (facultadId && facultadSelect && escuelaSelect) {
            facultadSelect.value = facultadId;
            escuelaSelect.disabled = false;

            Array.from(escuelaSelect.options).forEach(option => {
                const escuelaFacultadId = option.getAttribute('data-facultad');
                option.hidden = (option.value !== "") && escuelaFacultadId !== facultadId;
            });

            if (escuelaId) {
                escuelaSelect.value = escuelaId;
            }
        }

        facultadSelect.addEventListener('change', function () {
            const facultadId = this.value;

            if (!facultadId) {
                escuelaSelect.disabled = true;
                escuelaSelect.value = "";
                Array.from(escuelaSelect.options).forEach(option => option.hidden = true);
                return;
            }

            escuelaSelect.disabled = false;

            Array.from(escuelaSelect.options).forEach(option => {
                const escuelaFacultadId = option.getAttribute('data-facultad');

                option.hidden = (option.value !== "") && escuelaFacultadId !== facultadId;
            });

            escuelaSelect.value = "";
        });

        provinciaSelect.disabled = true;
        distritoSelect.disabled = true;
        facultadSelect.disabled = true;
        escuelaSelect.disabled = true;

        provinciaSelect.addEventListener('change', function () {
            const newProvinciaId = this.value;
            cargarDistritosEn(distritoSelect, newProvinciaId);
        });
    });

    // Configurar el botón de editar para todos los modales
    document.querySelectorAll('.modal').forEach(modal => {
        const editBtn = modal.querySelector('#btnEditar');
        const updateBtn = modal.querySelector('#btnUpdate');
        const formInputs = modal.querySelectorAll('#dni, #celular, #nombres, #apellidos, #provincia, #distrito, #sexo');

        let editing = false;

        if (editBtn) {
            editBtn.addEventListener('click', function () {
                editing = !editing; // alterna el estado

                if (editing) {
                    // Activar campos
                    formInputs.forEach(input => input.removeAttribute('readonly'));
                    updateBtn.classList.remove('d-none');
                    editBtn.innerHTML = '<i class="fas fa-times"></i> Cancelar';
                    editBtn.classList.remove('btn-info');
                    editBtn.classList.add('btn-warning');
                    formInputs.forEach(input => input.removeAttribute('disabled'));

                } else {
                    // Restaurar campos
                    formInputs.forEach(input => {
                        input.setAttribute('readonly', true);
                    });
                    updateBtn.classList.add('d-none');
                    editBtn.innerHTML = '<i class="fas fa-edit"></i> Editar';
                    editBtn.classList.remove('btn-warning');
                    editBtn.classList.add('btn-info');
                }
            });
        }
    });
});