@extends('template')

@section('title', 'Recursos')
@section('subtitle', 'Repositorio de Documentos y Plantillas')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="recursosData()">

    <x-header-content
        title="Recursos Disponibles"
        subtitle="En esta sección podrás encontrar documentos, plantillas y guías necesarias para el proceso de prácticas preprofesionales."
        icon="bi-cloud-arrow-up-fill"
        :enableButton="Auth::user()->hasAnyRoles([1, 2, 3, 4]) && !empty($tiposPermitidos)"
        :typeButton="2"
        msj="Subir Recurso"
        icon_msj="bi-cloud-upload"
        function="toggleModal(true)"
    />

    @include('components.skeletonLoader-table')

    <div class="overflow-x-auto mt-8">
        <table id="tablaRecursos" class="w-full text-left border-collapse table-skeleton-ready">
            <thead>
                <tr class="bg-gradient-to-r from-primary-dark to-primary text-white">
                    <th class="px-6 py-4 text-[11px] font-black uppercase tracking-[0.15em] first:rounded-tl-2xl border-none">Nombre del Recurso</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Tipo</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Nivel / Dirigido a</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Fecha Subida</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] border-none">Subido por</th>
                    <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-[0.15em] last:rounded-tr-2xl border-none">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($recursos as $recurso)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-500 border-1 border-rose-100 dark:border-rose-500/20 shadow-sm">
                                    <i class="bi bi-file-earmark-pdf-fill text-xl"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate tracking-tight">{{ $recurso->nombre }}</span>
                                    @if($recurso->descripcion)
                                        <span class="text-[10px] font-medium text-slate-400 truncate max-w-[200px]">{{ $recurso->descripcion }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-1 border-slate-200 dark:border-slate-700 shadow-sm">
                                {{ $tipoLabels[$recurso->tipo] ?? $recurso->tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                             <div class="flex flex-col items-center gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-50 text-blue-600 border border-blue-100">{{ $nivelLabels[$recurso->nivel] ?? 'N/A' }}</span>
                                @if($recurso->id_rol)
                                    @php $rolName = \App\Models\type_users::find($recurso->id_rol)->name ?? 'Rol Desconocido'; @endphp
                                    <span class="text-[10px] text-slate-400 font-medium">Solo: {{ $rolName }}</span>
                                @else
                                    <span class="text-[10px] text-slate-400 font-medium">Todos</span>
                                @endif
                             </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400 tracking-tight">{{ $recurso->created_at->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">{{ $recurso->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($recurso->uploader && $recurso->uploader->persona)
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-[10px] font-black uppercase">
                                        {{ substr($recurso->uploader->persona->nombres, 0, 1) }}{{ substr($recurso->uploader->persona->apellidos, 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-600 dark:text-slate-400 tracking-tight">
                                        {{ $recurso->uploader->persona->nombres }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs font-bold text-slate-400 italic">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ asset($recurso->ruta) }}" target="_blank"
                                    class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white transition-all border-1 border-blue-100 dark:border-blue-500/20 active:scale-95 flex items-center justify-center group/btn shadow-sm"
                                    title="Descargar">
                                    <i class="bi bi-cloud-download text-lg group-hover/btn:animate-bounce"></i>
                                </a>
                                @if(Auth::user()->hasAnyRoles([1, 2]))
                                    <form action="{{ route('recursos.destroy', $recurso->id) }}" method="POST" 
                                          class="inline-block" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar este recurso?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-all border-1 border-rose-100 dark:border-rose-500/20 active:scale-95 flex items-center justify-center group/btn shadow-sm"
                                            title="Eliminar">
                                            <i class="bi bi-trash-fill text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Subir Recurso (Alpine JS) - Estilo Compacto Premium -->
    @if(Auth::user()->hasAnyRoles([1, 2, 3, 4]) && !empty($tiposPermitidos))
    <div x-show="uploadModalOpen"
        class="fixed inset-0 z-[1100] flex items-center justify-center px-4" 
        x-cloak>
        <x-backdrop-modal name="uploadModalOpen" />

        <div x-show="uploadModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-slate-50 dark:bg-slate-900 rounded-[1.5rem] shadow-2xl w-full max-w-lg overflow-hidden border-1 border-slate-100 dark:border-slate-800">
            
            <div class="bg-gradient-to-r from-blue-950 to-blue-900 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md text-white border-1 border-white/20 dark:border-slate-700">
                            <i class="bi bi-cloud-upload text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-black tracking-tight leading-none">Subir Recurso</h3>
                            <p class="text-blue-100/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Repositorio de Documentos</p>
                        </div>
                    </div>
                    <button @click="toggleModal(false)" class="w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center text-white/70 hover:text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="p-4">
                <form action="{{ route('recursos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label for="id_rol" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                            Tipo de Usuario
                        </label>
                        <div class="relative group">
                            <select id="id_rol" name="id_rol" x-model="selectedRol"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none">
                                <option value="">Todos los usuarios</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="facultad" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                                Facultad
                            </label>
                            <div class="relative">
                                <select id="facultad" name="facultad" x-model="selectedFacultad"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none">
                                    <option value="">Todas las facultades</option>
                                    @foreach($facultades as $fac)
                                        <option value="{{ $fac->id }}">{{ $fac->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label for="escuela" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                                Escuela
                            </label>
                            <div class="relative">
                                <select id="escuela" name="escuela" x-model="selectedEscuela" :disabled="!selectedFacultad"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none">
                                    <option value="">Todas las escuelas</option>
                                    <template x-for="escuela in escuelas" :key="escuela.id">
                                        <option :value="escuela.id" x-text="escuela.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label for="seccion" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                                Sección
                            </label>
                            <div class="relative">
                                <select id="seccion" name="seccion" x-model="selectedSeccion" :disabled="!selectedEscuela"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none">
                                    <option value="">Todas las secciones</option>
                                    <template x-for="seccion in secciones" :key="seccion.id">
                                        <option :value="seccion.id" x-text="seccion.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="nombreArchivo" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                            Nombre del Documento
                        </label>
                        <div class="relative group">
                            <input type="text" id="nombreArchivo" name="nombre" required
                                class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-300 shadow-sm"
                                placeholder="Eje: Guía de Prácticas 2024">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="tipoRecurso" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                                Tipo de Recurso
                            </label>
                            <div class="relative group">
                                <select id="tipoRecurso" name="tipo" required
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm cursor-pointer appearance-none">
                                    <template x-for="tipo in availableTypes" :key="tipo">
                                        <option :value="tipo" x-text="tipoLabels[tipo] || tipo"></option>
                                    </template>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="bi bi-chevron-down text-xs text-slate-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="archivoRecurso" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                                Archivo (PDF, DOCX, XLSX)
                            </label>
                            <div class="relative group">
                                <input type="file" id="archivoRecurso" name="archivo" required accept=".pdf,.doc,.docx,.xls,.xlsx"
                                    class="hidden" @change="fileName = $event.target.files[0].name">
                                <label for="archivoRecurso" class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm font-bold text-slate-400 cursor-pointer flex items-center justify-between shadow-sm hover:border-blue-500/50 transition-all">
                                    <span class="truncate" x-text="fileName || 'Seleccionar archivo...'"></span>
                                    <i class="bi bi-paperclip text-lg text-blue-500"></i>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="descripcionRecurso" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                            Descripción (Opcional)
                        </label>
                        <textarea id="descripcionRecurso" name="descripcion" rows="3"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder:text-slate-300"
                            placeholder="Detalle breve del contenido del recurso..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6">
                        <button type="button" @click="toggleModal(false)"
                            class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-xs font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-blue-500/20 active:scale-95 transition-all flex items-center gap-2">
                            <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                            Subir Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
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
        timer: 4000,
        timerProgressBar: true,
    });
</script>
@endif

@include('recursos.recursos_alpine')

<script>
    $(document).ready(function() {
        $('#tablaRecursos').DataTable({
            language: {
                "lengthMenu": "Mostrar _MENU_",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "",
                "searchPlaceholder": "Buscar recurso...",
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
                $('#skeletonLoader').addClass('hidden');
                $('#tablaRecursos').addClass('dt-ready');
            }
        });
    });
</script>
@endpush
