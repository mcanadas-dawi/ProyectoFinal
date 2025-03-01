@extends('layouts.app')

@section('title', 'Editar Plantilla')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Editar Plantilla</h1>
    <form action="{{ route('teams.update', $team) }}" method="POST" class="bg-white p-4 shadow rounded">
        @csrf @method('PUT')

        <label class="block">Nombre:</label>
        <input type="text" name="nombre" class="border p-2 w-full" value="{{ $team->nombre }}" required>

        <label class="block mt-2">Modalidad:</label>
        <select name="modalidad" class="border p-2 w-full">
            <option value="F5" {{ $team->modalidad == 'F5' ? 'selected' : '' }}>Fútbol 5</option>
            <option value="F7" {{ $team->modalidad == 'F7' ? 'selected' : '' }}>Fútbol 7</option>
            <option value="F8" {{ $team->modalidad == 'F8' ? 'selected' : '' }}>Fútbol 8</option>
            <option value="F11" {{ $team->modalidad == 'F11' ? 'selected' : '' }}>Fútbol 11</option>
        </select>

        <button type="submi
