@extends('layouts.app')
@section('title','Resultados')
@section('page-title','Resultados')

@section('content')
<div class="card">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px;margin-bottom:20px">
        <i class="ti ti-calendar-stats" style="font-size:18px;vertical-align:-2px;margin-right:8px;color:var(--gold)"></i>
        Resultados oficiales
    </h2>
    @if(empty($resultados))
    <div style="text-align:center;padding:40px;color:var(--muted)">
        <i class="ti ti-calendar-off" style="font-size:40px;display:block;margin-bottom:10px"></i>
        Aún no hay resultados registrados.
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Fase / Grupo</th>
                    <th>Local</th>
                    <th style="text-align:center">Resultado</th>
                    <th>Visitante</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultados as $r)
                <tr>
                    <td style="color:var(--muted);font-size:12px">
                        {{ $r->fecha_hora ? date('d M Y · H:i', strtotime($r->fecha_hora)) : '—' }}
                    </td>
                    <td>
                        <div style="font-size:12px">{{ $r->fase }}</div>
                        @if($r->grupo) <div style="font-size:11px;color:var(--muted)">Grupo {{ $r->grupo }}</div> @endif
                    </td>
                    <td>
                        <div style="font-weight:500">{{ $r->equipo_local ?? ($r->llave_referencia ?? '—') }}</div>
                    </td>
                    <td style="text-align:center">
                        @if(!is_null($r->goles_local))
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:3px;color:var(--gold)">
                            {{ $r->goles_local }} – {{ $r->goles_visitante }}
                        </span>
                        @else
                        <span style="color:var(--muted);font-size:13px">Por jugar</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:500">{{ $r->equipo_visitante ?? '—' }}</div>
                    </td>
                    <td>
                        @if($r->finalizado)
                            <span class="badge badge-green">Final</span>
                        @else
                            <span class="badge" style="background:rgba(255,255,255,.05);color:var(--muted);border:1px solid rgba(255,255,255,.1)">Pendiente</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
