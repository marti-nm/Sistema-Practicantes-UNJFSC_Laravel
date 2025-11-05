<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\Acreditar;
use App\Models\Archivo;
use Illuminate\Http\Request;
// Log
use Illuminate\Support\Facades\Log;

class ArchivoController extends Controller
{
    public function subirFicha(Request $request)
    {
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'ficha' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        // Guardar el archivo
        $nombre = 'ficha_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('ficha')->storeAs('fichas', $nombre, 'public');

        // Buscar o crear la matrícula
        $matricula = Matricula::firstOrNew(['persona_id' => $personaId]);

        $matricula->ruta_ficha = 'storage/' . $ruta;
        $matricula->estado_ficha = 'en proceso';

        if (!$matricula->exists) {
            $matricula->estado_record = null;
        }

        $matricula->save();

        return back()->with('success', 'Ficha de matrícula subida correctamente.');
    }

    public function subirRecord(Request $request)
    {
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
            'record' => 'required|file|mimes:pdf|max:20480',
        ]);

        $personaId = $request->persona_id;

        $nombre = 'record_' . $personaId . '_' . time() . '.pdf';
        $ruta = $request->file('record')->storeAs('records', $nombre, 'public');

        $matricula = Matricula::firstOrNew(['persona_id' => $personaId]);

        $matricula->ruta_record = 'storage/' . $ruta;
        $matricula->estado_record = 'en proceso';

        if (!$matricula->exists) {
            $matricula->estado_ficha = null;
        }

        $matricula->save();

        return back()->with('success', 'Récord académico subido correctamente.');
    }

    public function subirCLectiva(Request $request)
    {
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'carga_lectiva' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_ap = $request->ap_id;

        $acreditacion = Acreditar::firstOrCreate(
            ['ap_id' => $id_ap],
            [
                'estado_acreditacion' => 'Pendiente',
                'estado' => 1
            ]
        );

        $file = $request->file('carga_lectiva');
        $nombre = 'cl_' . $id_ap . '_' . time() . '.pdf';
        //$ruta = $request->file('carga_lectiva')->storeAs('carga_lectiva', $nombre, 'public');
        $ruta = $file->storeAs('cargas_lectivas', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $acreditacion->id,
            'archivo_type' => Acreditar::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'carga lectiva',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'estado' => 1
        ]);

        $acreditacion->save;

        return back()->with('success', 'Constancia de carga lectiva subida correctamente.');
    }

    public function subirHorario(Request $request)
    {
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'horario' => 'required|file|mimes:pdf|max:20480',
        ]);
        
        $id_ap = $request->ap_id;

        $acreditacion = Acreditar::firstOrCreate(
            ['ap_id' => $id_ap],
            [
                'estado_acreditacion' => 'Pendiente',
                'estado' => 1
            ]
        );

        $file = $request->file('horario');
        $nombre = 'horario_' . $id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('horarios', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $acreditacion->id,
            'archivo_type' => Acreditar::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'horario',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'estado' => 1
        ]);

        $acreditacion->save;
        return back()->with('success', 'Horario subida correctamente.');
    }

    public function subirResolucion(Request $request)
    {
        $request->validate([
            'ap_id' => 'required|exists:asignacion_persona,id',
            'resolucion' => 'required|file|mimes:pdf|max:20480',
        ]);

        $id_ap = $request->ap_id;

        $acreditacion = Acreditar::firstOrCreate(
            ['ap_id' => $id_ap],
            [
                'estado_acreditacion' => 'Pendiente',
                'estado' => 1
            ]
        );

        $file = $request->file('resolucion');
        $nombre = 'resolucion_' . $id_ap . '_' . time() . '.pdf';
        $ruta = $file->storeAs('resoluciones', $nombre, 'public');
        $rutaCompleta = 'storage/' . $ruta;

        Archivo::create([
            'archivo_id' => $acreditacion->id,
            'archivo_type' => Acreditar::class,
            'estado_archivo' => 'Enviado',
            'tipo' => 'resolucion',
            'ruta' => $rutaCompleta,
            'comentario' => null,
            'subido_por_user_id' => $id_ap,
            'estado' => 1
        ]);

        $acreditacion->save;
        return back()->with('success', 'Resolución subida correctamente.');
    }

    public function showPDF($documento)
    {
        if (!auth()->check()) {
            abort(403, 'No autorizado');
        }

        $path = storage_path('app/public/' . $documento);
        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
