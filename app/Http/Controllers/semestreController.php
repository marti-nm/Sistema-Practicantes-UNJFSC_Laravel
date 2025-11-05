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
        $semestres = Semestre::orderBy('id', 'desc')->get();
        return view('semestre.index', compact('semestres'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('semestre.create');
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
                'codigo' => $request->codigo,
                'ciclo' => $request->ciclo,
                'date_create' => now(),
                'estado' => true
            ]);

            DB::commit();

            return redirect()->route('semestre.index')
                            ->with('success', 'Semestre registrado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al guardar el semestre: ' . $e->getMessage());
        }
        

        return redirect()->route('semestre.index')->with('success', 'Semestre registrado correctamente.');

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
        return view('semestre.edit', ['semestre' => $semestre]); // Pasas la variable correcta
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
                'codigo' => $request->codigo,
                'ciclo' => $request->ciclo,
                'date_update' => now(),
            ]);

            return redirect()->route('semestre.index')
                            ->with('success', 'Semestre actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Error al actualizar el semestre: ' . $e->getMessage());
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

        return redirect()->route('semestre.index')->with('success', 'Semestre eliminado correctamente.');
  
    }

    public function setActive($id)
    {
        // validar que el semestre exista
        $semestre = Semestre::findOrFail($id);

        // actualizar el semestre en la session
        session(['semestre_actual_id' => $semestre->id]);

        return redirect()->route('semestre.index')->with('success', 'Semestre actual establecido correctamente.');
    }
}
