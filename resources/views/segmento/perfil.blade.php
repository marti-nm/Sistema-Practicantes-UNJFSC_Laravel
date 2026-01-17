@extends('template')
@section('title', 'Mi Perfil')
@section('subtitle', 'Gestionar información personal y configuración de cuenta')

@section('content')
<div class="max-w-5xl mx-auto pb-20" x-data="profileManagement()">
    
    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 z-50 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm flex items-center justify-center transition-all duration-300" 
         x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="flex flex-col items-center gap-4">
            <div class="w-10 h-10 border-2 border-slate-200 border-t-slate-800 dark:border-slate-700 dark:border-t-white rounded-full animate-spin"></div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" class="fixed inset-0 z-50 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-sm flex items-center justify-center transition-all duration-300" 
         x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="flex flex-col items-center gap-4">
            <div class="w-10 h-10 border-2 border-slate-200 border-t-slate-800 dark:border-slate-700 dark:border-t-white rounded-full animate-spin"></div>
        </div>
    </div>

    <!-- Main Form -->
    <form method="POST" action="{{ route('persona.editar') }}" enctype="multipart/form-data" @submit="loading = true">
        @csrf
        <input type="hidden" name="persona_id" value="{{ $persona->id }}">

        <div class="space-y-20 mt-8">
            
            <!-- SECTION 1: PHOTO -->
            <section id="photo" class="group">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start">
                    <!-- Section Title -->
                    <div class="md:col-span-4">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Fotografía</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Esta imagen se mostrará en tu perfil y en los listados de estudiantes. Soporta JPG, PNG y GIF.
                        </p>
                    </div>

                    <!-- Content -->
                    <div class="md:col-span-8">
                        <div class="flex items-center gap-8">
                            <div class="relative w-32 h-32 flex-shrink-0">
                                <div class="w-full h-full rounded-full overflow-hidden shadow-xl ring-4 ring-white dark:ring-slate-800 bg-slate-100 dark:bg-slate-800">
                                    <template x-if="previewUrl">
                                        <img :src="previewUrl" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!previewUrl && '{{ $persona->ruta_foto }}'">
                                        <img src="{{ asset($persona->ruta_foto) }}" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!previewUrl && !'{{ $persona->ruta_foto }}'">
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <i class="bi bi-person-fill text-5xl"></i>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Hidden File Input -->
                                <input type="file" name="ruta_foto" x-ref="photoInput" class="hidden" accept="image/*" @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                            </div>

                            <div class="space-y-4">
                                <button type="button" @click="$refs.photoInput.click()" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 text-white dark:text-slate-900 text-xs font-bold uppercase tracking-widest rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                                    Cambiar imagen
                                </button>
                                <button type="submit" x-show="previewUrl" class="block w-full px-5 py-2.5 bg-blue-600 text-white text-xs font-bold uppercase tracking-widest rounded-lg hover:bg-blue-700 transition-all animate-fade-in-up">
                                    Guardar Foto
                                </button>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">
                                    Recomendado 500x500 px <br> Máximo 2MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <div class="h-px w-full bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-700 to-transparent"></div>

            <!-- SECTION 2: PERSONAL DATA -->
            <section id="personal" class="group">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start">
                    <!-- Section Title -->
                    <div class="md:col-span-4 sticky top-10">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Información Personal</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-6">
                            Mantén tu información personal actualizada para facilitar la comunicación y los trámites administrativos.
                        </p>
                        
                        <div class="flex items-center gap-2">
                             <div class="relative inline-flex items-center cursor-pointer" @click="isEditing = !isEditing">
                                <input type="checkbox" class="sr-only peer" :checked="isEditing">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-slate-900"></div>
                                <span class="ml-3 text-[10px] font-bold uppercase tracking-widest text-slate-500" x-text="isEditing ? 'Edición Habilitada' : 'Habilitar Edición'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="md:col-span-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                            
                            <!-- Read Only Identifiers -->
                            <div class="md:col-span-2 grid grid-cols-2 gap-6 mb-2">
                                <div class="group/input">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Código</label>
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-3 py-2.5 rounded-lg border-1 border-slate-200 dark:border-slate-700">
                                        {{ $persona->codigo }}
                                    </div>
                                </div>
                                <div class="group/input">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Correo Institucional</label>
                                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-3 py-2.5 rounded-lg border-1 border-slate-200 dark:border-slate-700 truncate">
                                        {{ $persona->correo_inst }}
                                    </div>
                                </div>
                            </div>

                            <!-- Editable Inputs Style: Boxed -->
                            <div class="group/input">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 transition-colors" :class="{'text-blue-500': isEditing}">DNI</label>
                                <input type="text" name="dni" value="{{ $persona->dni }}" :disabled="!isEditing"
                                    class="block w-full text-xs font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-500 transition-all py-2.5 px-3 placeholder-slate-300">
                            </div>

                            <div class="group/input">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 transition-colors" :class="{'text-blue-500': isEditing}">Celular</label>
                                <input type="tel" name="celular" value="{{ $persona->celular }}" :disabled="!isEditing"
                                    class="block w-full text-xs font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-500 transition-all py-2.5 px-3 placeholder-slate-300">
                            </div>

                            <div class="group/input md:col-span-2">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 transition-colors" :class="{'text-blue-500': isEditing}">Nombres</label>
                                <input type="text" name="nombres" value="{{ $persona->nombres }}" :disabled="!isEditing"
                                    class="block w-full text-xs font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-500 transition-all py-2.5 px-3 placeholder-slate-300">
                            </div>

                            <div class="group/input md:col-span-2">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 transition-colors" :class="{'text-blue-500': isEditing}">Apellidos</label>
                                <input type="text" name="apellidos" value="{{ $persona->apellidos }}" :disabled="!isEditing"
                                    class="block w-full text-xs font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-500 transition-all py-2.5 px-3 placeholder-slate-300">
                            </div>

                            <!-- Location Block -->
                            <div class="md:col-span-2 mt-2">
                                <span class="text-xs font-bold text-slate-900 dark:text-white mb-4 block">Ubicación Geográfica</span>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    
                                    <!-- Departamento (Always Read-Only) -->
                                    <div class="group/input">
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Dpto.</label>
                                        <input type="text" value="{{ $persona->departamento }}" disabled
                                            class="block w-full text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 border-1 border-slate-200 dark:border-slate-700 rounded-lg py-2.5 px-3">
                                    </div>

                                    <!-- Provincia -->
                                    <div class="group/input">
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Provincia</label>
                                        
                                        <!-- Read Only Text (Computed) -->
                                        <div x-show="!isEditing" class="text-xs font-bold text-slate-700 dark:text-slate-300 py-2.5 px-3 border-1 border-slate-200 dark:border-slate-700 rounded-lg" x-text="getProvinciaName()"></div>
                                        
                                        <!-- Interactive Select & Hidden Input for DB -->
                                        <div x-show="isEditing" class="relative">
                                            <input type="hidden" name="provincia" :value="getProvinciaName()">
                                            <select x-model="selectedProvincia" @change="updateDistritos()"
                                                class="block w-full text-xs font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all py-2.5 px-3 appearance-none">
                                                <option value="">Seleccione</option>
                                                <template x-for="prov in provincias" :key="prov.id">
                                                    <option :value="prov.id" x-text="prov.nombre"></option>
                                                </template>
                                            </select>
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                                <i class="bi bi-chevron-down text-[10px]"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Distrito -->
                                    <div class="group/input">
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Distrito</label>
                                        
                                        <!-- Read Only Text (Computed) -->
                                        <div x-show="!isEditing" class="text-xs font-bold text-slate-700 dark:text-slate-300 py-2.5 px-3 border-1 border-slate-200 dark:border-slate-700 rounded-lg" x-text="getDistritoName()"></div>
                                        
                                        <!-- Interactive Select & Hidden Input for DB -->
                                        <div x-show="isEditing" class="relative">
                                            <input type="hidden" name="distrito" :value="getDistritoName()">
                                            <select x-model="selectedDistrito" :disabled="!selectedProvincia"
                                                class="block w-full text-xs font-bold text-slate-900 dark:text-white bg-slate-50 dark:bg-slate-900 border-1 border-slate-200 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-500 transition-all py-2.5 px-3 appearance-none">
                                                <option value="">Seleccione</option>
                                                <template x-for="dist in distritos_options" :key="dist.id">
                                                    <option :value="dist.id" x-text="dist.nombre"></option>
                                                </template>
                                            </select>
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                                <i class="bi bi-chevron-down text-[10px]"></i>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        
                        <!-- Actions -->
                        <div x-show="isEditing" class="mt-8 flex items-center justify-end gap-4 animate-fade-in-up">
                            <button type="button" @click="cancelEditing()" class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-red-500 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-slate-900 dark:bg-blue-600 text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Divider -->
            <div class="h-px w-full bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-700 to-transparent"></div>

            <!-- SECTION 3: ACADEMIC -->
            <section id="academic" class="scroll-mt-32 group">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start opacity-70 group-hover:opacity-100 transition-opacity duration-500">
                    <!-- Section Title -->
                    <div class="md:col-span-4">
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Información Académica</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            Detalles de tu asignación actual.
                            @if($persona->asignacion_persona && $persona->asignacion_persona->state != 1)
                                <span class="text-blue-500 font-bold block mt-1">Puedes editar estos datos mientras la validación esté pendiente.</span>
                            @endif
                        </p>
                    </div>

                    <!-- Content -->
                    <div class="md:col-span-8">
                         @if($persona->asignacion_persona)
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border-1 border-slate-100 dark:border-slate-800">
                                
                                {{-- If Validated: Read Only --}}
                                @if($persona->asignacion_persona->state == 1)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-1">Facultad</span>
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                                {{ $persona->asignacion_persona->seccion_academica->facultad->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-1">Escuela</span>
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                                {{ $persona->asignacion_persona->seccion_academica->escuela->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-1">Sección</span>
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                                {{ $persona->asignacion_persona->seccion_academica->seccion ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center gap-2">
                                        <i class="bi bi-shield-check text-emerald-500"></i>
                                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide">Asignación Validada y Activa</span>
                                    </div>
                                
                                {{-- If Not Validated: Editable --}}
                                @else
                                    <div class="grid grid-cols-1 gap-6">
                                        
                                        <!-- Facultad -->
                                        <div class="group/input">
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Facultad</label>
                                            <select x-model="selectedFacultad" @change="loadEscuelas(); selectedEscuela=''; selectedSeccionAcad=''" class="block w-full text-xs font-medium text-slate-900 dark:text-white bg-transparent border-0 border-b border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-slate-900 transition-all py-1.5 appearance-none rounded-none">
                                                <option value="">Seleccione Facultad</option>
                                                @foreach($facultades as $fac)
                                                    <option value="{{ $fac->id }}">{{Str::limit($fac->name, 40)}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Escuela -->
                                        <div class="group/input">
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Escuela</label>
                                            <select x-model="selectedEscuela" @change="loadSecciones(); selectedSeccionAcad=''" :disabled="!selectedFacultad" class="block w-full text-xs font-medium text-slate-900 dark:text-white bg-transparent border-0 border-b border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-slate-900 disabled:border-slate-100 disabled:text-slate-500 transition-all py-1.5 appearance-none rounded-none">
                                                <option value="">Seleccione Escuela</option>
                                                <template x-for="esc in escuelas" :key="esc.id">
                                                    <option :value="esc.id" x-text="esc.name"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <!-- Sección -->
                                        <div class="group/input">
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Sección</label>
                                            <select name="seccion_id" x-model="selectedSeccionAcad" :disabled="!selectedEscuela" class="block w-full text-xs font-medium text-slate-900 dark:text-white bg-transparent border-0 border-b border-slate-200 dark:border-slate-700 focus:ring-0 focus:border-slate-900 disabled:border-slate-100 disabled:text-slate-500 transition-all py-1.5 appearance-none rounded-none">
                                                <option value="">Seleccione Sección</option>
                                                <template x-for="sec in secciones" :key="sec.id">
                                                    <option :value="sec.id" x-text="sec.name"></option>
                                                </template>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-clock-history text-amber-500"></i>
                                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wide">Pendiente de Validación</span>
                                        </div>
                                        <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-[10px] font-bold uppercase tracking-widest rounded shadow hover:bg-blue-700 transition-colors">
                                            Actualizar Asignación
                                        </button>
                                    </div>
                                @endif
                            </div>
                         @else
                            <div class="p-6 text-center border-2 border-dashed border-slate-200 rounded-2xl">
                                <span class="text-xs text-slate-400">Sin información académica registrada</span>
                            </div>
                         @endif
                    </div>
                </div>
            </section>

        </div>
    </form>
</div>
@endsection

@push('js')
<script>
    function profileManagement() {
        return {
            loading: false,
            isEditing: false,
            previewUrl: null,
            provincias: [],
            all_distritos: {},
            distritos_options: [],
            selectedProvincia: '{{ $persona->provincia }}',
            selectedDistrito: '{{ $persona->distrito }}',
            
            // Academic Data
            escuelas: [],
            secciones: [],
            selectedFacultad: '{{ $persona->asignacion_persona?->seccion_academica?->id_facultad ?? "" }}',
            selectedEscuela: '{{ $persona->asignacion_persona?->seccion_academica?->id_escuela ?? "" }}',
            selectedSeccionAcad: '{{ $persona->asignacion_persona?->id_sa ?? "" }}',
            semestreId: '{{ $persona->asignacion_persona?->id_semestre ?? session("semestre_actual_id") }}',

            async init() {
                // Initialize Locations
                try {
                    const [provRes, distRes] = await Promise.all([
                        fetch('/data/provincias.json'),
                        fetch('/data/distritos.json')
                    ]);
                    const provData = await provRes.json();
                    const distData = await distRes.json();
                    this.provincias = provData.provincias || [];
                    this.all_distritos = distData.distritos || {};
                    
                    // Match Name to ID logic
                    if (this.selectedProvincia) {
                        // First try finding by ID (if saved as ID)
                        let prov = this.provincias.find(p => p.id == this.selectedProvincia); 
                        if (!prov) {
                            // If not found, try finding by Name (case insensitive)
                            prov = this.provincias.find(p => p.nombre.toLowerCase() == this.selectedProvincia.toLowerCase());
                            if (prov) this.selectedProvincia = prov.id;
                        }
                        
                        this.updateDistritos();
                        
                        // Wait for districts to populate
                        this.$nextTick(() => {
                            if (this.selectedDistrito) {
                                // Try ID match
                                let dist = this.distritos_options.find(d => d.id == this.selectedDistrito);
                                if (!dist) {
                                    // Try Name match
                                    dist = this.distritos_options.find(d => d.nombre.toLowerCase() == this.selectedDistrito.toLowerCase());
                                    if (dist) this.selectedDistrito = dist.id;
                                }
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error loading locations:', error);
                }

                // Initialize Academic Data
                if (this.selectedFacultad) await this.loadEscuelas();
                if (this.selectedEscuela) await this.loadSecciones();
            },
            
            // Helper: Get Province Name for Display/DB
            getProvinciaName() {
                const prov = this.provincias.find(p => p.id == this.selectedProvincia);
                // If found, return name. If not (e.g. init loading), return the raw value from DB.
                return prov ? prov.nombre : '{{ $persona->provincia }}';
            },
            
            // Helper: Get District Name for Display/DB
            getDistritoName() {
                const dist = this.distritos_options.find(d => d.id == this.selectedDistrito);
                return dist ? dist.nombre : '{{ $persona->distrito }}';
            },

            updateDistritos() {
                this.distritos_options = this.all_distritos[this.selectedProvincia] || [];
            },

            // Academic Methods
            async loadEscuelas() {
                if (!this.selectedFacultad) { this.escuelas = []; return; }
                try {
                    const response = await fetch('/api/escuelas/' + this.selectedFacultad);
                    this.escuelas = await response.json();
                } catch (error) { console.error('Error loading escuelas:', error); }
            },

            async loadSecciones() {
                if (!this.selectedEscuela || !this.semestreId) { this.secciones = []; return; }
                try {
                    const response = await fetch('/api/secciones/' + this.selectedEscuela + '/' + this.semestreId);
                    this.secciones = await response.json();
                } catch (error) { console.error('Error loading secciones:', error); }
            },

            cancelEditing() {
                this.isEditing = false;
                window.location.reload();
            }
        }
    }
</script>
<style>
    html { scroll-behavior: smooth; }
</style>
@endpush
