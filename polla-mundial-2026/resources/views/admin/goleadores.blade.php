@extends('layouts.app')
@section('title','Goleadores')
@section('page-title','Gestión de Goleadores y Premios')

@section('content')
<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">

  {{-- Formulario crear goleador --}}
  <div class="card" style="width:320px;flex-shrink:0">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:18px">
      <i class="ti ti-plus-circle" style="color:var(--gold);margin-right:6px"></i>Nuevo goleador
    </h2>
    <form action="{{ route('admin.goleadores.crear') }}" method="POST">
      @csrf
      <div style="margin-bottom:14px">
        <label class="form-label">Equipo *</label>
        <select name="equipo_id" class="form-input-dark" required>
          <option value="">Seleccionar...</option>
          @foreach($equipos as $e)
          <option value="{{ $e->id }}" {{ old('equipo_id')==$e->id?'selected':'' }}>{{ $e->nombre }} ({{ $e->codigo }})</option>
          @endforeach
        </select>
      </div>
      <div style="display:flex;gap:10px;margin-bottom:14px">
        <div style="flex:1">
          <label class="form-label">Nombre *</label>
          <input type="text" name="nombre" class="form-input-dark" placeholder="Lionel" value="{{ old('nombre') }}" required>
        </div>
        <div style="flex:1">
          <label class="form-label">Apellido *</label>
          <input type="text" name="apellido" class="form-input-dark" placeholder="Messi" value="{{ old('apellido') }}" required>
        </div>
      </div>
      <div style="display:flex;gap:10px;margin-bottom:14px">
        <div style="flex:1">
          <label class="form-label">Goles *</label>
          <input type="number" name="goles" class="form-input-dark" min="0" max="50" value="{{ old('goles',0) }}" required>
        </div>
        <div style="flex:1">
          <label class="form-label">Asist. *</label>
          <input type="number" name="asistencias" class="form-input-dark" min="0" max="50" value="{{ old('asistencias',0) }}" required>
        </div>
        <div style="flex:1">
          <label class="form-label">Min. *</label>
          <input type="number" name="minutos" class="form-input-dark" min="0" max="1200" value="{{ old('minutos',0) }}" required>
        </div>
      </div>
      <div style="margin-bottom:18px">
        <label class="form-label">Premio individual *</label>
        <select name="tipo_premio" class="form-input-dark" required>
          <option value="ninguno">Ninguno</option>
          @foreach($premios as $key => $label)
          <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%">
        <i class="ti ti-circle-check" style="font-size:15px"></i> Registrar goleador
      </button>
    </form>
  </div>

  {{-- Tabla de goleadores --}}
  <div class="card" style="flex:1;min-width:0">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px">
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">
        <i class="ti ti-shoe" style="color:var(--gold);margin-right:6px"></i>Tabla de goleadores
      </h2>
      <span class="badge badge-gold" style="font-size:12px;padding:6px 14px">{{ $goleadores->count() }} jugadores</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Jugador</th>
            <th>Equipo</th>
            <th style="text-align:center">Goles</th>
            <th style="text-align:center">Asist.</th>
            <th>Premio</th>
            <th style="text-align:center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($goleadores as $g)
          <tr>
            <td style="font-weight:500">{{ $g->nombre }} {{ $g->apellido }}</td>
            <td style="font-size:12px;color:var(--muted)">{{ $g->equipo->nombre ?? '—' }}</td>
            <td style="text-align:center;font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--gold)">{{ $g->goles }}</td>
            <td style="text-align:center;color:var(--muted)">{{ $g->asistencias }}</td>
            <td>
              @if($g->tipo_premio !== 'ninguno')
                <span class="badge {{ str_contains($g->tipo_premio,'oro') ? 'badge-gold' : 'badge-blue' }}" style="font-size:10px">
                  {{ $premios[$g->tipo_premio] ?? $g->tipo_premio }}
                </span>
              @else
                <span style="color:var(--muted);font-size:12px">—</span>
              @endif
            </td>
            <td style="text-align:center">
              <div style="display:flex;gap:6px;justify-content:center">
                <button onclick="abrirEditar({{ $g->id }},{{ $g->equipo_id }},'{{ addslashes($g->nombre) }}','{{ addslashes($g->apellido) }}',{{ $g->goles }},{{ $g->asistencias }},{{ $g->minutos }},'{{ $g->tipo_premio }}')"
                        class="btn btn-outline btn-sm"><i class="ti ti-edit" style="font-size:13px"></i></button>
                <form action="{{ route('admin.goleadores.eliminar', $g->id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar a {{ $g->nombre }} {{ $g->apellido }}?')">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash" style="font-size:13px"></i></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted)">No hay goleadores registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Evaluar predicciones de desempate --}}
    <div style="margin-top:20px;padding-top:18px;border-top:1px solid var(--border)">
      <h3 style="font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:1px;margin-bottom:6px">
        <i class="ti ti-checkbox" style="color:var(--gold);margin-right:6px"></i>Evaluar predicciones de desempate
      </h3>
      <p style="font-size:12px;color:var(--muted);margin-bottom:12px">
        Tras asignar un premio a un jugador, evalúa las predicciones de los participantes para esa categoría.
      </p>
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        @foreach($premios as $key => $label)
        <form action="{{ route('admin.goleadores.evaluar', $key) }}" method="POST"
              onsubmit="return confirm('¿Evaluar predicciones de {{ $label }}?')">
          @csrf
          <button type="submit" class="btn btn-outline btn-sm">
            <i class="ti ti-gavel" style="font-size:13px"></i> {{ $label }}
          </button>
        </form>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- Modal editar goleador --}}
<div id="modal-editar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:100;align-items:center;justify-content:center;padding:20px">
  <div style="background:#0D1B3E;border:1px solid rgba(200,146,42,.4);border-radius:14px;padding:32px;width:100%;max-width:420px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h3 style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px">Editar goleador</h3>
      <button onclick="document.getElementById('modal-editar').style.display='none'"
              style="background:none;border:none;color:var(--muted);font-size:22px;cursor:pointer">✕</button>
    </div>
    <form id="form-editar" method="POST">
      @csrf
      <div style="margin-bottom:14px">
        <label class="form-label">Equipo *</label>
        <select name="equipo_id" id="edit-equipo" class="form-input-dark" required>
          @foreach($equipos as $e)
          <option value="{{ $e->id }}">{{ $e->nombre }} ({{ $e->codigo }})</option>
          @endforeach
        </select>
      </div>
      <div style="display:flex;gap:10px;margin-bottom:14px">
        <div style="flex:1"><label class="form-label">Nombre *</label>
          <input type="text" name="nombre" id="edit-nombre" class="form-input-dark" required></div>
        <div style="flex:1"><label class="form-label">Apellido *</label>
          <input type="text" name="apellido" id="edit-apellido" class="form-input-dark" required></div>
      </div>
      <div style="display:flex;gap:10px;margin-bottom:14px">
        <div style="flex:1"><label class="form-label">Goles *</label>
          <input type="number" name="goles" id="edit-goles" class="form-input-dark" min="0" max="50" required></div>
        <div style="flex:1"><label class="form-label">Asist. *</label>
          <input type="number" name="asistencias" id="edit-asist" class="form-input-dark" min="0" max="50" required></div>
        <div style="flex:1"><label class="form-label">Min. *</label>
          <input type="number" name="minutos" id="edit-min" class="form-input-dark" min="0" max="1200" required></div>
      </div>
      <div style="margin-bottom:20px">
        <label class="form-label">Premio individual *</label>
        <select name="tipo_premio" id="edit-premio" class="form-input-dark" required>
          <option value="ninguno">Ninguno</option>
          @foreach($premios as $key => $label)
          <option value="{{ $key }}">{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div style="display:flex;gap:10px">
        <button type="button" onclick="document.getElementById('modal-editar').style.display='none'"
                class="btn btn-outline" style="flex:1">Cancelar</button>
        <button type="submit" class="btn btn-gold" style="flex:1">
          <i class="ti ti-circle-check" style="font-size:15px"></i> Guardar
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
function abrirEditar(id, equipoId, nombre, apellido, goles, asist, min, premio) {
    document.getElementById('edit-equipo').value  = equipoId;
    document.getElementById('edit-nombre').value  = nombre;
    document.getElementById('edit-apellido').value= apellido;
    document.getElementById('edit-goles').value   = goles;
    document.getElementById('edit-asist').value   = asist;
    document.getElementById('edit-min').value     = min;
    document.getElementById('edit-premio').value  = premio;
    document.getElementById('form-editar').action = '/admin/goleadores/' + id + '/editar';
    document.getElementById('modal-editar').style.display = 'flex';
}
</script>
@endsection
