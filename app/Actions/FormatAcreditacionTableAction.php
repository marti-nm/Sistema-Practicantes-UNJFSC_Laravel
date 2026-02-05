<?php
namespace App\Actions;

class FormatAcreditacionTableAction
{
    public function execute($usuarios)
    {
        return $usuarios->map(
            fn($user) => [
                "id" => $user->asignacion_persona->id,
                "unique_id" => $user->asignacion_persona->id,
                "people" => "{$user->apellidos}, {$user->nombres}",
                "avatar" => strtoupper(
                    substr($user->nombres, 0, 1) .
                        substr($user->apellidos, 0, 1),
                ),
                "semestre" => $user->asignacion_persona->semestre->codigo ?? "",
                "escuela" =>
                    $user->asignacion_persona->seccion_academica->escuela
                        ->name ?? "",
                "facultad" =>
                    $user->asignacion_persona->seccion_academica->escuela
                        ->facultad->name ?? "",
                "seccion" =>
                    $user->asignacion_persona->seccion_academica->seccion ?? "",
                "archivos" => [
                    "carga_lectiva" => $this->ResolveFileStatus(
                        $user,
                        "carga_lectiva",
                    ),
                    "horario" => $this->ResolveFileStatus($user, "horario"),
                ],
            ],
        );
    }

    private function ResolveFileStatus($user, $tipo)
    {
        $acreditacion = $user->asignacion_persona->acreditacion->first();
        $file = $acreditacion
            ? $acreditacion->archivos->where("tipo", $tipo)->first()
            : null;

        return [
            "exists" => (bool) $file,
            "estado" => $file ? $file->estado_archivo : "Falta",
            "id" => $acreditacion->id ?? null,
            "type" => $tipo,
            "name" => "{$user->apellidos} {$user->nombres}",
        ];
    }
}
