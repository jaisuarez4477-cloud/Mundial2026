@extends('layouts.app')
@section('title','Predicciones de Desempate')
@section('page-title','Premios Individuales (Desempate)')

@section('content')
<div class="card" style="margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px">
    <i class="ti ti-award" style="font-size:22px;color:var(--gold)"></i>
    <div>
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">Premios individuales</h2>
      <p style="font-size:13px;color:var(--muted)">
        Predice los ganadores de la <strong>Bota de Oro</strong> (goleadores) y el <strong>Balón de Oro</strong> (mejores jugadores).
        Se usan como criterio de desempate del concurso. Escribe el nombre completo del jugador.
      </p>
    </div>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px">
  @foreach($tipos as $key => $label)
  @php $pred = $misPred[$key] ?? null; @endphp
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
      <div style="display:flex;align-items:center;gap:8px">
        <i class="ti {{ str_contains($key,'bota') ? 'ti-shoe' : 'ti-ball-football' }}"
           style="font-size:20px;color:{{ str_contains($key,'oro') ? 'var(--gold)' : (str_contains($key,'plata') ? '#C0C0C0' : '#CD7F32') }}"></i>
        <h3 style="font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:1px">{{ $label }}</h3>
      </div>
      @if($pred && !is_null($pred->correcto))
        <span class="badge {{ $pred->correcto ? 'badge-green' : 'badge-red' }}" style="font-size:10px">
          {{ $pred->correcto ? '✓ Acertó' : '✗ Falló' }}
        </span>
      @endif
    </div>

    <form action="{{ route('predicciones.desempate.guardar') }}" method="POST">
      @csrf
      <input type="hidden" name="tipo" value="{{ $key }}">
      <div style="margin-bottom:12px">
        <label class="form-label">Jugador</label>
        <input type="text" name="jugador_nombre" class="form-input-dark"
               placeholder="Ej: Lionel Messi"
               value="{{ $pred->jugador_nombre ?? '' }}"
               {{ ($pred && !is_null($pred->correcto)) ? 'disabled' : '' }} required>
      </div>
      @if(!($pred && !is_null($pred->correcto)))
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-gold" style="flex:1">
          <i class="ti ti-device-floppy" style="font-size:14px"></i> Guardar
        </button>
        @if($pred)
        <button type="button" onclick="document.getElementById('del-{{ $pred->id }}').submit()"
                class="btn btn-danger btn-sm" title="Eliminar">
          <i class="ti ti-trash" style="font-size:14px"></i>
        </button>
        @endif
      </div>
      @else
      <p style="font-size:11px;color:var(--muted)"><i class="ti ti-lock"></i> Predicción evaluada, ya no se puede modificar.</p>
      @endif
    </form>

    @if($pred && is_null($pred->correcto))
    <form id="del-{{ $pred->id }}" action="{{ route('predicciones.desempate.eliminar', $pred->id) }}"
          method="POST" onsubmit="return confirm('¿Eliminar esta predicción?')" style="display:none">
      @csrf
    </form>
    @endif
  </div>
  @endforeach
</div>
@endsection
