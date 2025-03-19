<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;

class DashboardController extends Controller
{
    public function index()
    {
        $teams = Team::where('user_id', Auth::id())->with('players')->get();
        return view('dashboard.index', compact('teams'));
    }
}
