@extends('template')

@section('title', 'Gestión de Semestres')
@section('subtitle', 'Administración de periodos académicos')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
    newModal: false, 
    viewModalId: null,
    editModalId: null, 
    finishModalId: null,
    backModalId: null
}">
    <x-header-content
        title="Lista de Semestres"
        subtitle="Control de periodos académicos"
        icon="bi-building"
        :enableButton
    />

    <!-- Table Card -->
    <div class="">
        @include('components.skeletonLoader-table')

        <div class="overflow-x-auto">
            <table id="tablaSemestres" class="w-full text-left border-collapse table-skeleton-ready">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">ID</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Código</th>
                        <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-[0.15em] border-none">Ciclo Académico</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Estado</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900/50">
                    @foreach($semestres as $semestre)
                    <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ str_pad($semestre->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-sm font-black border border-blue-100 dark:border-blue-800/50">
                                {{ $semestre->codigo }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-black text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $semestre->ciclo }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($semestre->state == 1)
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">Activo</span>
                            @elseif($semestre->state == 2)
                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400 text-[10px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-800">Registrado</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-700">Finalizado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                @if($semestre->state == 1)
                                    {{-- Botón Finalizar --}}
                                    <button @click="finishModalId = {{ $semestre->id }}" class="px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-100 dark:border-rose-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5 text-[10px] font-black uppercase tracking-wider" title="Finalizar Semestre">
                                        Finalizar
                                    </button>
                                    
                                    {{-- Botón Editar --}}
                                    <button @click="editModalId = {{ $semestre->id }}" class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40 border border-amber-100 dark:border-amber-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5" title="Editar Ciclo">
                                        <i class="bi bi-pencil-square"></i> 
                                    </button>

                                    {{-- Botón Retroceder --}}
                                    <button @click="backModalId = {{ $semestre->id }}" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-white hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5" title="Retroceder al anterior">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                @elseif($semestre->state == 0)
                                    <button @click="viewModalId = {{ $semestre->id }}" class="px-3 py-2 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 border border-blue-100 dark:border-blue-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5 text-[10px] font-black uppercase tracking-wider" title="Ver Detalle">
                                        <i class="bi bi-eye mr-1"></i> Detalle
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nuevo Semestre -->
    <div x-show="newModal" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
        <div x-show="newModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="newModal = false"></div>
        
        <div x-show="newModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-[#111c44] to-blue-900 px-8 py-6">
                <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                    <i class="bi bi-plus-circle-fill"></i>
                    Nuevo Semestre
                </h3>
            </div>
            
            <form action="{{ route('semestre.store') }}" method="POST" class="p-8">
                @csrf
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Código del Semestre</label>
                        <div class="relative group">
                            <i class="bi bi-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-600 transition-colors group-focus-within:text-blue-500"></i>
                            <input type="text" name="codigo" placeholder="Ej: 2024-1" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Ciclo Académico</label>
                        <div class="relative group">
                            <i class="bi bi-stack absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-600 transition-colors group-focus-within:text-blue-500"></i>
                            <input type="text" name="ciclo" placeholder="Ej: IX Ciclo" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200" required>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-10">
                    <button type="button" @click="newModal = false" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-[2] px-6 py-4 bg-[#111c44] text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-800 shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                        Guardar Periodo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modales por cada Semestre -->
    @foreach($semestres as $semestre)
        <!-- Modal Visualizar -->
        <div x-show="viewModalId === {{ $semestre->id }}" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
            <div x-show="viewModalId === {{ $semestre->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="viewModalId = null"></div>
            <div x-show="viewModalId === {{ $semestre->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 dark:border-slate-800">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                    <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                        <i class="bi bi-info-circle"></i>
                        Detalle del Semestre
                    </h3>
                </div>
                <div class="p-8 space-y-4">
                    <div class="flex flex-col gap-1 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Código Oficial</span>
                        <span class="text-lg font-black text-slate-800 dark:text-white">{{ $semestre->codigo }}</span>
                    </div>
                    <div class="flex flex-col gap-1 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ciclo Académico</span>
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-300">{{ $semestre->ciclo }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado</span>
                            <span class="text-xs font-black {{ $semestre->state == 0 ? 'text-slate-500' : 'text-emerald-500' }} uppercase">
                                {{ $semestre->state == 0 ? 'Finalizado' : 'Activo' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha Registro</span>
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400">{{ $semestre->date_create }}</span>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-6 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-100 dark:border-slate-800 text-right">
                    <button @click="viewModalId = null" class="px-6 py-3 bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-300/50 dark:hover:bg-slate-700 transition-all">Cerrar</button>
                </div>
            </div>
        </div>

        <!-- Modal Editar -->
        <div x-show="editModalId === {{ $semestre->id }}" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
            <div x-show="editModalId === {{ $semestre->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="editModalId = null"></div>
            <div x-show="editModalId === {{ $semestre->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-8 py-6">
                    <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                        <i class="bi bi-pencil-square"></i>
                        Editar Semestre
                    </h3>
                </div>
                <form action="{{ route('semestre.update', $semestre->id) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')
                    <div class="space-y-6">
                        <div class="space-y-2 text-left">
                            <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Código (No editable si está activo)</label>
                            <input type="text" name="codigo" value="{{ $semestre->codigo }}" class="w-full px-4 py-3.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl font-bold text-slate-500 dark:text-slate-400 {{ $semestre->state == 1 ? 'cursor-not-allowed' : '' }}" {{ $semestre->state == 1 ? 'readonly' : '' }} required>
                            @if($semestre->state == 1)
                                <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold ml-1 uppercase">El código de un semestre activo no puede modificarse.</p>
                            @endif
                        </div>
                        <div class="space-y-2 text-left">
                            <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Ciclo Académico</label>
                            <input type="text" name="ciclo" value="{{ $semestre->ciclo }}" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-slate-700 dark:text-slate-200" required>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-10">
                        <button type="button" @click="editModalId = null" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Cancelar</button>
                        <button type="submit" class="flex-[2] px-6 py-4 bg-amber-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-amber-600 shadow-lg shadow-amber-500/20 transition-all">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        @if($semestre->state == 1)
            <!-- Modal Finalizar -->
            <div x-show="finishModalId === {{ $semestre->id }}" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
                <div x-show="finishModalId === {{ $semestre->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="finishModalId = null"></div>
                <div x-show="finishModalId === {{ $semestre->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
                    <div class="bg-gradient-to-r from-rose-500 to-red-600 px-8 py-6">
                        <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                            <i class="bi bi-flag-fill"></i>
                            Finalizar Semestre
                        </h3>
                    </div>
                    <form action="{{ route('semestre.finalizar', $semestre->id) }}" method="POST" class="p-8">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/50 rounded-2xl p-6 flex gap-4">
                                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                                    <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-amber-800 dark:text-amber-400 font-black text-sm mb-1 uppercase tracking-tight">Acción Irreversible</h4>
                                    <p class="text-amber-700 dark:text-amber-500 text-xs font-semibold leading-relaxed">
                                        Al finalizar el periodo <strong class="text-rose-600">{{ $semestre->codigo }}</strong>, este pasará a histórico y se <strong class="underline">creará automáticamente el siguiente semestre</strong>.
                                    </p>
                                </div>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 font-bold text-center">¿Estás seguro de finalizar el ciclo académico actual?</p>
                        </div>
                        <div class="flex gap-3 mt-10">
                            <button type="button" @click="finishModalId = null" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Cancelar</button>
                            <button type="submit" class="flex-[2] px-6 py-4 bg-rose-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-rose-600 shadow-lg shadow-rose-500/20 transition-all">Confirmar Finalización</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Retroceder -->
            <div x-show="backModalId === {{ $semestre->id }}" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
                <div x-show="backModalId === {{ $semestre->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/100 dark:bg-black/90 backdrop-blur-md" @click="backModalId = null"></div>
                <div x-show="backModalId === {{ $semestre->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-slate-200 dark:border-slate-800">
                    <div class="bg-slate-950 px-8 py-8 text-center">
                        <div class="w-16 h-16 bg-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl shadow-rose-500/40">
                            <i class="bi bi-arrow-counterclockwise text-white text-3xl"></i>
                        </div>
                        <h3 class="text-white text-xl font-black uppercase tracking-tight">Retroceder Semestre</h3>
                    </div>
                    <form action="{{ route('semestre.retroceder', $semestre->id) }}" method="POST" class="p-8">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">
                            <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-900/50 rounded-2xl p-5">
                                <p class="text-rose-600 dark:text-rose-400 font-black text-xs text-center leading-relaxed">
                                    ¡ALERTA! SE ELIMINARÁ EL SEMESTRE ACTUAL ({{ $semestre->codigo }}) Y SE REACTIVARÁ EL ANTERIOR.
                                </p>
                            </div>
                            <ul class="space-y-3 text-left">
                                <li class="flex items-start gap-3 text-[11px] font-bold text-slate-500">
                                    <i class="bi bi-shield-check text-emerald-500 shrink-0 mt-0.5"></i>
                                    <span>No deben existir asignaciones ni registros en este semestre.</span>
                                </li>
                                <li class="flex items-start gap-3 text-[11px] font-bold text-slate-500">
                                    <i class="bi bi-shield-check text-emerald-500 shrink-0 mt-0.5"></i>
                                    <span>Esta acción es definitiva y destructiva.</span>
                                </li>
                            </ul>
                            <p class="text-slate-800 dark:text-white font-black text-center text-sm">¿Deseas proceder bajo tu responsabilidad?</p>
                        </div>
                        <div class="flex flex-col gap-3 mt-8 text-left">
                            <button type="submit" class="w-full px-6 py-4 bg-slate-950 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-rose-600 transition-all shadow-xl active:scale-95">Confirmar Retroceso</button>
                            <button type="button" @click="backModalId = null" class="w-full px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all text-center">Mejor no</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: '{{ session('success') }}',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
});
</script>
@endif
@if(session('error'))
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'error',
    title: '{{ session('error') }}',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});
</script>
@endif
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaSemestres').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar semestre...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Sig.",
                "previous": "Ant."
            },
        },
        pageLength: 10,
        responsive: true,
        dom: '<"flex flex-col md:flex-row md:items-center justify-between gap-4 py-8 px-2"lf>rt<"flex flex-col md:flex-row md:items-center justify-between gap-4 pt-4 pb-2 px-2"ip>',
        initComplete: function() {
            // Hide skeleton and show table
            $('#skeletonLoader').addClass('hidden');
            $('#tablaSemestres').addClass('dt-ready');
        }
    });
});
</script>
@endpush
