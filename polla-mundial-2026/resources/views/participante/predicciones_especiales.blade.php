@extends('layouts.app')
@section('title','Predicciones Especiales')
@section('page-title','Predicciones Especiales')

@section('content')
<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">

  {{-- Formulario agregar predicción especial --}}
  <div class="card" style="width:340px;flex-shrink:0">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:6px">
      <i class="ti ti-tournament" style="color:var(--gold);margin-right:6px"></i>Nueva predicción
    </h2>
    <p style="font-size:12px;color:var(--muted);margin-bottom:16px">
      Predice qué equipos avanzarán en cada fase y quién será campeón.
    </p>
    <form action="{{ route('predicciones.especiales.guardar') }}" method="POST">
      @csrf
      <div style="margin-bottom:14px">
        <label class="form-label">Categoría *</label>
        <select name="tipo" class="form-input-dark" required>
          <option value="">Seleccionar...</option>
          @foreach($tipos as $key => $info)
          <option value="{{ $key }}">{{ $info['label'] }} ({{ $info['pts'] }} pts)</option>
          @endforeach
        </select>
      </div>
      <div style="margin-bottom:18px">
        <label class="form-label">Equipo *</label>
        <select name="equipo_id" class="form-input-dark" required>
          <option value="">Seleccionar equipo...</option>
          @foreach($equipos as $e)
          <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%">
        <i class="ti ti-circle-check" style="font-size:15px"></i> Agregar predicción
      </button>
    </form>

    <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--border)">
      <p style="font-size:11px;color:var(--muted);line-height:1.6">
        <i class="ti ti-info-circle" style="color:var(--gold)"></i>
        Para <strong>Campeón</strong>, <strong>Subcampeón</strong> y <strong>Tercer puesto</strong> solo se permite un equipo (reemplaza el anterior).
        Para las fases de clasificación puedes agregar varios equipos.
      </p>
    </div>
  </div>

  {{-- Lista de predicciones por categoría --}}
  <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:16px">
    @foreach($tipos as $key => $info)
    @php $items = $misPred[$key] ?? collect(); @endphp
    <div class="card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <h3 style="font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:1px">
          {{ $info['label'] }}
        </h3>
        <span class="badge badge-gold">{{ $info['pts'] }} pts</span>
      </div>
      @if($items->count() === 0)
        <p style="font-size:12px;color:var(--muted)">Sin predicciones en esta categoría.</p>
      @else
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        @foreach($items as $pred)
        <div style="display:flex;align-items:center;gap:8px;padding:6px 10px;background:rgba(255,255,255,.04);border-radius:7px;border:1px solid rgba(255,255,255,.06)">
          @if($pred->equipo && $pred->equipo->bandera_url)
            <img src="{{ $pred->equipo->bandera_url }}" alt="" class="bandera">
          @else
            <span class="bandera-placeholder"></span>
          @endif
          <span style="font-size:13px;font-weight:500">{{ $pred->equipo->nombre ?? '—' }}</span>
          @if($pred->calculado)
            <span class="badge {{ $pred->puntos_obtenidos > 0 ? 'badge-green' : 'badge-red' }}" style="font-size:10px">
              {{ $pred->puntos_obtenidos > 0 ? '✓ +'.$pred->puntos_obtenidos : '✗ 0' }}
            </span>
          @else
            <form action="{{ route('predicciones.especiales.eliminar', $pred->id) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar esta predicción?')" style="display:inline">
              @csrf
              <button type="submit" style="background:none;border:none;color:rgba(204,0,0,.6);cursor:pointer;font-size:14px;padding:0 2px" title="Eliminar">
                <i class="ti ti-x"></i>
              </button>
            </form>
          @endif
        </div>
        @endforeach
      </div>
      @endif
    </div>
    @endforeach
  </div>
</div>
@endsection
