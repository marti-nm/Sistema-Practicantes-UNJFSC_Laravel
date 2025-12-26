<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RequirementsSearch extends Component
{
    public $search = '';
    
    public $requirements = [
        ['title' => 'Carga Lectiva', 'desc' => 'Documento que acredita las horas académicas.', 'stage' => 'Acreditación'],
        ['title' => 'Plan de Mejora', 'desc' => 'Propuesta técnica para la empresa.', 'stage' => 'Desarrollo'],
        ['title' => 'Informe Final', 'desc' => 'Consolidado de actividades realizadas.', 'stage' => 'Finalización'],
        ['title' => 'Ficha de Evaluación', 'desc' => 'Calificación del jefe inmediato.', 'stage' => 'Desarrollo'],
        ['title' => 'Carta de Aceptación', 'desc' => 'Documento firmado por la empresa receptora.', 'stage' => 'Acreditación'],
    ];

    public function render()
    {
        $filteredRequirements = collect($this->requirements)->filter(function ($req) {
            return empty($this->search) || 
                   str_contains(strtolower($req['title']), strtolower($this->search)) ||
                   str_contains(strtolower($req['stage']), strtolower($this->search));
        });

        return view('livewire.requirements-search', [
            'filtered' => $filteredRequirements
        ]);
    }
}
