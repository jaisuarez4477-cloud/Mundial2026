@extends('layouts.app')
@section('title','Inicio')
@section('page-title','Mi Panel')

@section('content')
{{-- Stats personales --}}
<div class="grid-4">
    <div class="stat-card">
        <div class="stat-label">Mis puntos</div>
        <div class="stat-value">{{ $puntaje->puntos_total ?? 0 }}</div>
        <div class="stat-sub">Puntuación total</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Mi posición</div>
        <div class="stat-value" style="color:#E8B84B">{{ $miPosicion->posicion ?? '—' }}</div>
        <div class="stat-sub">En el ranking general</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pts. partidos</div>
        <div class="stat-value" style="color:#5DCAA5">{{ $puntaje->puntos_partidos ?? 0 }}</div>
        <div class="stat-sub">Marcadores y resultados</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pts. eliminatorias</div>
        <div class="stat-value" style="color:#85B7EB">{{ $puntaje->puntos_eliminatorias ?? 0 }}</div>
        <div class="stat-sub">Fases avanzadas</div>
    </div>
</div>

<div class="grid-2">
    {{-- Próximos partidos --}}
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">
                <i class="ti ti-clock" style="font-size:16px;vertical-align:-2px;margin-right:6px;color:var(--gold)"></i>
                Próximos partidos
            </h2>
            <a href="{{ route('pronosticos') }}" style="font-size:12px;color:var(--gold);text-decoration:none">
                Ver todos →
            </a>
        </div>
        @forelse($proximos as $p)
        <div style="border-bottom:1px solid rgba(255,255,255,.06);padding:12px 0;last-child:border-none">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <div style="font-size:11px;color:var(--muted);margin-bottom:6px">
                    {{ $p->fase->nombre }} @if($p->grupo) · Grupo {{ $p->grupo->nombre }} @endif
                </div>
                <div style="font-size:11px;color:var(--gold)">
                    {{ $p->fecha_hora ? $p->fecha_hora->format('d M · H:i') : '' }}
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div style="flex:1;text-align:right;font-size:14px;font-weight:500">
                    {{ $p->equipoLocal->nombre ?? '—' }}
                </div>
                <div style="padding:4px 14px;background:rgba(200,146,42,.12);border:1px solid rgba(200,146,42,.25);border-radius:6px;margin:0 12px;font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px;color:var(--gold)">
                    VS
                </div>
                <div style="flex:1;text-align:left;font-size:14px;font-weight:500">
                    {{ $p->equipoVisitante->nombre ?? '—' }}
                </div>
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:4px;text-align:center">
                <i class="ti ti-map-pin" style="font-size:11px"></i> {{ $p->estadio }}, {{ $p->ciudad }}
            </div>
        </div>
        @empty
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0">
            No hay partidos próximos registrados.
        </p>
        @endforelse
    </div>

    {{-- Top 10 Ranking --}}
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">
                <i class="ti ti-trophy" style="font-size:16px;vertical-align:-2px;margin-right:6px;color:var(--gold)"></i>
                Top 10 ranking
            </h2>
            <a href="{{ route('ranking') }}" style="font-size:12px;color:var(--gold);text-decoration:none">
                Ver completo →
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Participante</th>
                        <th style="text-align:right">Puntos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $r)
                    <tr @if($r->usuario_id == session('usuario_id')) style="background:rgba(200,146,42,.07)" @endif>
                        <td>
                            @if($r->posicion == 1) <span style="font-size:16px">🥇</span>
                            @elseif($r->posicion == 2) <span style="font-size:16px">🥈</span>
                            @elseif($r->posicion == 3) <span style="font-size:16px">🥉</span>
                            @else <span style="color:var(--muted)">{{ $r->posicion }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:500;font-size:13px">{{ $r->nombre_completo }}</div>
                            @if($r->usuario_id == session('usuario_id'))
                                <div style="font-size:11px;color:var(--gold)">← Tú</div>
                            @endif
                        </td>
                        <td style="text-align:right;font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--gold)">
                            {{ $r->puntos_total }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Sistema de puntos (resumen) --}}
<div class="card" style="margin-top:4px">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:16px">
        <i class="ti ti-info-circle" style="font-size:16px;vertical-align:-2px;margin-right:6px;color:var(--gold)"></i>
        Sistema de puntuación
    </h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px">
        @foreach([
            ['Marcador exacto','10 pts','ti-target','gold'],
            ['Resultado correcto','5 pts','ti-check','green'],
            ['Posición en grupo','10 pts','ti-list-numbers','blue'],
            ['Clasificado a 32avos','20 pts','ti-tournament','blue'],
            ['Clasificado a octavos','40 pts','ti-tournament','blue'],
            ['Clasificado a cuartos','50 pts','ti-tournament','gold'],
            ['Clasificado a semis','60 pts','ti-tournament','gold'],
            ['Subcampeón','120 pts','ti-medal','gold'],
            ['Campeón','200 pts','ti-trophy','gold'],
        ] as [$label,$pts,$icon,$color])
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:rgba(255,255,255,.04);border-radius:8px;border:1px solid rgba(255,255,255,.07)">
            <div style="display:flex;align-items:center;gap:8px;font-size:13px">
                <i class="ti {{ $icon }}" style="font-size:16px;color:{{ $color==='gold'?'var(--gold)':($color==='green'?'#5DCAA5':'#85B7EB') }}"></i>
                {{ $label }}
            </div>
            <span class="badge badge-{{ $color==='gold'?'gold':($color==='green'?'green':'blue') }}">{{ $pts }}</span>
        </div>
        @endforeach
    </div>
</div>
@endsection
