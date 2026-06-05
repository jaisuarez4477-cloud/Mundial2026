@extends('layouts.app')
@section('title','Ranking')
@section('page-title','Ranking General')

@section('content')
<div class="grid-3" style="margin-bottom:28px">
    @foreach(array_slice($ranking,0,3) as $r)
    <div style="background:var(--card);border:1px solid {{ $r->posicion==1?'rgba(200,146,42,.5)':($r->posicion==2?'rgba(133,183,235,.4)':'rgba(200,146,42,.25)') }};border-radius:14px;padding:24px;text-align:center;position:relative">
        <div style="font-size:36px;margin-bottom:8px">{{ $r->posicion==1?'🥇':($r->posicion==2?'🥈':'🥉') }}</div>
        <div style="width:52px;height:52px;border-radius:50%;background:rgba(200,146,42,.15);border:2px solid var(--gold);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:18px;font-weight:600;color:var(--gold)">
            {{ strtoupper(substr($r->nombre_completo,0,1)) }}
        </div>
        <div style="font-weight:600;font-size:15px;margin-bottom:4px">{{ $r->nombre_completo }}</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:32px;color:var(--gold);letter-spacing:2px">{{ $r->puntos_total }}</div>
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px">puntos</div>
        @if($r->posicion==1)
        <div style="position:absolute;top:12px;right:12px;font-size:11px;font-weight:600;background:rgba(200,146,42,.15);color:var(--gold);border:1px solid rgba(200,146,42,.3);padding:3px 10px;border-radius:20px">Premio 50%</div>
        @elseif($r->posicion==2)
        <div style="position:absolute;top:12px;right:12px;font-size:11px;font-weight:600;background:rgba(133,183,235,.1);color:#85B7EB;border:1px solid rgba(133,183,235,.3);padding:3px 10px;border-radius:20px">Premio 30%</div>
        @else
        <div style="position:absolute;top:12px;right:12px;font-size:11px;font-weight:600;background:rgba(200,146,42,.08);color:#C8922A;border:1px solid rgba(200,146,42,.2);padding:3px 10px;border-radius:20px">Premio 20%</div>
        @endif
    </div>
    @endforeach
</div>

<div class="card">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px;margin-bottom:16px">
        <i class="ti ti-list-numbers" style="font-size:18px;vertical-align:-2px;margin-right:8px;color:var(--gold)"></i>
        Tabla completa
    </h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Participante</th>
                    <th style="text-align:right">Partidos</th>
                    <th style="text-align:right">Grupos</th>
                    <th style="text-align:right">Eliminatorias</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ranking as $r)
                <tr @if($r->usuario_id == session('usuario_id')) style="background:rgba(200,146,42,.07);border-left:3px solid var(--gold)" @endif>
                    <td>
                        @if($r->posicion <= 3)
                            {{ ['🥇','🥈','🥉'][$r->posicion-1] }}
                        @else
                            <span style="color:var(--muted);font-weight:500">{{ $r->posicion }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:500">{{ $r->nombre_completo }}</div>
                        @if($r->usuario_id == session('usuario_id'))
                            <div style="font-size:11px;color:var(--gold)">← Tú</div>
                        @endif
                    </td>
                    <td style="text-align:right;color:var(--muted)">{{ $r->puntos_partidos }}</td>
                    <td style="text-align:right;color:var(--muted)">{{ $r->puntos_grupos }}</td>
                    <td style="text-align:right;color:var(--muted)">{{ $r->puntos_eliminatorias }}</td>
                    <td style="text-align:right;font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--gold)">{{ $r->puntos_total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top:16px">
    <h3 style="font-size:14px;font-weight:600;margin-bottom:12px;color:var(--muted);text-transform:uppercase;letter-spacing:1px">
        <i class="ti ti-info-circle" style="font-size:14px;vertical-align:-1px;margin-right:6px"></i>
        Criterios de desempate
    </h3>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        @foreach(['1. Bota de Oro','2. Bota de Plata','3. Bota de Bronce','4. Balón de Oro','5. Balón de Plata','6. Balón de Bronce'] as $c)
        <span style="font-size:12px;padding:5px 12px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:20px;color:var(--muted)">{{ $c }}</span>
        @endforeach
    </div>
</div>
@endsection
