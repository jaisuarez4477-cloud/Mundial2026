@extends('layouts.app')
@section('title','Equipos')
@section('page-title','Gestión de Equipos')

@section('content')
<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">

  {{-- Formulario crear equipo --}}
  <div class="card" style="width:320px;flex-shrink:0">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:18px">
      <i class="ti ti-plus-circle" style="color:var(--gold);margin-right:6px"></i>Nuevo equipo
    </h2>
    <form action="{{ route('admin.equipos.crear') }}" method="POST">
      @csrf
      <div class="form-group" style="margin-bottom:14px">
        <label class="form-label">Nombre completo *</label>
        <input type="text" name="nombre" class="form-input-dark"
               placeholder="Colombia" value="{{ old('nombre') }}" required>
      </div>
      <div class="form-group" style="margin-bottom:14px">
        <label class="form-label">Nombre corto *</label>
        <input type="text" name="nombre_corto" class="form-input-dark"
               placeholder="Colombia" value="{{ old('nombre_corto') }}" required>
      </div>
      <div class="form-group" style="margin-bottom:14px">
        <label class="form-label">Código FIFA (3 letras) *</label>
        <input type="text" name="codigo" class="form-input-dark"
               placeholder="COL" maxlength="3" style="text-transform:uppercase"
               value="{{ old('codigo') }}" required>
      </div>
      <div class="form-group" style="margin-bottom:14px">
        <label class="form-label">Confederación *</label>
        <select name="confederacion" class="form-input-dark" required>
          <option value="">Seleccionar...</option>
          @foreach(['CONMEBOL','UEFA','CONCACAF','CAF','AFC','OFC'] as $conf)
          <option value="{{ $conf }}" {{ old('confederacion')==$conf?'selected':'' }}>{{ $conf }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group" style="margin-bottom:18px">
        <label class="form-label">URL de bandera</label>
        <input type="url" name="bandera_url" class="form-input-dark"
               placeholder="https://..." value="{{ old('bandera_url') }}">
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%">
        <i class="ti ti-circle-check" style="font-size:15px"></i> Crear equipo
      </button>
    </form>
  </div>

  {{-- Tabla de equipos --}}
  <div class="card" style="flex:1;min-width:0">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px">
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">
        <i class="ti ti-shirt" style="color:var(--gold);margin-right:6px"></i>
        Equipos registrados
      </h2>
      <span class="badge badge-gold" style="font-size:12px;padding:6px 14px">
        {{ $equipos->count() }} / 48
      </span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Equipo</th>
            <th>Código</th>
            <th>Confederación</th>
            <th>Grupo</th>
            <th style="text-align:center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($equipos as $eq)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                @if($eq->bandera_url)
                  <img src="{{ $eq->bandera_url }}" alt="" class="bandera">
                @else
                  <span class="bandera-placeholder"></span>
                @endif
                <div>
                  <div style="font-weight:500">{{ $eq->nombre }}</div>
                  <div style="font-size:11px;color:var(--muted)">{{ $eq->nombre_corto }}</div>
                </div>
              </div>
            </td>
            <td>
              <span class="badge badge-blue">{{ $eq->codigo }}</span>
            </td>
            <td style="font-size:12px;color:var(--muted)">{{ $eq->confederacion }}</td>
            <td style="font-size:12px;color:var(--muted)">
              {{ $eq->grupos->pluck('nombre')->map(fn($n)=>'Grupo '.$n)->join(', ') ?: '—' }}
            </td>
            <td style="text-align:center">
              <div style="display:flex;gap:6px;justify-content:center">
                <button onclick="abrirEditar({{ $eq->id }},'{{ addslashes($eq->nombre) }}','{{ addslashes($eq->nombre_corto) }}','{{ $eq->codigo }}','{{ $eq->confederacion }}','{{ $eq->bandera_url }}')"
                        class="btn btn-outline btn-sm">
                  <i class="ti ti-edit" style="font-size:13px"></i>
                </button>
                <form action="{{ route('admin.equipos.eliminar', $eq->id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar {{ $eq->nombre }}?')">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="ti ti-trash" style="font-size:13px"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted)">
            No hay equipos registrados.
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal editar equipo --}}
<div id="modal-editar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:100;align-items:center;justify-content:center;padding:20px">
  <div style="background:#0D1B3E;border:1px solid rgba(200,146,42,.4);border-radius:14px;padding:32px;width:100%;max-width:420px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h3 style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px">Editar equipo</h3>
      <button onclick="document.getElementById('modal-editar').style.display='none'"
              style="background:none;border:none;color:var(--muted);font-size:22px;cursor:pointer">✕</button>
    </div>
    <form id="form-editar" method="POST">
      @csrf
      <div style="margin-bottom:14px">
        <label class="form-label">Nombre completo *</label>
        <input type="text" name="nombre" id="edit-nombre" class="form-input-dark" required>
      </div>
      <div style="margin-bottom:14px">
        <label class="form-label">Nombre corto *</label>
        <input type="text" name="nombre_corto" id="edit-nombre-corto" class="form-input-dark" required>
      </div>
      <div style="margin-bottom:14px">
        <label class="form-label">Código FIFA *</label>
        <input type="text" name="codigo" id="edit-codigo" class="form-input-dark"
               maxlength="3" style="text-transform:uppercase" required>
      </div>
      <div style="margin-bottom:14px">
        <label class="form-label">Confederación *</label>
        <select name="confederacion" id="edit-conf" class="form-input-dark" required>
          @foreach(['CONMEBOL','UEFA','CONCACAF','CAF','AFC','OFC'] as $conf)
          <option value="{{ $conf }}">{{ $conf }}</option>
          @endforeach
        </select>
      </div>
      <div style="margin-bottom:20px">
        <label class="form-label">URL de bandera</label>
        <input type="url" name="bandera_url" id="edit-bandera" class="form-input-dark" placeholder="https://...">
      </div>
      <div style="display:flex;gap:10px">
        <button type="button"
                onclick="document.getElementById('modal-editar').style.display='none'"
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
function abrirEditar(id, nombre, nombreCorto, codigo, conf, bandera) {
    document.getElementById('edit-nombre').value       = nombre;
    document.getElementById('edit-nombre-corto').value = nombreCorto;
    document.getElementById('edit-codigo').value       = codigo;
    document.getElementById('edit-conf').value         = conf;
    document.getElementById('edit-bandera').value      = bandera || '';
    document.getElementById('form-editar').action      = '/admin/equipos/' + id + '/editar';
    document.getElementById('modal-editar').style.display = 'flex';
}
document.getElementById('edit-codigo').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endsection