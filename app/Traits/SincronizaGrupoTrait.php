<?php

namespace App\Traits;

use App\Models\grupo_practica;
use App\Models\grupo_estudiante;
use App\Models\EvaluacionPractica;
use Illuminate\Support\Facades\Log;

trait SincronizaGrupoTrait
{
    /**
     * Sincroniza el id_modulo de un grupo basándose en el progreso individual
     * de todos los estudiantes asignados a ese grupo.
     *
     * @param int $grupoId
     * @return int El nuevo id_modulo del grupo
     */
    public function sincronizarModuloGrupo($grupoId)
    {
        $grupo = grupo_practica::find($grupoId);
        if (!$grupo) {
            Log::error("Sincronización fallida: No se encontró el grupo ID {$grupoId}");
            return 1;
        }

        // Obtener los IDs de asignacion_persona (estudiantes) del grupo
        $estudiantesIds = grupo_estudiante::where('id_gp', $grupoId)->pluck('id_estudiante');

        if ($estudiantesIds->isEmpty()) {
            $grupo->id_modulo = 1;
            $grupo->save();
            Log::info("Grupo {$grupoId} sin estudiantes. Módulo reseteado a 1.");
            return 1;
        }

        $modulosActuales = [];
        foreach ($estudiantesIds as $apId) {
            // Buscamos el primer módulo que NO esté aprobado para este estudiante.
            // Si tiene registros aprobados, su "módulo actual" es el siguiente.
            $primerModuloPendiente = EvaluacionPractica::where('id_ap', $apId)
                ->where('estado_evaluacion', '!=', 'Aprobado')
                ->orderBy('id_modulo', 'asc')
                ->value('id_modulo');

            // Si primerModuloPendiente es null, significa que ya aprobó TODO (los 4 módulos).
            // En ese caso, para el cálculo del mínimo del grupo, lo ponemos como 4 (el tope).
            $modulosActuales[] = $primerModuloPendiente ?? 4;
        }

        // La regla de oro: El grupo está en el nivel del estudiante más retrasado.
        // El módulo del grupo es el MÍNIMO de los módulos que están cursando sus integrantes.
        $minModulo = min($modulosActuales);

        $grupo->id_modulo = $minModulo;
        $grupo->save();

        Log::info("Sincronización de Grupo: El Grupo {$grupoId} ({$grupo->name}) se ha sincronizado al Módulo {$minModulo} basado en el progreso de sus " . count($estudiantesIds) . " estudiantes.");
        
        return $minModulo;
    }
}
