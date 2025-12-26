@extends('estudiante')
@section('title', 'Prácticas del Estudiante')
@section('subtitle', 'Detalles de las prácticas')

@section('content')
    @if($practicas)
        @include('practicas.estudiante.desarrollo.est_des')
    @else
        @include('practicas.estudiante.practica')
    @endif
<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmationModalLabel">
                    <i class="bi bi-question-circle me-2"></i>Confirmar Selección
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('desarrollo.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="ed" id="practiceTypeInput"> 
                    <p class="mb-0">¿Estás seguro de que deseas seleccionar la modalidad <strong id="modalPracticeType" class="text-primary"></strong>?</p>
                    <p class="text-muted small mt-2"><i class="bi bi-info-circle"></i> Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let confirmationModal = null; // Instancia global del modal

    function selectPracticeType(type) {
        // Establecer texto visual
        const typeText = type.charAt(0).toUpperCase() + type.slice(1);
        document.getElementById('modalPracticeType').textContent = typeText;
        
        // Establecer valor del input hidden (1: Desarrollo, 2: Convalidación)
        const edValue = (type === 'desarrollo') ? 1 : 2;
        document.getElementById('practiceTypeInput').value = edValue;

        // Inicializar modal si no existe, o obtener instancia existente
        const modalEl = document.getElementById('confirmationModal');
        if (!confirmationModal) {
            confirmationModal = new bootstrap.Modal(modalEl);
        }
        confirmationModal.show();
    }
</script>
@endpush