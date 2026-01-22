<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Recurso;
use App\Models\type_users;
use App\Models\Facultad;
use App\Models\Escuela;
use App\Models\seccion_academica;
use App\Models\asignacion_persona;
use Illuminate\Support\Facades\Auth;

class UploadResource extends Component
{
    use WithFileUploads;

    // Form Fields
    public $id_rol = '';
    public $facultad = '';
    public $escuela = '';
    public $seccion = '';
    public $nombre = '';
    public $tipo = '';
    public $archivo;
    public $descripcion = '';

    // UI State
    public $uploadModalOpen = false;
    public $escuelas = [];
    public $secciones = [];
    public $availableTypes = [];

    // Context Data
    public $roles = [];
    public $facultadesList = [];
    public $mapaTiposDestinatario = [];
    public $tipoLabels = [];
    public $tiposPermitidos = [];

    // Listeners
    protected $listeners = ['openUploadModal' => 'openModal'];

    public function mount($roles, $facultades, $mapaTiposDestinatario, $tipoLabels, $tiposPermitidos)
    {
        $this->roles = $roles;
        $this->facultadesList = $facultades;
        $this->mapaTiposDestinatario = $mapaTiposDestinatario;
        $this->tipoLabels = $tipoLabels;
        $this->tiposPermitidos = $tiposPermitidos;
        
        $this->availableTypes = $this->tiposPermitidos;
    }

    public function updatedIdRol($value)
    {
        if (!$value) {
            $this->availableTypes = $this->tiposPermitidos;
        } else {
            $tiposParaElDestinatario = $this->mapaTiposDestinatario[$value] ?? [];
            $this->availableTypes = array_filter($this->tiposPermitidos, function($t) use ($tiposParaElDestinatario) {
                return in_array($t, $tiposParaElDestinatario);
            });
        }
        $this->tipo = '';
    }

    public function updatedFacultad($value)
    {
        $this->escuela = '';
        $this->seccion = '';
        $this->secciones = [];
        if ($value) {
            $this->escuelas = Escuela::where('facultad_id', $value)->where('state', 1)->get()->toArray();
        } else {
            $this->escuelas = [];
        }
    }

    public function updatedEscuela($value)
    {
        $this->seccion = '';
        if ($value) {
            $id_semestre = session('semestre_actual_id');
            $this->secciones = seccion_academica::where('id_escuela', $value)
                ->where('id_semestre', $id_semestre)
                ->where('state', 1)
                ->get()
                ->toArray();
        } else {
            $this->secciones = [];
        }
    }

    public function openModal()
    {
        $this->resetForm();
        $this->uploadModalOpen = true;
    }

    public function closeModal()
    {
        $this->uploadModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['id_rol', 'facultad', 'escuela', 'seccion', 'nombre', 'tipo', 'archivo', 'descripcion', 'escuelas', 'secciones']);
        $this->availableTypes = $this->tiposPermitidos;
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required',
            'archivo' => 'required|max:20480', // 20MB
            'id_rol' => 'nullable',
            'facultad' => 'nullable',
            'escuela' => 'nullable',
            'seccion' => 'nullable',
        ]);

        $id_semestre = session('semestre_actual_id');
        $user = Auth::user();

        // Nivel y ID_SA logic
        $nivel = 1;
        $id_sa_referencial = null;

        if ($this->seccion) {
            $nivel = 4;
            $id_sa_referencial = $this->seccion;
        } elseif ($this->escuela) {
            $nivel = 3;
            $sa = seccion_academica::where('id_escuela', $this->escuela)
                ->where('id_semestre', $id_semestre)
                ->first();
            $id_sa_referencial = $sa ? $sa->id : null;
        } elseif ($this->facultad) {
            $nivel = 2;
            $sa = seccion_academica::where('id_facultad', $this->facultad)
                ->where('id_semestre', $id_semestre)
                ->first();
            $id_sa_referencial = $sa ? $sa->id : null;
        }

        if (($nivel == 2 || $nivel == 3) && !$id_sa_referencial) {
            $this->addError('facultad', 'No se encontraron secciones académicas activas para la facultad/escuela seleccionada en este semestre.');
            return;
        }

        $ap = asignacion_persona::where('id_persona', $user->persona->id)
            ->where('id_semestre', $id_semestre)
            ->first();

        if (!$ap) {
            session()->flash('error', 'No se encontró tu asignación para este semestre.');
            return;
        }

        // Store File
        $nombreFile = 'recurso_' . $this->tipo . '_' . time() . '.' . $this->archivo->getClientOriginalExtension();
        $ruta = $this->archivo->storeAs('recursos', $nombreFile, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        // Create Resource
        Recurso::create([
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'ruta' => $rutaCompleta,
            'descripcion' => $this->descripcion,
            'subido_por_ap' => $ap->id,
            'id_sa' => $id_sa_referencial,
            'nivel' => $nivel,
            'id_semestre' => $id_semestre,
            'id_rol' => $this->id_rol ?: null,
            'state' => 1
        ]);

        $this->emit('recursoGuardado');
        $this->closeModal();
        $this->emit('swal:success', 'Recurso subido correctamente.');
    }

    public function render()
    {
        return view('livewire.upload-resource');
    }
}
