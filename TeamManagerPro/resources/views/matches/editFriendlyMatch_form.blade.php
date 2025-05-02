@if($match->id)
<form id="edit-match-form-{{ $match->id }}" action="{{ route('matches.update',  ['id' => $match->id]) }}" method="POST">
    @csrf
    @method('PATCH')  
    <input type="hidden" name="tipo" value="amistoso">
    <input type="hidden" name="goles_a_favor" id="edit-goles-favor-{{ $match->id }}" value="{{ $match->goles_a_favor }}" min="0">
    <input type="hidden" name="goles_en_contra" id="edit-goles-contra-{{ $match->id }}" value="{{ $match->goles_en_contra }}" min="0">
    <input type="hidden" name="resultado" id="edit-resultado-{{ $match->id }}" value="{{ $match->resultado }}" min="0">
    <input type="hidden" name="actuacion_equipo" id="edit-actuacion-{{ $match->id }}" value="{{ $match->actuacion_equipo }}"  min="1" max="10" step="0.1">
    <input type="hidden" name="fecha_partido" id="edit-fecha-{{ $match->id }}" value="{{ $match->fecha_partido }}">
    <input type="hidden" name="team_id" value="{{ $team->id }}">
</form>
@endif