<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Recurso;
use App\Models\type_users;
use App\Models\Facultad;
use Illuminate\Support\Facades\Auth;

class TableResources extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'tailwind';
    
    // Listeners for events from other components (like upload form)
    protected $listeners = ['recursoGuardado' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteRecurso($id)
    {
        $user = Auth::user();
        if (!in_array($user->getRolId(), [1, 2])) {
            $this->emit('swal:error', 'No tienes permisos para eliminar recursos.');
            return;
        }

        $recurso = Recurso::findOrFail($id);
        $recurso->state = 0;
        $recurso->save();

        $this->emit('swal:success', 'Recurso eliminado correctamente.');
    }

    public function render()
    {
        $id_semestre = session('semestre_actual_id');
        $authUser = Auth::user();
        $ap = $authUser->persona->asignacion_persona;
        
        $mySa = $ap->seccion_academica;
        $myFacultadId = $mySa ? $mySa->id_facultad : null;
        $myEscuelaId = $mySa ? $mySa->id_escuela : null;
        $mySeccionId = $mySa ? $mySa->id : null;
        $myRolId = $authUser->getRolId();

        // Query Principal
        $query = Recurso::activo()
            ->with(['uploader.persona', 'seccionAcademica.escuela'])
            ->where('id_semestre', $id_semestre);
            
        // Búsqueda
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('tipo', 'like', '%' . $this->search . '%')
                  ->orWhereHas('uploader.persona', function($q2) {
                        $q2->where('nombres', 'like', '%' . $this->search . '%')
                           ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // 1. Filtrar por Rol Dirigido
        if (!in_array($myRolId, [1, 2])) {
            $query->where(function($q) use ($myRolId) {
                $q->whereNull('id_rol')
                  ->orWhere('id_rol', $myRolId);
            });
        }

        // 2. Filtrar por Nivel Jerárquico
        if ($myRolId == 1) {
            // Admin Global: Ve todo
        } elseif ($myRolId == 2) {
            // Sub Admin
            if ($myFacultadId) {
                 $query->where(function($q) use ($myFacultadId) {
                    $q->where('nivel', 1) 
                      ->orWhere(function($subQ) use ($myFacultadId) {
                          $subQ->where('nivel', 2)
                               ->whereHas('seccionAcademica', function($f) use ($myFacultadId) {
                                   $f->where('id_facultad', $myFacultadId);
                               });
                      })
                      ->orWhere(function($subQ) use ($myFacultadId) {
                          $subQ->whereIn('nivel', [3, 4])
                               ->whereHas('seccionAcademica', function($f) use ($myFacultadId) {
                                   $f->where('id_facultad', $myFacultadId);
                               });
                      });
                });
            }
        } else {
            // Otros roles
            $query->where(function($q) use ($myFacultadId, $myEscuelaId, $mySeccionId) {
                $q->where('nivel', 1); // Global
                
                if ($myFacultadId) {
                    $q->orWhere(function($sub) use ($myFacultadId) {
                        $sub->where('nivel', 2)
                            ->whereHas('seccionAcademica', function($f) use ($myFacultadId) {
                                $f->where('id_facultad', $myFacultadId);
                            });
                    });
                }
                
                if ($myEscuelaId) {
                    $q->orWhere(function($sub) use ($myEscuelaId) {
                        $sub->where('nivel', 3)
                            ->whereHas('seccionAcademica', function($e) use ($myEscuelaId) {
                                $e->where('id_escuela', $myEscuelaId);
                            });
                    });
                }
                
                if ($mySeccionId) {
                    $q->orWhere(function($sub) use ($mySeccionId) {
                        $sub->where('nivel', 4)
                            ->where('id_sa', $mySeccionId);
                    });
                }
            });
        }

        $recursos = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        $tipoLabels = [
            'otros' => 'Otros',
            'carga_lectiva' => 'Carga Lectiva',
            'horario' => 'Horario',
            'resolucion' => 'Resolución',
            'ficha' => 'Ficha',
            'record' => 'Record',
            'fut' => 'FUT',
            'carta_presentacion' => 'Carta de Presentación',
            'carta_aceptacion' => 'Carta de Aceptación',
            'plan_actividades_ppp' => 'Plan de Actividades PPP',
            'constancia_cumplimiento' => 'Constancia de Cumplimiento',
            'informe_final_ppp' => 'Informe Final PPP',
            'anexo_7' => 'Anexo 7',
            'anexo_8' => 'Anexo 8',
            'memrandum' => 'Memorándum',
            'oficio' => 'Oficio',
            'memorandum' => 'Memorándum',
        ];

        $nivelLabels = [
             1 => 'Global',
             2 => 'Facultad',
             3 => 'Escuela',
             4 => 'Sección'
        ];

        return view('livewire.table-resources', compact('recursos', 'tipoLabels', 'nivelLabels'));
    }
}
