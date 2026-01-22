@extends('template')

@section('title', 'Recursos')
@section('subtitle', 'Repositorio de Documentos y Plantillas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <x-header-content
        title="Recursos Disponibles"
        subtitle="En esta sección podrás encontrar documentos, plantillas y guías necesarias para el proceso de prácticas preprofesionales."
        icon="bi-cloud-arrow-up-fill"
        :enableButton="Auth::user()->hasAnyRoles([1, 2, 3, 4]) && !empty($tiposPermitidos)"
        :typeButton="2"
        msj="Subir Recurso"
        icon_msj="bi-cloud-upload"
        function="window.livewire.emit('openUploadModal')"
    />

    <livewire:table-resources />

    <livewire:upload-resource 
        :roles="$roles" 
        :facultades="$facultades" 
        :mapaTiposDestinatario="$mapaTiposDestinatario" 
        :tipoLabels="$tipoLabels" 
        :tiposPermitidos="$tiposPermitidos" 
    />
</div>
@endsection

@push('js')
{{-- No scripts needed here, all handled by Livewire and Global Listeners in template --}}
@endpush
