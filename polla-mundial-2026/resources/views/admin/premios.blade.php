@extends('layouts.app')
@section('title','Premios')
@section('page-title','Distribución de Premios')

@section('content')
@php
  $medallas    = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
  $porcentajes = [1 => '50%', 2 => '30%', 3 => '20%'];
  $premiosByPos= $premios->keyBy('posicion');
@endphp

<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">

  {{-- Panel izquierdo: generación automática + asignación manual --}}
  <div style="width:330px;flex-shrink:0;display:flex;flex-direction:column;gap:16px">

    {{-- Generar automático con Top 3 --}}
    <div class="card">
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:6px">
        <i class="ti ti-wand" style="color:var(--gold);margin-right:6px"></i>Generar automático
      </h2>
      <p style="font-size:12px;color:var(--muted);margin-bottom:14px">
        Asigna los premios al <strong>Top 3</strong> del ranking actual indicando el pozo total.
      </p>
      <form action="{{ route('admin.premios.generar') }}" method="POST">
        @csrf
        <div style="margin-bottom:14px">
          <label class="form-label">Pozo total (COP) *</label>
          <input type="number" name="monto_total" class="form-input-dark" step="0.01" min="0"
                 placeholder="1000000" value="{{ $montoTotal ?: '' }}" required>
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%">
          <i class="ti ti-bolt" style="font-size:15px"></i> Generar con Top 3
        </button>
      </form>

      @if(count($top3) > 0)
      <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--border)">
        <p style="font-size:11px;color:var(--muted);margin-bottom:8px">Top 3 actual del ranking:</p>
        @foreach($top3 as $i => $t)
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;padding:3px 0">
          <span>{{ $medallas[$i+1] ?? ($i+1) }}</span>
          <span style="font-weight:500">{{ $t->nombre_completo }}</span>
          <span style="color:var(--gold);margin-left:auto">{{ $t->puntos_total }} pts</span>
        </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- Asignar manualmente --}}
    <div class="card">
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:14px">
        <i class="ti ti-hand-finger" style="color:var(--gold);margin-right:6px"></i>Asignar manual
      </h2>
      <form action="{{ route('admin.premios.guardar') }}" method="POST">
        @csrf
        <div style="margin-bottom:12px">
          <label class="form-label">Posición *</label>
          <select name="posicion" class="form-input-dark" required>
            <option value="1">🥇 1° puesto (50%)</option>
            <option value="2">🥈 2° puesto (30%)</option>
            <option value="3">🥉 3° puesto (20%)</option>
          </select>
        </div>
        <div style="margin-bottom:12px">
          <label class="form-label">Participante *</label>
          <select name="usuario_id" class="form-input-dark" required>
            <option value="">Seleccionar...</option>
            @foreach($usuarios as $u)
            <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-bottom:12px">
          <label class="form-label">Pozo total (COP) *</label>
          <input type="number" name="monto_total" class="form-input-dark" step="0.01" min="0"
                 placeholder="1000000" value="{{ $montoTotal ?: '' }}" required>
        </div>
        <div style="margin-bottom:16px">
          <label class="form-label">Estado *</label>
          <select name="estado" class="form-input-dark" required>
            <option value="pendiente">Pendiente</option>
            <option value="confirmado">Confirmado</option>
            <option value="pagado">Pagado</option>
          </select>
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%">
          <i class="ti ti-circle-check" style="font-size:15px"></i> Guardar premio
        </button>
      </form>
    </div>
  </div>

  {{-- Tarjetas de los 3 premios --}}
  <div style="flex:1;min-width:0">
    <div class="grid-3">
      @for($pos = 1; $pos <= 3; $pos++)
      @php $premio = $premiosByPos[$pos] ?? null; @endphp
      <div class="card" style="text-align:center;border-color:{{ $pos==1 ? 'rgba(200,146,42,.5)' : 'var(--border)' }}">
        <div style="font-size:40px;line-height:1;margin-bottom:8px">{{ $medallas[$pos] }}</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">{{ $pos }}° Puesto</div>
        <div style="font-size:12px;color:var(--gold);margin-bottom:12px">{{ $porcentajes[$pos] }} del pozo</div>

        @if($premio && $premio->usuario)
          <div style="padding:10px;background:rgba(255,255,255,.04);border-radius:8px;border:1px solid rgba(255,255,255,.06);margin-bottom:10px">
            <div style="font-weight:600;font-size:14px">{{ $premio->usuario->nombre }} {{ $premio->usuario->apellido }}</div>
            <div style="font-family:'Bebas Neue',sans-serif;font-size:24px;color:var(--gold);margin-top:4px">
              ${{ number_format($premio->monto_ganado, 0, ',', '.') }}
            </div>
          </div>
          <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:10px">
            @php
              $estadoBadge = ['pendiente'=>'badge-gold','confirmado'=>'badge-blue','pagado'=>'badge-green'];
            @endphp
            <span class="badge {{ $estadoBadge[$premio->estado] ?? 'badge-gold' }}">{{ ucfirst($premio->estado) }}</span>
          </div>
          <div style="display:flex;gap:6px;justify-content:center">
            <form action="{{ route('admin.premios.estado', $premio->id) }}" method="POST" style="display:flex;gap:6px">
              @csrf
              <select name="estado" class="form-input-dark" style="padding:5px 8px;font-size:12px;width:auto"
                      onchange="this.form.submit()">
                <option value="pendiente"  {{ $premio->estado=='pendiente'?'selected':'' }}>Pendiente</option>
                <option value="confirmado" {{ $premio->estado=='confirmado'?'selected':'' }}>Confirmado</option>
                <option value="pagado"     {{ $premio->estado=='pagado'?'selected':'' }}>Pagado</option>
              </select>
            </form>
            <form action="{{ route('admin.premios.eliminar', $premio->id) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar este premio?')">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash" style="font-size:13px"></i></button>
            </form>
          </div>
        @else
          <div style="padding:20px 10px;color:var(--muted);font-size:13px">
            <i class="ti ti-user-question" style="font-size:28px;display:block;margin-bottom:6px"></i>
            Sin asignar
          </div>
        @endif
      </div>
      @endfor
    </div>

    {{-- Resumen del pozo --}}
    @if($montoTotal > 0)
    <div class="card" style="margin-top:4px">
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <div class="stat-label">Pozo total del concurso</div>
          <div class="stat-value">${{ number_format($montoTotal, 0, ',', '.') }}</div>
        </div>
        <i class="ti ti-cash-banknote" style="font-size:40px;color:var(--gold);opacity:.5"></i>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection
