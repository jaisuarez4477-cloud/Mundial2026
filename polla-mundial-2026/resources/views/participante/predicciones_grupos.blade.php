@extends('layouts.app')
@section('title','Predicciones de Grupos')
@section('page-title','Predicciones de Grupos')

@section('content')
<div class="card" style="margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px">
    <i class="ti ti-list-numbers" style="font-size:22px;color:var(--gold)"></i>
    <div>
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">Posiciones finales por grupo</h2>
      <p style="font-size:13px;color:var(--muted)">
        Asigna la posición final (1° a 4°) que crees tendrá cada equipo. Aciertas <strong style="color:var(--gold)">10 pts</strong> por cada posición correcta.
        No puedes repetir posiciones dentro de un mismo grupo.
      </p>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px">
  @forelse($grupos as $grupo)
  @php $predGrupo = $misPred[$grupo->id] ?? collect(); @endphp
  <div class="card">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
      <div style="width:36px;height:36px;border-radius:8px;background:rgba(200,146,42,.15);border:1px solid rgba(200,146,42,.3);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--gold)">
        {{ $grupo->nombre }}
      </div>
      <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px">Grupo {{ $grupo->nombre }}</div>
    </div>

    @if($grupo->equipos->count() === 0)
      <p style="color:var(--muted);font-size:12px;text-align:center;padding:14px 0">Este grupo aún no tiene equipos asignados.</p>
    @else
    <form action="{{ route('predicciones.grupos.guardar') }}" method="POST">
      @csrf
      <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
      <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px">
        @foreach($grupo->equipos as $eq)
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:7px 10px;background:rgba(255,255,255,.04);border-radius:7px;border:1px solid rgba(255,255,255,.06)">
          <div style="display:flex;align-items:center;gap:8px;min-width:0">
            @if($eq->bandera_url)
              <img src="{{ $eq->bandera_url }}" alt="" class="bandera">
            @else
              <span class="bandera-placeholder"></span>
            @endif
            <span style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $eq->nombre }}</span>
          </div>
          <select name="posiciones[{{ $eq->id }}]" class="form-input-dark" style="width:90px;flex-shrink:0;padding:6px 8px">
            <option value="">—</option>
            @for($pos = 1; $pos <= 4; $pos++)
            <option value="{{ $pos }}" {{ ($predGrupo[$eq->id] ?? null) == $pos ? 'selected' : '' }}>{{ $pos }}°</option>
            @endfor
          </select>
        </div>
        @endforeach
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%">
        <i class="ti ti-device-floppy" style="font-size:15px"></i> Guardar Grupo {{ $grupo->nombre }}
      </button>
    </form>
    @endif
  </div>
  @empty
  <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--muted)">
    <i class="ti ti-grid-dots" style="font-size:48px;display:block;margin-bottom:12px"></i>
    Aún no hay grupos disponibles para pronosticar.
  </div>
  @endforelse
</div>
@endsection
