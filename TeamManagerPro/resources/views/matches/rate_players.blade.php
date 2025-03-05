@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold text-gray-900 mb-4">Valorar Jugadores del Partido</h2>
    
    <form action="{{ route('matches.saveRatings', $match->id) }}" method="POST">
        @csrf
        <table class="w-full border-collapse border border-gray-300 mb-4">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Jugador</th>
                    <th class="border p-2">Valoración</th>
                </tr>
            </thead>
            <tbody>
                @if (!empty($match->players) && $match->players->count() > 0)
                    @foreach ($match->players as $player)
                        <tr class="border">
                            <td class="p-2">{{ $player->nombre }} {{ $player->apellido }} (Dorsal: {{ $player->dorsal }})</td>
                            <td class="p-2 text-center">
                                <input type="number" name="ratings[{{ $player->id }}]" min="1" max="10" required 
                                       class="w-16 p-2 border rounded text-center"
                                       value="{{ $player->pivot->valoracion ?? '' }}">
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" class="p-4 text-center text-gray-500">No hay jugadores convocados para este partido.</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div class="flex justify-between">
            <button type="button" onclick="window.history.back();" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancelar</button>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg">Guardar Valoraciones</button>
        </div>
    </form>
</div>
@endsection
