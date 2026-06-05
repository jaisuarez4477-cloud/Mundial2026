@extends('layouts.app')
@section('title','Resultados oficiales')
@section('page-title','Resultados Oficiales y Cálculo de Puntos')

@section('content')
<div class="card" style="margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px">
    <i class="ti ti-calculator" style="font-size:22px;color:var(--gold)"></i>
    <div>
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">Cierre de fases y cálculo automático</h2>
      <p style="font-size:13px;color:var(--muted)">
        Registra aquí las <strong>posiciones finales reales</strong> de cada grupo y los <strong>equipos clasificados</strong> a cada fase.
        Al guardar, el sistema calcula y suma automáticamente los puntos a cada participante que acertó, y actualiza el ranking.
      </p>
    </div>
  </div>
</div>

{{-- ════════════════ POSICIONES FINALES DE GRUPOS ════════════════ --}}
<h2 style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px;margin:6px 0 14px">
  <i class="ti ti-list-numbers" style="color:var(--gold);margin-right:6px"></i>Posiciones finales por grupo
  <span style="font-size:12px;color:var(--muted);font-family:'Barlow',sans-serif;letter-spacing:0">· 10 pts por posición acertada</span>
</h2>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-bottom:32px">
  @forelse($grupos as $grupo)
  <div class="card">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
      <div style="width:36px;height:36px;border-radius:8px;background:rgba(200,146,42,.15);border:1px solid rgba(200,146,42,.3);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--gold)">
        {{ $grupo->nombre }}
      </div>
      <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px">Grupo {{ $grupo->nombre }}</div>
    </div>

    @if($grupo->equipos->count() === 0)
      <p style="color:var(--muted);font-size:12px;text-align:center;padding:14px 0">Sin equipos asignados.</p>
    @else
    <form action="{{ route('admin.resultados.grupo', $grupo->id) }}" method="POST">
      @csrf
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
            <option value="{{ $pos }}" {{ ($eq->pivot->posicion_final ?? null) == $pos ? 'selected' : '' }}>{{ $pos }}°</option>
            @endfor
          </select>
        </div>
        @endforeach
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%"
              onclick="return confirm('¿Guardar posiciones del Grupo {{ $grupo->nombre }} y calcular puntos? Esta acción suma puntos a los participantes.')">
        <i class="ti ti-calculator" style="font-size:15px"></i> Guardar y calcular
      </button>
    </form>
    @endif
  </div>
  @empty
  <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted)">No hay grupos creados.</div>
  @endforelse
</div>

{{-- ════════════════ RESULTADOS DE PREDICCIONES ESPECIALES ════════════════ --}}
<h2 style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px;margin:6px 0 14px">
  <i class="ti ti-tournament" style="color:var(--gold);margin-right:6px"></i>Equipos clasificados por fase
  <span style="font-size:12px;color:var(--muted);font-family:'Barlow',sans-serif;letter-spacing:0">· marca los equipos que avanzaron realmente</span>
</h2>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px">
  @foreach($tipos as $key => $label)
  @php $seleccionados = $oficiales[$key] ?? []; @endphp
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
      <h3 style="font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:1px">{{ $label }}</h3>
      <span class="badge badge-gold">{{ $puntos[$key] }} pts</span>
    </div>
    <form action="{{ route('admin.resultados.especial') }}" method="POST">
      @csrf
      <input type="hidden" name="tipo" value="{{ $key }}">
      <div style="max-height:220px;overflow-y:auto;display:flex;flex-direction:column;gap:5px;margin-bottom:12px;padding-right:4px">
        @foreach($equipos as $eq)
        <label style="display:flex;align-items:center;gap:8px;padding:6px 9px;background:rgba(255,255,255,.04);border-radius:6px;border:1px solid rgba(255,255,255,.06);cursor:pointer;font-size:13px">
          <input type="checkbox" name="equipos[]" value="{{ $eq->id }}"
                 {{ in_array($eq->id, $seleccionados) ? 'checked' : '' }}
                 style="accent-color:var(--gold);width:15px;height:15px">
          @if($eq->bandera_url)
            <img src="{{ $eq->bandera_url }}" alt="" class="bandera">
          @else
            <span class="bandera-placeholder"></span>
          @endif
          <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $eq->nombre }}</span>
          <span class="badge badge-blue" style="font-size:9px;margin-left:auto">{{ $eq->codigo }}</span>
        </label>
        @endforeach
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%"
              onclick="return confirm('¿Guardar resultado de «{{ $label }}» y calcular puntos?')">
        <i class="ti ti-calculator" style="font-size:14px"></i> Guardar y calcular
      </button>
    </form>
  </div>
  @endforeach
</div>
@endsection
