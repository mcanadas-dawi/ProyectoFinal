<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $matches = Matches::whereHas('team', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return view('matches.index', compact('matches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teams = Team::where('user_id', Auth::id())->get();
        return view('matches.create', compact('teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'numero_jornada' => 'required|integer',
            'equipo_rival' => 'required|string',
            'fecha_partido' => 'required|date',
        ]);

        Matches::create($request->all());

        return redirect()->route('matches.index')->with('success', 'Partido agregado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Matches $match)
    {
        return view('matches.show', compact('match'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('matches.edit', compact('match'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Matches $match)
    {
        $request->validate([
            'resultado' => 'nullable|in:Victoria,Empate,Derrota',
            'goles_a_favor' => 'nullable|integer',
            'goles_en_contra' => 'nullable|integer',
            'actuacion_equipo' => 'nullable|numeric|min:1|max:10',
        ]);

        $match->update($request->all());

        return redirect()->route('matches.index')->with('success', 'Partido actualizado.'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matches $match)
    {
        $match->delete();
        return redirect()->route('matches.index')->with('success', 'Partido eliminado.');
    }
}
