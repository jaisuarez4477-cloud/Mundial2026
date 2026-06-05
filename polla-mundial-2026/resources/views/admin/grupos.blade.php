@extends('layouts.app')
@section('title','Grupos')
@section('page-title','Gestión de Grupos')

@section('content')
<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">

  {{-- Panel izquierdo: crear grupo + asignar equipo --}}
  <div style="width:300px;flex-shrink:0;display:flex;flex-direction:column;gap:16px">

    <div class="card">
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:16px">
        <i class="ti ti-plus-circle" style="color:var(--gold);margin-right:6px"></i>Nuevo grupo
      </h2>
      <form action="{{ route('admin.grupos.crear') }}" method="POST">
        @csrf
        <div style="margin-bottom:14px">
          <label class="form-label">Letra del grupo *</label>
          <input type="text" name="nombre" class="form-input-dark"
                 placeholder="A" maxlength="1" style="text-transform:uppercase;font-size:20px;text-align:center;font-family:'Bebas Neue',sans-serif"
                 value="{{ old('nombre') }}" required>
        </div>
        <div style="margin-bottom:16px">
          <label class="form-label">Descripción</label>
          <input type="text" name="descripcion" class="form-input-dark"
                 placeholder="Opcional..." value="{{ old('descripcion') }}">
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%">
          <i class="ti ti-circle-check" style="font-size:15px"></i> Crear grupo
        </button>
      </form>
    </div>

    <div class="card">
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:16px">
        <i class="ti ti-link" style="color:var(--gold);margin-right:6px"></i>Asignar equipo
      </h2>
      <form action="{{ route('admin.grupos.asignar') }}" method="POST">
        @csrf
        <div style="margin-bottom:14px">
          <label class="form-label">Grupo *</label>
          <select name="grupo_id" class="form-input-dark" required>
            <option value="">Seleccionar grupo...</option>
            @foreach($grupos as $g)
            <option value="{{ $g->id }}">Grupo {{ $g->nombre }} ({{ $g->equipos->count() }}/4)</option>
            @endforeach
          </select>
        </div>
        <div style="margin-bottom:16px">
          <label class="form-label">Equipo *</label>
          <select name="equipo_id" class="form-input-dark" required>
            <option value="">Seleccionar equipo...</option>
            @foreach($equipos as $e)
            <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%">
          <i class="ti ti-circle-check" style="font-size:15px"></i> Asignar
        </button>
      </form>
    </div>
  </div>

  {{-- Tabla de grupos con sus equipos --}}
  <div style="flex:1;min-width:0">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px">
      @forelse($grupos as $grupo)
      <div class="card" style="border-color:{{ $grupo->equipos->count()==4 ? 'rgba(29,158,117,.4)' : 'rgba(200,146,42,.25)' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <div style="display:flex;align-items:center;gap:10px">
            <div style="width:36px;height:36px;border-radius:8px;background:rgba(200,146,42,.15);border:1px solid rgba(200,146,42,.3);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--gold)">
              {{ $grupo->nombre }}
            </div>
            <div>
              <div style="font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:1px">Grupo {{ $grupo->nombre }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $grupo->equipos->count() }}/4 equipos</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:6px">
            <span class="badge {{ $grupo->equipos->count()==4 ? 'badge-green' : 'badge-gold' }}" style="font-size:10px">
              {{ $grupo->equipos->count()==4 ? 'Completo' : 'Incompleto' }}
            </span>
            <form action="{{ route('admin.grupos.eliminar', $grupo->id) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar Grupo {{ $grupo->nombre }}?')">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm" style="padding:4px 8px">
                <i class="ti ti-trash" style="font-size:12px"></i>
              </button>
            </form>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px">
          @forelse($grupo->equipos as $eq)
          <div style="display:flex;align-items:center;justify-content:space-between;padding:7px 10px;background:rgba(255,255,255,.04);border-radius:7px;border:1px solid rgba(255,255,255,.06)">
            <div style="display:flex;align-items:center;gap:8px">
              @if($eq->bandera_url)
                <img src="{{ $eq->bandera_url }}" alt="" class="bandera">
              @else
                <span class="bandera-placeholder"></span>
              @endif
              <span style="font-size:13px;font-weight:500">{{ $eq->nombre }}</span>
              <span class="badge badge-blue" style="font-size:10px">{{ $eq->codigo }}</span>
            </div>
            <form action="{{ route('admin.grupos.quitar', [$grupo->id, $eq->id]) }}" method="POST"
                  onsubmit="return confirm('¿Quitar {{ $eq->nombre }} del Grupo {{ $grupo->nombre }}?')">
              @csrf
              <button type="submit" style="background:none;border:none;color:rgba(204,0,0,.6);cursor:pointer;font-size:14px;padding:2px 4px" title="Quitar del grupo">
                <i class="ti ti-x"></i>
              </button>
            </form>
          </div>
          @empty
          <div style="text-align:center;padding:16px;color:var(--muted);font-size:12px">
            <i class="ti ti-users" style="font-size:20px;display:block;margin-bottom:4px"></i>
            Sin equipos asignados
          </div>
          @endforelse
        </div>
      </div>
      @empty
      <div style="grid-column:span 3;text-align:center;padding:60px;color:var(--muted)">
        <i class="ti ti-grid-dots" style="font-size:48px;display:block;margin-bottom:12px"></i>
        No hay grupos creados aún.
      </div>
      @endforelse
    </div>
  </div>
</div>
@endsection