<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::where('user_id', Auth::id())->get();
        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'modalidad' => 'required|in:F5,F7,F8,F11',
        ]);

        Team::create([
            'nombre' => $request->nombre,
            'modalidad' => $request->modalidad,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('teams.index')->with('success', 'Plantilla creada.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        if ($team->user_id !== Auth::id()) {
            abort(403);
        }
        return view('teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        if ($team->user_id !== Auth::id()) {
            abort(403);
        }
        return view('teams.edit', compact('team'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        if ($team->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nombre' => 'required|string',
            'modalidad' => 'required|in:F5,F7,F8,F11',
        ]);

        $team->update($request->all());

        return redirect()->route('teams.index')->with('success', 'Plantilla actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        if ($team->user_id !== Auth::id()) {
            abort(403);
        }

        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Plantilla eliminada.');
    }
}
