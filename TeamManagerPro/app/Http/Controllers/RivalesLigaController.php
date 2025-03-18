<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RivalLiga;
use App\Models\Matches;

class RivalesLigaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'nombre_equipo' => 'required|string|unique:rivales_liga,nombre_equipo',
        'jornada' => 'required|integer|min:1',
        'team_id' => 'required|exists:teams,id', // Asegurar que el equipo existe
    ]);

    // Crear rival en la tabla rivales_liga
    $rival = RivalLiga::create([
        'nombre_equipo' => $request->nombre_equipo,
        'jornada' => $request->jornada,
    ]);

    // Crear automáticamente el partido de liga
    Matches::create([
        'team_id' => $request->team_id,
        'tipo' => 'liga',
        'rival_liga_id' => $rival->id,
        'equipo_rival' => $rival->nombre_equipo, // Opcional, solo para referencia
        'fecha_partido' => now()->addDays(7), // 📌 Por defecto, fecha en 7 días (se puede cambiar manualmente)
    ]);

    return redirect()->route('dashboard')->with('success', 'Calendario de Liga creado y partido añadido correctamente.');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
