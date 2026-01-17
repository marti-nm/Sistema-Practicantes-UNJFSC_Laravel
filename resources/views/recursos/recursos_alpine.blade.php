<script>
    function recursosData() {
        return {
            // State
            uploadModalOpen: false,
            loading: false,
            fileName: '',
            
            // Selects Data
            selectedRol: '',
            selectedFacultad: '',
            selectedEscuela: '',
            selectedSeccion: '',
            
            escuelas: [],
            secciones: [],
            
            // Context
            currentSemestre: '{{ session('semestre_actual_id') }}',
            mapaTiposDestinatario: @json($mapaTiposDestinatario),
            currentTiposPermitidos: @json($tiposPermitidos),
            tipoLabels: @json($tipoLabels),
            
            availableTypes: [],

            // Methods
            init() {
                this.availableTypes = this.currentTiposPermitidos;

                this.$watch('selectedRol', (rolId) => {
                     // Si selecciona un rol de destino (ej. Estudiante), mostramos los docs para ESE rol
                     // Pero SIEMPRE filtrado por lo que YO (Usuario Actual) tengo permiso de subir.
                     // (Aunque si soy Admin, tengo permiso de casi todo).
                     
                     if (!rolId) {
                         // Si no selecciono rol, muestro todo lo que puedo subir
                         // O podríamos decidir mostrar solo "Otros". Dejamos todo por defecto.
                         this.availableTypes = this.currentTiposPermitidos; 
                     } else {
                         const tiposParaElDestinatario = this.mapaTiposDestinatario[rolId] || [];
                         
                         // Intersección: 
                         // Tipos que el destinatario NECESITA (ej. FUT)
                         // AND
                         // Tipos que YO puedo subir (ej. Admin puede subir FUT)
                         this.availableTypes = this.currentTiposPermitidos.filter(tipo => tiposParaElDestinatario.includes(tipo));
                     }
                });

                this.$watch('selectedFacultad', (value) => {
                    this.selectedEscuela = '';
                    this.selectedSeccion = '';
                    this.escuelas = [];
                    this.secciones = [];
                    if (value) this.fetchEscuelas(value);
                });
                
                this.$watch('selectedEscuela', (value) => {
                    this.selectedSeccion = '';
                    this.secciones = [];
                    if (value) this.fetchSecciones(value);
                });
            },

            async fetchEscuelas(facultadId) {
                try {
                    const response = await fetch(`/api/escuelas/${facultadId}`);
                    this.escuelas = await response.json();
                } catch (error) {
                    console.error('Error fetching escuelas:', error);
                }
            },

            async fetchSecciones(escuelaId) {
                try {
                    // Ajustar endpoint según rutas disponibles
                    const response = await fetch(`/api/secciones/${escuelaId}/${this.currentSemestre}`);
                    this.secciones = await response.json();
                } catch (error) {
                    console.error('Error fetching secciones:', error);
                }
            },

            toggleModal(status = true) {
                this.uploadModalOpen = status;
                if (!status) {
                    this.resetForm();
                }
            },

            resetForm() {
                this.fileName = '';
                this.selectedRol = '';
                this.selectedFacultad = '';
                this.selectedEscuela = '';
                this.selectedSeccion = '';
                this.escuelas = [];
                this.secciones = [];
                this.availableTypes = this.currentTiposPermitidos;
            }
        }
    }
</script>
