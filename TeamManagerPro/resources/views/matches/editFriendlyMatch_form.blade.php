<form id="edit-match-form-{{ $match->id }}" action="{{ route('matches.update', $match->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="goles_a_favor" id="edit-goles-favor-{{ $match->id }}" value="{{ $match->goles_a_favor }}">
    <input type="hidden" name="goles_en_contra" id="edit-goles-contra-{{ $match->id }}" value="{{ $match->goles_en_contra }}">
    <input type="hidden" name="resultado" id="edit-resultado-{{ $match->id }}" value="{{ $match->resultado }}">
    <input type="hidden" name="actuacion_equipo" id="edit-actuacion-{{ $match->id }}" value="{{ $match->actuacion_equipo }}">
</form>
