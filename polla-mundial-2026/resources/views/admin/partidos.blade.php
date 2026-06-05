@extends('layouts.app')
@section('title','Gestión de Partidos')
@section('page-title','Gestión de Partidos')

@section('content')
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;justify-content:space-between;width:100%">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px">
        <i class="ti ti-ball-football" style="..."></i> Todos los partidos
    </h2>
    <button onclick="document.getElementById('modal-crear-partido').style.display='flex'"
            class="btn btn-gold">
        <i class="ti ti-plus" style="font-size:15px"></i> Nuevo partido
    </button>
</div>

        <div style="display:flex;gap:8px">
            <span class="badge badge-green" style="font-size:12px;padding:6px 12px">
                {{ $partidos->where('finalizado',1)->count() }} finalizados
            </span>
            <span class="badge badge-gold" style="font-size:12px;padding:6px 12px">
                {{ $partidos->where('finalizado',0)->count() }} pendientes
            </span>

     </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Fase</th>
                    <th>Local</th>
                    <th style="text-align:center">Resultado</th>
                    <th>Visitante</th>
                    <th>Sede</th>
                    <th style="text-align:center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partidos as $partido)
                <tr>
                    <td style="font-size:12px;color:var(--muted)">
                        {{ $partido->fecha_hora ? $partido->fecha_hora->format('d M Y H:i') : 'Por definir' }}
                    </td>
                    <td>
                        <div style="font-size:12px">{{ $partido->fase->nombre }}</div>
                        @if($partido->grupo)
                        <div style="font-size:11px;color:var(--muted)">Grupo {{ $partido->grupo->nombre }}</div>
                        @endif
                    </td>
                    <td style="font-weight:500">{{ $partido->equipoLocal->nombre ?? $partido->llave_referencia ?? '—' }}</td>
                    <td style="text-align:center">
                        @if(!is_null($partido->goles_local))
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--gold);letter-spacing:3px">
                                {{ $partido->goles_local }} – {{ $partido->goles_visitante }}
                            </span>
                        @else
                            <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td style="font-weight:500">{{ $partido->equipoVisitante->nombre ?? '—' }}</td>
                    <td style="font-size:12px;color:var(--muted)">{{ $partido->estadio }}<br>{{ $partido->ciudad }}</td>
                    <td style="text-align:center">
                        @if(!$partido->finalizado)
                        <button onclick="abrirModal({{ $partido->id }}, '{{ addslashes($partido->equipoLocal->nombre ?? '?') }}','{{ addslashes($partido->equipoVisitante->nombre ?? '?') }}')"
                                class="btn btn-gold btn-sm">
                            <i class="ti ti-edit" style="font-size:13px"></i> Resultado
                        </button>
                        @else
                        <span class="badge badge-green">Registrado</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">No hay partidos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal resultado --}}
<div id="modal-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:100;align-items:center;justify-content:center">
    <div style="background:#0D1B3E;border:1px solid rgba(200,146,42,.4);border-radius:14px;padding:32px;width:100%;max-width:380px;margin:20px">
        <h3 style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px;margin-bottom:6px">Registrar resultado</h3>
        <div id="modal-subtitulo" style="font-size:13px;color:var(--muted);margin-bottom:24px"></div>
        <form id="modal-form" method="POST">
            @csrf
            @method('POST')
            <div style="display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:24px">
                <div style="text-align:center">
                    <div id="modal-local" style="font-size:13px;color:var(--muted);margin-bottom:8px"></div>
                    <input type="number" name="goles_local" class="score-input" min="0" max="20" value="0" style="width:64px;font-size:28px;padding:12px 8px">
                </div>
                <span style="font-family:'Bebas Neue',sans-serif;font-size:24px;color:var(--muted)">–</span>
                <div style="text-align:center">
                    <div id="modal-visitante" style="font-size:13px;color:var(--muted);margin-bottom:8px"></div>
                    <input type="number" name="goles_visitante" class="score-input" min="0" max="20" value="0" style="width:64px;font-size:28px;padding:12px 8px">
                </div>
            </div>
            <div style="display:flex;gap:10px">
                <button type="button" onclick="cerrarModal()" class="btn btn-outline" style="flex:1">Cancelar</button>
                <button type="submit" class="btn btn-gold" style="flex:1">
                    <i class="ti ti-circle-check" style="font-size:15px"></i> Confirmar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function abrirModal(id, local, visitante) {
    document.getElementById('modal-local').textContent = local;
    document.getElementById('modal-visitante').textContent = visitante;
    document.getElementById('modal-subtitulo').textContent = local + ' vs ' + visitante;
    document.getElementById('modal-form').action = '/admin/partidos/' + id + '/resultado';
    const overlay = document.getElementById('modal-overlay');
    overlay.style.display = 'flex';
}
function cerrarModal() {
    document.getElementById('modal-overlay').style.display = 'none';
}
document.getElementById('modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
{{-- Modal crear partido --}}
<div id="modal-crear-partido" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:100;align-items:center;justify-content:center;padding:20px">
  <div style="background:#0D1B3E;border:1px solid rgba(200,146,42,.4);border-radius:14px;padding:32px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h3 style="font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px">Nuevo Partido</h3>
      <button onclick="document.getElementById('modal-crear-partido').style.display='none'"
              style="background:none;border:none;color:var(--muted);font-size:22px;cursor:pointer">✕</button>
    </div>
    <form action="{{ route('admin.partidos.crear') }}" method="POST">
      @csrf
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

        <div style="grid-column:span 2">
          <label class="form-label">Fase *</label>
          <select name="fase_id" class="form-input-dark" required>
            <option value="">Seleccionar fase...</option>
            @foreach($fases as $f)
            <option value="{{ $f->id }}">{{ $f->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div style="grid-column:span 2">
          <label class="form-label">Grupo (solo fase de grupos)</label>
          <select name="grupo_id" class="form-input-dark">
            <option value="">Sin grupo / Eliminatoria</option>
            @foreach($grupos as $g)
            <option value="{{ $g->id }}">Grupo {{ $g->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="form-label">Equipo local</label>
          <select name="equipo_local_id" class="form-input-dark">
            <option value="">Por definir</option>
            @foreach($equipos as $e)
            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="form-label">Equipo visitante</label>
          <select name="equipo_visitante_id" class="form-input-dark">
            <option value="">Por definir</option>
            @foreach($equipos as $e)
            <option value="{{ $e->id }}">{{ $e->nombre }}</option>
            @endforeach
          </select>
        </div>

        <div style="grid-column:span 2">
          <label class="form-label">Fecha y hora *</label>
          <input type="datetime-local" name="fecha_hora" class="form-input-dark" required>
        </div>

        <div>
          <label class="form-label">Estadio *</label>
          <input type="text" name="estadio" class="form-input-dark" placeholder="Estadio Azteca" required>
        </div>

        <div>
          <label class="form-label">Ciudad *</label>
          <input type="text" name="ciudad" class="form-input-dark" placeholder="Ciudad de México" required>
        </div>

        <div style="grid-column:span 2">
          <label class="form-label">País sede *</label>
          <input type="text" name="pais_sede" class="form-input-dark" placeholder="México" required>
        </div>

        <div style="grid-column:span 2">
          <label class="form-label">Llave referencia (eliminatorias)</label>
          <input type="text" name="llave_referencia" class="form-input-dark"
                 placeholder="Ej: Ganador Grupo A vs Segundo Grupo B">
        </div>

      </div>
      <div style="display:flex;gap:10px;margin-top:20px">
        <button type="button"
                onclick="document.getElementById('modal-crear-partido').style.display='none'"
                class="btn btn-outline" style="flex:1">Cancelar</button>
        <button type="submit" class="btn btn-gold" style="flex:1">
          <i class="ti ti-circle-check" style="font-size:15px"></i> Crear partido
        </button>
      </div>
    </form>
  </div>
</div>


@endsection
