@extends('template')
@section('title', 'Prácticas del Estudiante')
@section('subtitle', 'Detalles de las prácticas')

@section('content')
<div x-data="{
    confirmModalOpen: false,
    practiceType: '',
    practiceTypeText: '',
    practiceTypeValue: 0,
    
    openConfirmModal(type) {
        this.practiceType = type;
        this.practiceTypeText = type.charAt(0).toUpperCase() + type.slice(1);
        this.practiceTypeValue = (type === 'desarrollo') ? 1 : 2;
        this.confirmModalOpen = true;
    }
}" @open-confirm-modal.window="openConfirmModal($event.detail.type)">
    
    @if($practicas)
        @include('practicas.estudiante.desarrollo.est_des')
    @else
        @include('practicas.estudiante.practica')
    @endif

    <!-- Modal de Confirmación con Alpine.js y Tailwind CSS -->
    <div
        x-show="confirmModalOpen" 
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4" 
        x-cloak
        @keydown.escape.window="confirmModalOpen = false">

        <!-- Backdrop -->
        <div 
            x-show="confirmModalOpen" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100"
            class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" 
            @click="confirmModalOpen = false">
        </div>

        <!-- Modal Content -->
        <div 
            x-show="confirmModalOpen" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-white dark:bg-slate-900 rounded-[1.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 dark:border-slate-800">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border border-white/20">
                            <i class="bi bi-question-circle text-lg"></i>
                        </div>
                        <h3 class="text-white text-base font-black tracking-tight">Confirmar Selección</h3>
                    </div>
                    <button @click="confirmModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('desarrollo.store') }}" method="POST">
                @csrf
                <input type="hidden" name="ed" :value="practiceTypeValue">
                
                <!-- Body -->
                <div class="p-6 space-y-4">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 mx-auto mb-4">
                            <i class="bi bi-briefcase text-3xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 leading-relaxed">
                            ¿Estás seguro de que deseas seleccionar la modalidad 
                            <span class="font-black text-blue-600 dark:text-blue-400" x-text="practiceTypeText"></span>?
                        </p>
                    </div>
                    
                    <!-- Warning -->
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3">
                        <div class="flex items-start gap-2">
                            <i class="bi bi-info-circle text-amber-600 dark:text-amber-400 text-sm mt-0.5"></i>
                            <p class="text-xs font-semibold text-amber-800 dark:text-amber-200 leading-snug">
                                Esta acción no se puede deshacer. Una vez confirmada, no podrás cambiar el tipo de práctica.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-slate-50 dark:bg-slate-800/40 px-6 py-4 flex flex-col sm:flex-row gap-3 border-t border-slate-100 dark:border-slate-800">
                    <button 
                        type="button" 
                        @click="confirmModalOpen = false" 
                        class="flex-1 px-5 py-2.5 rounded-xl bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest hover:text-slate-700 dark:hover:text-white transition-all border border-slate-200 dark:border-slate-700">
                        Cancelar
                    </button>
                    <button 
                        type="submit" 
                        class="flex-1 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs font-black uppercase tracking-widest hover:shadow-lg hover:shadow-blue-500/30 transition-all border border-blue-600 flex items-center justify-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Función global para abrir el modal desde los botones
    function selectPracticeType(type) {
        // Disparar evento personalizado para Alpine.js
        window.dispatchEvent(new CustomEvent('open-confirm-modal', { 
            detail: { type: type } 
        }));
    }
</script>
@endpush