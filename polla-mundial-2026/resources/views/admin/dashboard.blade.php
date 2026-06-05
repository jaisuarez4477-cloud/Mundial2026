@extends('layouts.app')
@section('title','Dashboard Admin')
@section('page-title','Panel de Administración')

@section('content')
<div class="grid-4" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-label">Participantes</div>
        <div class="stat-value">{{ $stats['usuarios'] }}</div>
        <div class="stat-sub">Registrados y activos</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Partidos jugados</div>
        <div class="stat-value" style="color:#5DCAA5">{{ $stats['jugados'] }}</div>
        <div class="stat-sub">De {{ $stats['partidos'] }} totales</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Partidos pendientes</div>
        <div class="stat-value" style="color:#E8B84B">{{ $stats['pendientes'] }}</div>
        <div class="stat-sub">Sin resultado aún</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pronósticos</div>
        <div class="stat-value" style="color:#85B7EB">{{ $stats['pronos'] }}</div>
        <div class="stat-sub">Total registrados</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">
                <i class="ti ti-ball-football" style="font-size:16px;vertical-align:-2px;margin-right:6px;color:var(--gold)"></i>
                Próximos partidos
            </h2>
            <a href="{{ route('admin.partidos') }}" class="btn btn-outline btn-sm">
                <i class="ti ti-external-link" style="font-size:14px"></i> Gestionar
            </a>
        </div>
        @forelse($proximos as $p)
        <div style="padding:12px 0;border-bottom:1px solid rgba(255,255,255,.06)">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                <span class="badge badge-blue" style="font-size:11px">{{ $p->fase->nombre }}</span>
                <span style="font-size:11px;color:var(--muted)">{{ $p->fecha_hora ? $p->fecha_hora->format('d M · H:i') : '' }}</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:14px;font-weight:500">
                <span style="flex:1;text-align:right">{{ $p->equipoLocal->nombre ?? '—' }}</span>
                <span style="color:var(--gold);font-family:'Bebas Neue',sans-serif;font-size:16px;letter-spacing:2px">VS</span>
                <span style="flex:1">{{ $p->equipoVisitante->nombre ?? '—' }}</span>
            </div>
        </div>
        @empty
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0">No hay partidos pendientes.</p>
        @endforelse
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">
                <i class="ti ti-trophy" style="font-size:16px;vertical-align:-2px;margin-right:6px;color:var(--gold)"></i>
                Top 5 ranking
            </h2>
            <a href="{{ route('ranking') }}" class="btn btn-outline btn-sm">
                <i class="ti ti-external-link" style="font-size:14px"></i> Ver completo
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Participante</th><th style="text-align:right">Puntos</th></tr>
                </thead>
                <tbody>
                    @foreach($ranking as $r)
                    <tr>
                        <td>
                            @if($r->posicion<=3) {{ ['🥇','🥈','🥉'][$r->posicion-1] }}
                            @else <span style="color:var(--muted)">{{ $r->posicion }}</span> @endif
                        </td>
                        <td style="font-weight:500">{{ $r->nombre_completo }}</td>
                        <td style="text-align:right;font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--gold)">{{ $r->puntos_total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
