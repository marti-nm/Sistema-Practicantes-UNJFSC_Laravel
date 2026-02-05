<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Facultad;
use Illuminate\Support\Collection;

class AcreditacionService
{
    public function getFacultadesPorRol($user): Collection
    {
        $ap_now = $user->persona->asignacion_persona;
        $query = Facultad::where("state", 1);

        if ($ap_now->id_rol == 2) {
            $query->where("id", $ap_now->id_facultad);
        }

        return $query->get();
    }

    public function getDocentesTitulares($filtros, $idSemestre): Collection
    {
        return Persona::whereHas("asignacion_persona", function ($query) use (
            $idSemestre,
            $filtros,
        ) {
            $query->where("id_semestre", $idSemestre)->where("id_rol", 3);

            if (!empty($filtros["facultad"])) {
                $query->whereHas(
                    "seccion_academica.escuela",
                    fn($q) => $q->where("id_facultad", $filtros["facultad"]),
                );
            }
            if (!empty($filtros["escuela"])) {
                $query->whereHas(
                    "seccion_academica",
                    fn($q) => $q->where("id_escuela", $filtros["escuela"]),
                );
            }
            if (!empty($filtros["seccion"])) {
                $query->whereHas(
                    "seccion_academica",
                    fn($q) => $q->where("id", $filtros["seccion"]),
                );
            }
        })
            ->with([
                "asignacion_persona" => fn($q) => $q->where(
                    "id_semestre",
                    $idSemestre,
                ),
                "asignacion_persona.semestre",
                "asignacion_persona.seccion_academica.escuela.facultad",
                "asignacion_persona.acreditacion.archivos" => fn(
                    $q,
                ) => $q->orderBy("created_at", "desc"),
            ])
            ->get();
    }
}
