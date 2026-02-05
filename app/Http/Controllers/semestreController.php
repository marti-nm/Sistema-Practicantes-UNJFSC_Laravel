<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreSemestreRequest;
use App\Http\Requests\UpdateSemestreRequest;
use App\Models\Semestre;
use Illuminate\Support\Facades\DB;
use Exception;

class semestreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $semestres = Semestre::orderBy("id", "desc")->get();
        return view("semestre.index", compact("semestres"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("semestre.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSemestreRequest $request)
    {
        try {
            DB::beginTransaction();

            Semestre::create([
                "codigo" => $request->codigo,
                "ciclo" => $request->ciclo,
                "date_create" => now(),
                "state" => 2,
            ]);

            DB::commit();

            return redirect()
                ->route("semestre.index")
                ->with("success", "Semestre registrado correctamente.");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(
                "Error al guardar el semestre: " . $e->getMessage(),
            );
        }

        return redirect()
            ->route("semestre.index")
            ->with("success", "Semestre registrado correctamente.");
    }

    public function finalizar($id)
    {
        try {
            DB::beginTransaction();

            // Obtener el semestre actual antes de actualizarlo
            $semestreActual = Semestre::findOrFail($id);

            // Finalizar el semestre actual
            $semestreActual->update([
                "state" => 0,
                "date_update" => now(),
            ]);

            // Generar el código para el nuevo semestre
            $nuevoCodigo = $this->generarSiguienteCodigo(
                $semestreActual->codigo,
            );

            // Crear un nuevo semestre
            $semestreNuevo = Semestre::create([
                "codigo" => $nuevoCodigo,
                "ciclo" => $semestreActual->ciclo, // Mantener el mismo ciclo o definir uno por defecto
                "date_create" => now(),
                "state" => 1,
            ]);

            // Actualizar la session del nuevo semestre
            session(["semestre_actual_id" => $semestreNuevo->id]);

            // Actualizar o sea crear una nueva la asignacion persona del admin
            /*AsignacionPersona::create([
                'id_semestre' => $semestreNuevo->id,
                'id_persona' => 1,
                'id_rol' => 1,
                'date_create' => now(),
                'state' => 1
            ]);*/

            // Confirmar la transacción
            DB::commit();

            return redirect()
                ->route("semestre.index")
                ->with(
                    "success",
                    "Semestre finalizado correctamente. Nuevo semestre iniciado: " .
                        $nuevoCodigo,
                );
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(
                "Error al finalizar el semestre: " . $e->getMessage(),
            );
        }
    }

    private function generarSiguienteCodigo($codigoActual)
    {
        // Se asume formato "YYYY-1" o "YYYY-2"
        $partes = explode("-", $codigoActual);

        if (count($partes) != 2) {
            // Si el formato no es válido, retornamos un fallback o lanzamos error.
            // Por seguridad, incrementamos solo el año si no se puede parsear
            return (int) date("Y") + 1 . "-1";
        }

        $anio = (int) $partes[0];
        $numero = (int) $partes[1];

        if ($numero == 1) {
            return $anio . "-2";
        } else {
            return $anio + 1 . "-1";
        }
    }

    public function retroceder($id)
    {
        try {
            DB::beginTransaction();

            $semestreActual = Semestre::findOrFail($id);

            // Verificar si es el semestre activo
            if ($semestreActual->state != 1) {
                return back()->with(
                    "error",
                    "Solo se puede retroceder desde un semestre activo.",
                );
            }

            // Verificar que exista al menos un semestre anterior
            if (Semestre::count() <= 1) {
                return back()->with(
                    "error",
                    "No se puede retroceder. No existen semestres anteriores.",
                );
            }

            // Verificar dependencias
            $tieneDependencias = \App\Models\asignacion_persona::where(
                "id_semestre",
                $id,
            )->exists();
            // Puedes agregar más verificaciones aquí si existen otros modelos relacionados directamente

            if ($tieneDependencias) {
                return back()->with(
                    "error",
                    "No se puede retroceder. Existen registros (asignaciones) vinculados a este semestre. Debe eliminarlos primero.",
                );
            }

            // Eliminar semestre actual
            $semestreActual->delete();

            // Buscar el último semestre finalizado para reactivarlo
            // Asumimos que el ID más alto con state 0 es el anterior inmediato
            $semestreAnterior = Semestre::where("state", 0)
                ->orderBy("id", "desc")
                ->first();

            if ($semestreAnterior) {
                $semestreAnterior->update([
                    "state" => 1,
                    "date_update" => now(),
                ]);

                // Actualizar sesión
                session(["semestre_actual_id" => $semestreAnterior->id]);
                $mensaje =
                    "Se ha retrocedido correctamente. Semestre activo: " .
                    $semestreAnterior->codigo;
            } else {
                // Si no hay anterior, quizás se queda sin activo o se maneja distinto.
                // En este caso, limpiamos la sesión o informamos.
                session()->forget("semestre_actual_id");
                $mensaje =
                    "Se ha eliminado el semestre actual. No se encontró un semestre anterior para activar.";
            }

            DB::commit();

            return redirect()
                ->route("semestre.index")
                ->with("success", $mensaje);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(
                "Error al retroceder el semestre: " . $e->getMessage(),
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $semestre = Semestre::findOrFail($id); // Buscas el semestre por ID
        return view("semestre.edit", ["semestre" => $semestre]); // Pasas la variable correcta
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSemestreRequest $request, $id)
    {
        try {
            $semestre = Semestre::findOrFail($id); // Asegura que existe

            $semestre->update([
                "codigo" => $request->codigo,
                "ciclo" => $request->ciclo,
                "date_update" => now(),
            ]);

            return redirect()
                ->route("semestre.index")
                ->with("success", "Semestre actualizado correctamente.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with(
                    "error",
                    "Error al actualizar el semestre: " . $e->getMessage(),
                );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $semestre = Semestre::findOrFail($id);
        $semestre->delete();

        return redirect()
            ->route("semestre.index")
            ->with("success", "Semestre eliminado correctamente.");
    }

    public function setActive($id)
    {
        try {
            // validar que el semestre exista
            $semestre = Semestre::findOrFail($id);

            // actualizar el semestre en la session
            session(["semestre_actual_id" => $semestre->id]);

            // Retornar una respuesta JSON exitosa
            return response()->json([
                "message" => "Semestre actual establecido correctamente.",
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si el semestre no se encuentra, retornar un error 404
            return response()->json(
                ["error" => "El semestre no fue encontrado."],
                404,
            );
        } catch (\Exception $e) {
            // Para cualquier otro error, retornar un error 500
            return response()->json(
                ["error" => "Ocurrió un error al actualizar el semestre."],
                500,
            );
        }
    }
}
