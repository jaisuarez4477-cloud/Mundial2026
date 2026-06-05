@extends('layouts.app')
@section('title','Mis Pronósticos')
@section('page-title','Pronósticos')

@section('styles')
<style>
.partido-card{
    background:var(--card);border:1px solid var(--border);border-radius:12px;
    padding:20px 24px;margin-bottom:16px;transition:border-color .2s;
}
.partido-card:hover{border-color:rgba(200,146,42,.45)}
.partido-card.finalizado{opacity:.6;pointer-events:none}
.partido-card.con-prono{border-color:rgba(29,158,117,.35)}
.vs-badge{
    padding:6px 16px;background:rgba(200,146,42,.12);
    border:1px solid rgba(200,146,42,.25);border-radius:8px;
    font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:3px;
    color:var(--gold);margin:0 16px;flex-shrink:0;
}
.score-wrap{display:flex;align-items:center;gap:8px;justify-content:center;margin-top:12px}
.score-sep{font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--muted)}
.prono-pts{
    font-size:12px;color:#5DCAA5;font-weight:600;
    background:rgba(29,158,117,.1);border:1px solid rgba(29,158,117,.25);
    padding:3px 10px;border-radius:20px;
}
.filter-bar{
    display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap;align-items:center;
}
.filter-btn{
    padding:7px 16px;border-radius:20px;font-size:12px;font-weight:600;
    background:var(--card);border:1px solid var(--border);color:var(--muted);
    cursor:pointer;transition:all .2s;
}
.filter-btn.active{background:rgba(200,146,42,.15);border-color:rgba(200,146,42,.4);color:var(--gold)}
</style>
@endsection

@section('content')
<div class="filter-bar">
    <span style="font-size:13px;color:var(--muted);margin-right:4px">Filtrar:</span>
    <button class="filter-btn active" onclick="filtrar('todos',this)">Todos</button>
    <button class="filter-btn" onclick="filtrar('pendientes',this)">Pendientes</button>
    <button class="filter-btn" onclick="filtrar('registrados',this)">Con pronóstico</button>
    <button class="filter-btn" onclick="filtrar('finalizados',this)">Finalizados</button>
</div>

@forelse($partidos as $partido)
@php
    $tieneProno = isset($misProns[$partido->id]);
    $finalizado = $partido->finalizado;
    $comenzado  = $partido->fecha_hora && $partido->fecha_hora <= now();
    $clases     = 'partido-card';
    if ($finalizado) $clases .= ' finalizado';
    elseif ($tieneProno) $clases .= ' con-prono';
    $dataFiltro = $finalizado ? 'finalizado' : ($tieneProno ? 'registrado' : 'pendiente');
@endphp

<div class="{{ $clases }}" data-filtro="{{ $dataFiltro }}">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:8px">
        <div style="display:flex;align-items:center;gap:8px">
            <span class="badge badge-blue" style="font-size:11px">{{ $partido->fase->nombre }}</span>
            @if($partido->grupo)
            <span class="badge badge-gold" style="font-size:11px">Grupo {{ $partido->grupo->nombre }}</span>
            @endif
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:12px;color:var(--muted)">
                <i class="ti ti-calendar" style="font-size:12px"></i>
                {{ $partido->fecha_hora ? $partido->fecha_hora->format('d M Y · H:i') : 'Por definir' }}
            </span>
            @if($finalizado)
                <span class="badge badge-red">Finalizado</span>
            @elseif($comenzado)
                <span class="badge" style="background:rgba(239,159,39,.15);color:#EF9F27;border:1px solid rgba(239,159,39,.3)">En curso</span>
            @elseif($tieneProno)
                <span class="badge badge-green">Pronóstico registrado</span>
            @else
                <span class="badge" style="background:rgba(255,255,255,.06);color:var(--muted);border:1px solid rgba(255,255,255,.1)">Sin pronóstico</span>
            @endif
        </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between">
        <div style="flex:1;text-align:right">
            <div style="font-size:16px;font-weight:600">{{ $partido->equipoLocal->nombre ?? $partido->llave_referencia ?? '—' }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $partido->equipoLocal->confederacion ?? '' }}</div>
        </div>

        <div style="text-align:center;padding:0 8px">
            @if($finalizado)
                <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:3px;color:var(--gold)">
                    {{ $partido->goles_local }} – {{ $partido->goles_visitante }}
                </div>
                <div style="font-size:11px;color:var(--muted)">Resultado final</div>
                @if($tieneProno)
                <div style="font-size:11px;margin-top:4px">
                    Tu pronóstico: <span style="color:var(--gold)">{{ $misPronsLocal[$partido->id] ?? '?' }} – {{ $misProns[$partido->id] ?? '?' }}</span>
                </div>
                @endif
            @else
            <form action="{{ route('pronosticos.guardar') }}" method="POST">
                @csrf
                <input type="hidden" name="partido_id" value="{{ $partido->id }}">
                <div class="score-wrap">
                    <input type="number" name="goles_local" class="score-input"
                           min="0" max="20"
                           value="{{ $misPronsLocal[$partido->id] ?? '' }}"
                           placeholder="0" {{ ($comenzado || $finalizado) ? 'disabled' : '' }}>
                    <span class="score-sep">–</span>
                    <input type="number" name="goles_visitante" class="score-input"
                           min="0" max="20"
                           value="{{ $misProns[$partido->id] ?? '' }}"
                           placeholder="0" {{ ($comenzado || $finalizado) ? 'disabled' : '' }}>
                </div>
                @if(!$comenzado && !$finalizado)
                <div style="text-align:center;margin-top:10px">
                    <button type="submit" class="btn btn-gold btn-sm">
                        <i class="ti ti-device-floppy" style="font-size:14px"></i>
                        {{ $tieneProno ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
                @endif
            </form>
            @endif
        </div>

        <div style="flex:1;text-align:left">
            <div style="font-size:16px;font-weight:600">{{ $partido->equipoVisitante->nombre ?? '—' }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $partido->equipoVisitante->confederacion ?? '' }}</div>
        </div>
    </div>

    <div style="font-size:11px;color:var(--muted);text-align:center;margin-top:10px">
        <i class="ti ti-map-pin" style="font-size:11px"></i>
        {{ $partido->estadio }}, {{ $partido->ciudad }}, {{ $partido->pais_sede }}
    </div>
</div>
@empty
<div style="text-align:center;padding:60px 20px;color:var(--muted)">
    <i class="ti ti-ball-football" style="font-size:48px;display:block;margin-bottom:12px"></i>
    No hay partidos registrados aún.
</div>
@endforelse
@endsection

@section('scripts')
<script>
function filtrar(tipo, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.partido-card').forEach(c => {
        const f = c.dataset.filtro;
        const show = tipo === 'todos'
            || (tipo === 'pendientes'   && f === 'pendiente')
            || (tipo === 'registrados' && f === 'registrado')
            || (tipo === 'finalizados' && f === 'finalizado');
        c.style.display = show ? 'block' : 'none';
    });
}
</script>
@endsection
