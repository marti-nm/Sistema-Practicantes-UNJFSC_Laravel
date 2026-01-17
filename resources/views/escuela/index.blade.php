@extends('template')
@section('title', 'Gestión de Escuelas')
@section('subtitle', 'Administración de programas académicos')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
    newModal: false, 
    editModalId: null, 
    deleteModalId: null 
}">
    <x-header-content
        title="Lista de Escuelas"
        subtitle="Gestión académica oficial"
        icon="bi-building"
        :enableButton="true"
        :typeButton=2
        msj="Registrar Escuela"
        icon_msj="bi-mortarboard-fill"
        function="newModal = true"
    />

    <!-- Table Card -->
    <div class="">
        <!-- skeleton loader -->
        @include('components.skeletonLoader-table')

        <div class="overflow-x-auto">
            <table id="tablaEscuelas" class="w-full text-left border-collapse table-skeleton-ready">
                <thead>
                    {{-- centrar los titulos de la tabla --}}
                    <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">ID</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Facultad</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Nombre de la Escuela</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:bgdivide-slate-800 bg-white dark:bg-slate-900/50">
                    @foreach($escuelas as $escuela)
                    <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ str_pad($escuela->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800 shadow-sm transition-transform group-hover:scale-110">
                                    <i class="bi bi-building text-base"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 tracking-tight">{{ $escuela->facultad->name ?? 'Sin Facultad' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-black text-slate-800 dark:text-slate-200 leading-tight tracking-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $escuela->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="editModalId = {{ $escuela->id }}" class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40 border border-amber-100 dark:border-amber-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button @click="deleteModalId = {{ $escuela->id }}" class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-100 dark:border-rose-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5" title="Eliminar">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nueva Escuela -->
    <div x-show="newModal" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
        <div x-show="newModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="newModal = false"></div>
        
        <div x-show="newModal" 
            x-transition:enter="transition ease-out duration-300" 
            x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
            class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-[#111c44] to-blue-900 px-8 py-6">
                <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                    <i class="bi bi-plus-circle-fill"></i>
                    Nueva Escuela
                </h3>
            </div>
            
            <form action="{{ route('escuela.store') }}" method="POST" class="p-8">
                @csrf
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Facultad de Destino</label>
                        <div class="relative group">
                            <i class="bi bi-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-600 transition-colors group-focus-within:text-blue-500"></i>
                            <select name="facultad_id" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200" required>
                                <option value="">Selecciona Facultad</option>
                                @foreach($facultades as $facultad)
                                    <option value="{{ $facultad->id }}">{{ $facultad->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Nombre Oficial</label>
                        <div class="relative group">
                            <i class="bi bi-mortarboard absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-600 transition-colors group-focus-within:text-blue-500"></i>
                            <input type="text" name="name" placeholder="Ej: Escuela de Ingeniería" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all font-bold text-slate-700 dark:text-slate-200" required>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-10">
                    <button type="button" @click="newModal = false" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-[2] px-6 py-4 bg-[#111c44] text-white rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-blue-800 shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                        Guardar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modales Edit & Delete -->
    @foreach($escuelas as $escuela)
    <!-- Modal Editar -->
    <div x-show="editModalId === {{ $escuela->id }}" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
        <div x-show="editModalId === {{ $escuela->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="editModalId = null"></div>
        
        <div x-show="editModalId === {{ $escuela->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-8 py-6">
                <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                    <i class="bi bi-pencil-square"></i>
                    Editar Escuela
                </h3>
            </div>
            
            <form action="{{ route('escuela.update', $escuela->id) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div class="space-y-2 text-left">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Cambiar Facultad</label>
                        <select name="facultad_id" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-slate-700 dark:text-slate-200" required>
                            @foreach($facultades as $facultad)
                                <option value="{{ $facultad->id }}" {{ $facultad->id == $escuela->facultad_id ? 'selected' : '' }}>
                                    {{ $facultad->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2 text-left">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Nuevo Nombre</label>
                        <input type="text" name="name" value="{{ $escuela->name }}" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-slate-700 dark:text-slate-200" required>
                    </div>
                </div>

                <div class="flex gap-3 mt-10 text-left">
                    <button type="button" @click="editModalId = null" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-[2] px-6 py-4 bg-amber-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-amber-600 shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar -->
    <div x-show="deleteModalId === {{ $escuela->id }}" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
        <div x-show="deleteModalId === {{ $escuela->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="deleteModalId = null"></div>
        
        <div x-show="deleteModalId === {{ $escuela->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-md overflow-hidden border border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-rose-500 to-red-600 px-8 py-6">
                <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                    <i class="bi bi-trash-fill"></i>
                    Eliminar Escuela
                </h3>
            </div>
            <div class="p-10 text-center">
                <div class="w-20 h-20 bg-rose-50 dark:bg-rose-900/20 rounded-full flex items-center justify-center mx-auto mb-6 text-rose-500 dark:text-rose-400 border border-rose-100 dark:border-rose-800 shadow-inner">
                    <i class="bi bi-trash-fill text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2 tracking-tight">¿Eliminar esta escuela?</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 leading-relaxed">
                    Estás por eliminar la escuela <strong class="text-rose-600 dark:text-rose-400 font-bold">"{{ $escuela->name }}"</strong>. Esta acción no se puede deshacer y afectará los registros vinculados.
                </p>
                <div class="flex gap-3">
                    <button type="button" @click="deleteModalId = null" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        Mejor no
                    </button>
                    <form action="{{ route('escuela.destroy', $escuela->id) }}" method="POST" class="flex-[1.5]">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-6 py-4 bg-rose-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-rose-600 shadow-lg shadow-rose-500/20 transition-all active:scale-95">
                            Sí, eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaEscuelas').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar escuela...",
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
            $('#tablaEscuelas').addClass('dt-ready');
        }
    });
});
</script>
@endpush
