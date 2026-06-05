@extends('layouts.app')
@section('title','Usuarios')
@section('page-title','Gestión de Usuarios')

@section('content')
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <h2 style="font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:1px">
            <i class="ti ti-users" style="font-size:18px;vertical-align:-2px;margin-right:8px;color:var(--gold)"></i>
            Participantes registrados
        </h2>
        <div style="display:flex;gap:8px">
            <input type="text" class="form-input-dark" placeholder="Buscar participante..." id="buscador"
                   style="width:220px" oninput="buscar(this.value)">
            <span class="badge badge-gold" style="font-size:12px;padding:6px 14px">
                {{ $usuarios->count() }} total
            </span>
        </div>
    </div>
    <div class="table-wrap">
        <table id="tabla-usuarios">
            <thead>
                <tr>
                    <th>Participante</th>
                    <th>Correo</th>
                    <th>WhatsApp</th>
                    <th style="text-align:right">Puntos</th>
                    <th style="text-align:center">Ranking</th>
                    <th style="text-align:center">Estado</th>
                    <th style="text-align:center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                <tr data-nombre="{{ strtolower($u->nombre_completo) }}" data-email="{{ strtolower($u->email) }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:34px;height:34px;border-radius:50%;background:rgba(200,146,42,.15);border:1.5px solid var(--gold);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:var(--gold);flex-shrink:0">
                                {{ strtoupper(substr($u->nombre,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:500;font-size:13px">{{ $u->nombre_completo }}</div>
                                <div style="font-size:11px;color:var(--muted)">ID #{{ $u->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--muted)">{{ $u->email }}</td>
                    <td style="font-size:13px;color:var(--muted)">{{ $u->whatsapp ?? '—' }}</td>
                    <td style="text-align:right;font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--gold)">
                        {{ $u->puntaje->puntos_total ?? 0 }}
                    </td>
                    <td style="text-align:center;color:var(--muted)">
                        {{ $u->puntaje->posicion_ranking ?? '—' }}
                    </td>
                    <td style="text-align:center">
                        <span class="badge {{ $u->activo ? 'badge-green' : 'badge-red' }}">
                            {{ $u->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <form action="{{ route('admin.usuarios.toggle', $u->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn {{ $u->activo ? 'btn-danger' : 'btn-outline' }} btn-sm">
                                {{ $u->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">No hay participantes registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
function buscar(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#tabla-usuarios tbody tr').forEach(row => {
        const nombre = row.dataset.nombre || '';
        const email  = row.dataset.email  || '';
        row.style.display = (!q || nombre.includes(q) || email.includes(q)) ? '' : 'none';
    });
}
</script>
@endsection
