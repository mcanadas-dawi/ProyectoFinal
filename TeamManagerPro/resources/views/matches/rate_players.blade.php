@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Valorar Jugadores del Partido</h2>

    <form action="{{ route('matches.saveRatings', $match->id) }}" method="POST">
        @csrf
        @foreach ($match->players as $player)
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold">
                    {{ $player->nombre }} {{ $player->apellido }} (Dorsal: {{ $player->dorsal }})
                </label>
                <input type="number" name="ratings[{{ $player->id }}]" min="1" max="10" required 
                       class="w-16 p-2 border rounded text-center">
            </div>
        @endforeach

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar Valoraciones</button>
    </form>
</div>
@endsection
