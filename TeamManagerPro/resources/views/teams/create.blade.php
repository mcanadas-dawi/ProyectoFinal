@extends('layouts.app')

@section('title', 'Nueva Plantilla')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Nueva Plantilla</h1>
    <form action="{{ route('teams.store') }}" method="POST" class="bg-white p-4 shadow rounded">
        @csrf
        <label class="block">Nombre:</label>
        <input type="text" name="nombre" class="border p-2 w-full" required>

        <label class="block mt-2">Modalidad:</label>
        <select name="modalidad" class="border p-2 w-full">
            <option value="F5">Fútbol 5</option>
            <option value="F7">Fútbol 7</option>
            <option value="F8">Fútbol 8</option>
            <option value="F11">Fútbol 11</option>
        </select>

        <button type="submit" class="mt-4 bg-green-500 text-black px-4 py-2 rounded">Guardar</button>
    </form>
@endsection
