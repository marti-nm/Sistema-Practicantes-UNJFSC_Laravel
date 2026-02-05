@extends('template')
@section('title', 'Gestión de Secciones por Escuela')
@section('subtitle', 'Administración de secciones académicas')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    newModal: false,
    editModalId: null,
    deleteModalId: null
}">
    <x-header-content
        title="Lista de Secciones"
        subtitle="Administración por Escuela"
        icon="bi-building"
        :enableButton="false"
    />

    <!-- Table Card -->
    <div class="">
        @include('components.skeletonLoader-table')

        <div class="overflow-x-auto">
            <table id="tablaSecciones" class="w-full text-left border-collapse table-skeleton-ready">
                <thead>
                    <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">ID</th>
                        <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-[0.15em] border-none">Facultad</th>
                        <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-[0.15em] border-none">Escuela</th>
                        <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-[0.15em] border-none">Secciones</th>
                        <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900/50">
                    @foreach($escuelas as $escuela)
                    <tr class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors duration-200">
                        <td class="px-6 py-4 text-center">
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
                            <div class="flex flex-wrap gap-2">
                                @forelse ($escuela->sa as $seccion)
                                    <span class="px-3 py-1 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-[11px] font-bold border border-blue-200 dark:border-blue-800/50 shadow-sm">
                                        {{ $seccion->seccion }}
                                    </span>
                                @empty
                                    <span class="px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-[10px] font-bold border border-slate-200 dark:border-slate-700">
                                        Sin secciones
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="editModalId = {{ $escuela->id }}" class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40 border border-amber-100 dark:border-amber-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5" title="Agregar Sección">
                                    <i class="bi bi-plus-square-dotted"></i>
                                </button>
                                <button @click="deleteModalId = {{ $escuela->id }}" class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-100 dark:border-rose-800/50 shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5" title="Gestionar/Eliminar">
                                    <i class="bi bi-eraser-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modales Agregar Sección y Gestionar/Eliminar -->
    @foreach($escuelas as $escuela)

    <!-- Modal Agregar Sección (Originalmente "Editar") -->
    <div x-show="editModalId === {{ $escuela->id }}" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
        <div x-show="editModalId === {{ $escuela->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="editModalId = null"></div>

        <div x-show="editModalId === {{ $escuela->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-8 py-6">
                <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                    <i class="bi bi-plus-circle-dotted"></i>
                    Agregar Nueva Sección
                </h3>
            </div>

            <form action="{{ route('academico.seccion.store') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="facultad_id" value="{{ $escuela->facultad->id }}">
                <input type="hidden" name="escuela_id" value="{{ $escuela->id }}">

                <div class="space-y-6">
                    <div class="space-y-2 text-left">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Escuela</label>
                        <input type="text" name="name" value="{{ $escuela->name }}" class="w-full px-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed" readonly disabled>
                    </div>

                    <div class="space-y-2 text-left">
                        <label class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">Seleccionar Sección</label>
                        <div class="relative group">
                            <i class="bi bi-collection absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-600"></i>
                            <select name="seccion" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-bold text-slate-700 dark:text-slate-200 cursor-pointer" required>
                                <option value="A">Sección A</option>
                                <option value="B">Sección B</option>
                                <option value="C">Sección C</option>
                                <option value="D">Sección D</option>
                                <option value="E">Sección E</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-10 text-left">
                    <button type="button" @click="editModalId = null" class="flex-1 px-6 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-[2] px-6 py-4 bg-amber-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-amber-600 shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                        Guardar Sección
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Gestionar/Eliminar Secciones -->
    <div x-show="deleteModalId === {{ $escuela->id }}" class="fixed inset-0 z-[1060] flex items-center justify-center px-4" x-cloak>
        <div x-show="deleteModalId === {{ $escuela->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="deleteModalId = null"></div>

        <div x-show="deleteModalId === {{ $escuela->id }}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white dark:bg-slate-900 rounded-[1rem] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800">
            <div class="bg-gradient-to-r from-rose-500 to-red-600 px-8 py-6">
                <h3 class="text-white text-xl font-black tracking-tight flex items-center gap-3">
                    <i class="bi bi-eraser-fill"></i>
                    Gestionar Secciones
                </h3>
            </div>
            <div class="p-8">
                <div class="mb-6 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Escuela: <span class="text-slate-700 dark:text-slate-300 font-black">{{ $escuela->name }}</span></p>
                </div>

                <div class="max-h-[300px] overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                    @if($escuela->sa->isEmpty())
                        <div class="py-8 text-center bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <i class="bi bi-info-circle text-slate-400 text-2xl mb-2 block"></i>
                            <span class="text-sm font-bold text-slate-500 dark:text-slate-400">No hay secciones registradas</span>
                        </div>
                    @else
                        @foreach ($escuela->sa as $seccion)
                            <div class="flex items-center justify-between p-4 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl group hover:border-rose-200 dark:hover:border-rose-900/50 transition-colors shadow-sm">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-sm border border-blue-100 dark:border-blue-800">
                                        {{ $seccion->seccion }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Sección Activa</span>
                                </div>
                                <form action="{{ route('academico.seccion.destroy', $seccion->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all" title="Eliminar Sección">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-right">
                    <button type="button" @click="deleteModalId = null" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
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
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaSecciones').DataTable({
        language: {
            "lengthMenu": "Mostrar _MENU_",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "",
            "searchPlaceholder": "Buscar...",
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
            $('#tablaSecciones').addClass('dt-ready');
        }
    });

    // Handle delete confirmations inside the modal
    $('form[action*="destroy"]').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', // rose-600
            cancelButtonColor: '#64748b', // slate-500
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-2xl dark:bg-slate-900 dark:border dark:border-slate-800',
                title: 'dark:text-white',
                htmlContainer: 'dark:text-slate-400'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    });
});
</script>
<style>
    /* Custom Scrollbar for Modal List */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #334155;
    }
</style>
@endpush
