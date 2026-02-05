@extends('template')

@section('title', 'Expediente de Matrícula')
@section('subtitle', 'Gestión de documentos para prácticas pre-profesionales')

@section('content')
<div class="h-[calc(100vh-120px)] flex flex-col px-4 sm:px-6 overflow-hidden" 
     x-data="matriculaManager()" 
     x-init="init()">
    
    <!-- HEADER INTEGRADO (COMPACTO Y PLANO) -->
{{--<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 shrink-0 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/10">
                <i class="bi bi-patch-check-fill text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black text-slate-800 dark:text-white tracking-tight leading-none uppercase">Expediente de Matrícula</h2>
                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Digitalización de Documentos Oficiales</p>
            </div>
        </div>
    </div>--}}

    <!-- MAIN GRID -->
    <div class="flex flex-1 overflow-hidden gap-4 py-4 min-h-0 relative">
        
        <!-- SIDEBAR IZQUIERDO: SELECCIÓN -->
        <aside class="w-72 hidden lg:flex flex-col gap-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl p-4 shrink-0 h-full min-h-0">
            <div class="flex flex-col h-full">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 pl-1">Documentos Requeridos</h3>
                
                <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-1 min-h-0">
                    <!-- Ficha Item -->
                    <button @click="selectDocument('ficha')" 
                            class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200 group relative"
                            :class="selectedType === 'ficha' ? 'border-blue-600 bg-blue-50/20 dark:bg-blue-900/10' : 'border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors shadow-sm shrink-0"
                                :class="selectedType === 'ficha' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 group-hover:text-slate-600'">
                                <i class="bi bi-file-earmark-text-fill text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-black uppercase tracking-tight truncate" :class="selectedType === 'ficha' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-700 dark:text-slate-300'">Ficha Matrícula</p>
                                <span class="text-[9px] font-bold uppercase" 
                                    :class="getStatusTextColor(archivos.find(a => a.tipo === 'ficha')?.estado_archivo)"
                                    x-text="archivos.find(a => a.tipo === 'ficha')?.estado_archivo || 'Pendiente'"></span>
                            </div>
                        </div>
                        <div x-show="selectedType === 'ficha'" class="absolute right-3 top-1/2 -translate-y-1/2 w-1 h-6 bg-blue-600 rounded-full"></div>
                    </button>

                    <!-- Record Item -->
                    <button @click="selectDocument('record')" 
                            class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200 group relative"
                            :class="selectedType === 'record' ? 'border-blue-600 bg-blue-50/20 dark:bg-blue-900/10' : 'border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/50'">
                        <div class="flex items-center gap-3 relative z-10">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors shadow-sm shrink-0"
                                :class="selectedType === 'record' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 group-hover:text-slate-600'">
                                <i class="bi bi-award-fill text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] font-black uppercase tracking-tight truncate" :class="selectedType === 'record' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-700 dark:text-slate-300'">Récord Notas</p>
                                <span class="text-[9px] font-bold uppercase" 
                                    :class="getStatusTextColor(archivos.find(a => a.tipo === 'record')?.estado_archivo)"
                                    x-text="archivos.find(a => a.tipo === 'record')?.estado_archivo || 'Pendiente'"></span>
                            </div>
                        </div>
                        <div x-show="selectedType === 'record'" class="absolute right-3 top-1/2 -translate-y-1/2 w-1 h-6 bg-blue-600 rounded-full"></div>
                    </button>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-50 dark:border-white/5">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-900/20">
                        <div class="flex gap-3">
                            <i class="bi bi-info-circle-fill text-blue-600 mt-0.5"></i>
                            <p class="text-[10px] text-blue-700 dark:text-blue-400 font-medium leading-relaxed">
                                Los archivos deben estar en formato <span class="font-black">PDF</span> y no exceder los <span class="font-black">10MB</span> de peso.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <input type="file" x-ref="fileInput" class="hidden" @change="handleFileSelect" accept=".pdf">
        </aside>

        <!-- ÁREA CENTRAL: VISUALIZADOR PDF -->
        <main class="flex-1 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden flex flex-col relative min-h-0"
             @dragover.prevent="dragOver = true"
             @dragleave.prevent="dragOver = false"
             @drop.prevent="handleDrop">
            
            <!-- OVERLAY DE CARGA -->
            <div x-show="verifying || loading" x-transition.opacity
                class="absolute inset-0 z-[60] flex flex-col items-center justify-center bg-blue-600/90 backdrop-blur-sm transition-all text-white">
                <div class="mb-4">
                    <i class="bi bi-rocket-takeoff-fill text-5xl animate-bounce"></i>
                </div>
                <h4 class="text-xl font-black uppercase tracking-widest mb-1" x-text="verifying ? 'Verificando...' : 'Sincronizando...'"></h4>
                <p class="text-[10px] font-bold opacity-75 uppercase tracking-[0.3em] animate-pulse">Procesando archivo digital</p>
            </div>

            <!-- DROPZONE INDICATOR -->
            <div x-show="dragOver" x-transition.opacity
                class="absolute inset-0 z-40 bg-blue-600/10 pointer-events-none border-4 border-dashed border-blue-500/50 m-4 rounded-xl flex items-center justify-center backdrop-blur-[2px]">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                        <i class="bi bi-cloud-arrow-up-fill text-3xl text-blue-600"></i>
                    </div>
                    <p class="text-sm font-black text-blue-600 uppercase tracking-widest">¡Suelta para cargar!</p>
                </div>
            </div>

            <!-- VIEWER AREA -->
            <div class="flex-1 flex flex-col relative min-h-0 h-full">
                <!-- PDF IFRAME -->
                <template x-if="filePreviewUrl">
                    <iframe :src="filePreviewUrl" class="w-full h-full border-none bg-slate-50 dark:bg-slate-950 flex-1 min-h-0 rounded-xl"></iframe>
                </template>

                <!-- EMPTY / WELCOME STATE -->
                <template x-if="!filePreviewUrl && !verifying && !loading">
                    <div class="flex-1 flex flex-col items-center justify-center p-12 text-center animate-fade-in min-h-0 cursor-pointer"
                         @click="$refs.fileInput.click()">
                        <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] flex items-center justify-center mx-auto border-2 border-slate-100 dark:border-slate-800 group hover:border-blue-300 transition-all">
                            <i class="bi bi-file-earmark-arrow-up text-4xl text-slate-200 group-hover:text-blue-400 transition-colors"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-800 dark:text-white mt-6 mb-2 uppercase">Subir Expediente</h4>
                        <p class="text-xs text-slate-400 font-medium italic mb-2 px-12">Arrastra tu archivo aquí o haz clic para buscarlo en tu equipo.</p>
                        <div class="flex items-center justify-center gap-4 mt-8 opacity-40">
                            <i class="bi bi-shield-check text-2xl"></i>
                            <i class="bi bi-file-earmark-pdf text-2xl"></i>
                        </div>
                    </div>
                </template>
            </div>
        </main>

        <!-- PANEL DERECHO: INFO & ACCIONES -->
        <aside class="w-80 hidden xl:flex flex-col bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden shrink-0 h-full min-h-0 transition-all duration-300">
            <template x-if="selectedType">
                <div class="flex flex-col h-full min-h-0 animate-fade-in">
                    <!-- Tabs -->
                    <div class="flex border-b border-slate-50 dark:border-white/5 shrink-0">
                        <button @click="docViewMode = 'info'; saveState()" 
                                class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest transition-all relative"
                                :class="docViewMode === 'info' ? 'text-blue-600 bg-blue-50/10' : 'text-slate-400 hover:text-slate-600'">
                            Información
                            <div x-show="docViewMode === 'info'" class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600"></div>
                        </button>
                        <button @click="docViewMode = 'history'; saveState()" 
                                class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest transition-all relative"
                                :class="docViewMode === 'history' ? 'text-blue-600 bg-blue-50/10' : 'text-slate-400 hover:text-slate-600'">
                            Historial
                            <div x-show="docViewMode === 'history'" class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600"></div>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar p-5 space-y-6 min-h-0">
                        <!-- Info Mode -->
                        <div x-show="docViewMode === 'info'" class="space-y-6 flex flex-col h-full">
                            
                            <!-- ACCIÓN PRINCIPAL: ENVIAR (SECCIÓN DERECHA) -->
                            <template x-if="tempFile">
                                <div class="space-y-4 animate-bounce-in shrink-0">
                                    <div class="p-5 bg-blue-600 text-white rounded-xl shadow-xl shadow-blue-500/20 relative overflow-hidden group">
                                        <div class="relative z-10">
                                            <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-1 opacity-70">Documento Listo</p>
                                            <p class="text-xs font-black truncate mb-4" x-text="tempFileName"></p>
                                            
                                            <div class="flex items-center gap-2">
                                                <button @click="uploadFile()" 
                                                        class="flex-1 py-2.5 bg-white text-blue-600 text-[10px] font-black uppercase rounded-lg hover:bg-slate-50 transition-all active:scale-95 shadow-lg">
                                                    Enviar Ahora
                                                </button>
                                                <button @click="clearTempFile()" 
                                                        class="p-2.5 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors"
                                                        title="Borrar archivo seleccionado">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="absolute -right-4 -bottom-4 opacity-10 rotate-12 transition-transform group-hover:scale-110">
                                            <i class="bi bi-send-fill text-7xl"></i>
                                        </div>
                                    </div>
                                    <div class="px-2">
                                        <p class="text-[10px] font-bold text-slate-400 text-center italic leading-relaxed">
                                            Haz clic en enviar para registrar esta versión en el sistema y notificar al docente.
                                        </p>
                                    </div>
                                </div>
                            </template>

                            <!-- RESUMEN DEL ESTADO ACTUAL (CUANDO NO HAY TEMP) -->
                            <div x-show="!tempFile" class="space-y-6">
                                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-white/5">
                                    <div class="flex items-center justify-between mb-4">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Status</p>
                                        <span class="text-[9px] font-black px-2 py-0.5 rounded uppercase" 
                                              :class="getStatusBadgeClass(latestArchivo?.estado_archivo)"
                                              x-text="latestArchivo ? latestArchivo.estado_archivo : 'Pendiente'"></span>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center text-[10px]">
                                            <span class="font-bold text-slate-400">Tipo:</span>
                                            <span class="font-black text-slate-700 dark:text-slate-200 uppercase" x-text="selectedType === 'ficha' ? 'Ficha' : 'Récord'"></span>
                                        </div>
                                        <template x-if="latestArchivo">
                                            <div class="flex justify-between items-center text-[10px]">
                                                <span class="font-bold text-slate-400">Actualizado:</span>
                                                <span class="font-black text-slate-700 dark:text-slate-200" x-text="new Date(latestArchivo.updated_at).toLocaleDateString()"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- OBSERVACIÓN DE CORRECCIÓN -->
                                <template x-if="latestArchivo?.estado_archivo === 'Corregir'">
                                    <div class="p-5 bg-rose-50 dark:bg-rose-950/20 border-l-4 border-rose-500 rounded-xl space-y-4 animate-fade-in">
                                        <div class="flex items-center gap-2 text-rose-600">
                                            <i class="bi bi-chat-left-text-fill text-sm"></i>
                                            <p class="text-[10px] font-black uppercase tracking-widest">Observación</p>
                                        </div>
                                        <p class="text-[11px] font-bold text-rose-800 dark:text-rose-300 italic leading-relaxed" x-text="latestArchivo.comentario"></p>
                                        
                                        <button @click="$refs.fileInput.click()" 
                                                class="w-full py-3 bg-white border-2 border-rose-500 text-rose-600 text-[10px] font-black uppercase rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-sm flex items-center justify-center gap-2">
                                            <i class="bi bi-arrow-repeat"></i> Reemplazar Ahora
                                        </button>
                                    </div>
                                </template>

                                <!-- ESTADO APROBADO -->
                                <template x-if="latestArchivo?.estado_archivo === 'Aprobado'">
                                    <div class="p-6 text-center bg-emerald-50 dark:bg-emerald-950/20 rounded-xl border border-emerald-100 dark:border-emerald-800/30">
                                        <div class="w-12 h-12 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-100 shadow-sm">
                                            <i class="bi bi-check-all text-2xl text-emerald-500"></i>
                                        </div>
                                        <h5 class="text-[11px] font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-[0.2em] mb-1">Validado</h5>
                                        <p class="text-[9px] font-medium text-emerald-600/80">Este requisito ha sido aprobado.</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- History Mode -->
                        <div x-show="docViewMode === 'history'" class="space-y-2">
                            <template x-for="(item, index) in currentArchivos" :key="item.id">
                                <button @click="filePreviewUrl = '/' + item.ruta; tempFile = null" 
                                    class="w-full text-left p-4 bg-white dark:bg-slate-900 rounded-xl border-2 transition-all duration-300 group/item"
                                    :class="filePreviewUrl === '/' + item.ruta ? 'border-blue-600 bg-blue-50/10' : 'border-transparent hover:border-slate-50'">
                                     <div class="flex items-center justify-between">
                                         <div>
                                            <p class="text-[11px] font-black text-slate-800 dark:text-slate-200 mb-1" x-text="`Versión #${currentArchivos.length - index}`"></p>
                                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest" x-text="new Date(item.created_at).toLocaleString()"></p>
                                         </div>
                                         <i class="bi bi-chevron-right text-slate-300 group-hover/item:translate-x-1 transition-transform"></i>
                                     </div>
                                </button>
                            </template>
                            <template x-if="currentArchivos.length === 0">
                                <div class="py-12 text-center">
                                    <i class="bi bi-clock-history text-4xl text-slate-100 dark:text-slate-800 block mb-3"></i>
                                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest font-black">Sin Envíos previos</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </aside>
    </div>
</div>

<script>
function matriculaManager() {
    return {
        selectedType: null,
        loading: false,
        verifying: false,
        dragOver: false,
        filePreviewUrl: null,
        tempFile: null,
        tempFileName: null,
        tempFileSize: null,
        docViewMode: 'info',
        matricula: @json($matricula),
        storageKey: 'matricula_nav',

        init() {
            this.restoreState();
            if (!this.selectedType) {
                this.selectDocument('ficha');
            }
        },

        saveState() {
            localStorage.setItem(this.storageKey, JSON.stringify({
                selectedType: this.selectedType,
                docViewMode: this.docViewMode
            }));
        },

        restoreState() {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    this.selectedType = data.selectedType;
                    this.docViewMode = data.docViewMode || 'info';
                    
                    if (this.selectedType) {
                        this.$nextTick(() => {
                            const found = this.archivos.find(a => a.tipo === this.selectedType);
                            if (found) this.filePreviewUrl = '/' + found.ruta;
                        });
                    }
                } catch (e) {
                    console.error('Error restoring state', e);
                }
            }
        },
        
        get archivos() {
            return this.matricula.archivos || [];
        },

        get currentArchivos() {
            if (!this.selectedType) return [];
            return this.archivos.filter(a => a.tipo === this.selectedType).sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        },

        get latestArchivo() {
            return this.currentArchivos[0] || null;
        },

        selectDocument(type) {
            this.selectedType = type;
            this.docViewMode = 'info';
            this.tempFile = null;
            this.tempFileName = null;
            
            if (this.latestArchivo) {
                this.filePreviewUrl = '/' + this.latestArchivo.ruta;
            } else {
                this.filePreviewUrl = null;
            }
            this.saveState();
        },

        handleDrop(e) {
            this.dragOver = false;
            const file = e.dataTransfer.files[0];
            this.processFile(file);
        },

        handleFileSelect(e) {
            const file = e.target.files[0];
            this.processFile(file);
        },

        async processFile(file) {
            if (!file) return;

            this.verifying = true;
            await new Promise(resolve => setTimeout(resolve, 800));

            if (file.type !== 'application/pdf') {
                this.verifying = false;
                Swal.fire({
                    title: '<span class="text-rose-600 font-black uppercase tracking-tight">Formato Inválido</span>',
                    html: '<p class="text-xs font-bold text-slate-500">Solo se aceptan archivos <span class="text-rose-600 font-bold underline">PDF</span>.</p>',
                    icon: 'error',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            
            if (file.size > 10 * 1024 * 1024) {
                this.verifying = false;
                Swal.fire({
                    title: '<span class="text-rose-600 font-black uppercase tracking-tight">Archivo excedido</span>',
                    html: '<p class="text-xs font-bold text-slate-500">El peso máximo permitido es de <span class="text-rose-600 font-bold">10MB</span>.</p>',
                    icon: 'error',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            this.tempFile = file;
            this.tempFileName = file.name;
            this.tempFileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.filePreviewUrl = e.target.result;
            };
            reader.readAsDataURL(file);
            
            this.verifying = false;
        },

        clearTempFile() {
            this.tempFile = null;
            this.tempFileName = null;
            this.filePreviewUrl = this.latestArchivo ? '/' + this.latestArchivo.ruta : null;
        },

        async uploadFile() {
            if (!this.tempFile) return;
            
            this.loading = true;
            
            const formData = new FormData();
            formData.append(this.selectedType, this.tempFile);
            formData.append('ap_id', this.matricula.id_ap);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                let route = '';
                if (this.selectedType === 'ficha') route = '{{ route('subir.ficha') }}';
                else if (this.selectedType === 'record') route = '{{ route('subir.record') }}';

                const response = await fetch(route, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.ok) {
                    Swal.fire({
                        title: '¡Expediente Enviado!',
                        html: '<p class="text-xs font-bold text-slate-500 mt-2">Su documento ha sido sincronizado correctamente y está pendiente de revisión.</p>',
                        icon: 'success',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    const error = await response.json();
                    Swal.fire('Error', error.message || 'No se pudo procesar el archivo', 'error');
                }
            } catch (e) {
                Swal.fire('Error de Sistema', 'No se pudo contactar con el servidor. Reintente.', 'error');
            } finally {
                this.loading = false;
            }
        },

        getStatusBadgeClass(status) {
            return {
                'bg-emerald-100 text-emerald-700': status === 'Aprobado',
                'bg-amber-100 text-amber-700': status === 'Enviado',
                'bg-rose-100 text-rose-700': status === 'Corregir',
                'bg-slate-100 text-slate-500': !status
            };
        },

        getStatusTextColor(status) {
            if (status === 'Aprobado') return 'text-emerald-500 font-bold';
            if (status === 'Enviado') return 'text-amber-500 font-bold';
            if (status === 'Corregir') return 'text-rose-500 font-black animate-pulse';
            return 'text-slate-400 font-medium';
        },

        getStatusDotClass(status) {
            if (status === 'Aprobado') return 'bg-emerald-500';
            if (status === 'Enviado') return 'bg-amber-500';
            if (status === 'Corregir') return 'bg-rose-500 animate-pulse';
            return 'bg-slate-300';
        }
    };
}
</script>

<style>
    [x-cloak] { display: none !important; }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-bounce-in {
        animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes bounceIn {
        0% { transform: scale(0.95); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endsection
