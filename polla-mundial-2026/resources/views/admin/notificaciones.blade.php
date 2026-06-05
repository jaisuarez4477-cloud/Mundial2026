@extends('layouts.app')
@section('title','Notificaciones')
@section('page-title','Notificaciones')

@section('content')
<div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">

  {{-- Formulario crear notificación --}}
  <div class="card" style="width:340px;flex-shrink:0">
    <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;margin-bottom:18px">
      <i class="ti ti-send" style="color:var(--gold);margin-right:6px"></i>Nueva notificación
    </h2>
    <form action="{{ route('admin.notificaciones.crear') }}" method="POST">
      @csrf
      <div style="margin-bottom:14px">
        <label class="form-label">Destinatario *</label>
        <select name="destino" id="destino" class="form-input-dark" onchange="toggleUsuario()" required>
          <option value="individual">Un participante</option>
          <option value="todos">Todos los participantes</option>
        </select>
      </div>
      <div style="margin-bottom:14px" id="box-usuario">
        <label class="form-label">Participante *</label>
        <select name="usuario_id" class="form-input-dark">
          <option value="">Seleccionar...</option>
          @foreach($usuarios as $u)
          <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }} @if($u->whatsapp)({{ $u->whatsapp }})@endif</option>
          @endforeach
        </select>
      </div>
      <div style="margin-bottom:14px">
        <label class="form-label">Canal *</label>
        <select name="tipo" class="form-input-dark" required>
          <option value="whatsapp">WhatsApp</option>
          <option value="email">Email</option>
          <option value="sistema">Sistema (interna)</option>
        </select>
      </div>
      <div style="margin-bottom:14px">
        <label class="form-label">Asunto</label>
        <input type="text" name="asunto" class="form-input-dark" placeholder="Recordatorio de pronósticos" value="{{ old('asunto') }}">
      </div>
      <div style="margin-bottom:18px">
        <label class="form-label">Mensaje *</label>
        <textarea name="mensaje" class="form-input-dark" rows="4"
                  placeholder="Escribe el mensaje..." required>{{ old('mensaje') }}</textarea>
      </div>
      <button type="submit" class="btn btn-gold" style="width:100%">
        <i class="ti ti-circle-check" style="font-size:15px"></i> Crear notificación
      </button>
    </form>
    <p style="font-size:11px;color:var(--muted);line-height:1.6;margin-top:14px;padding-top:12px;border-top:1px solid var(--border)">
      <i class="ti ti-brand-whatsapp" style="color:#25D366"></i>
      Para WhatsApp, al pulsar <strong>Enviar</strong> se abrirá WhatsApp con el mensaje listo. El participante debe tener su número registrado.
    </p>
  </div>

  {{-- Historial de notificaciones --}}
  <div class="card" style="flex:1;min-width:0">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px">
      <h2 style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px">
        <i class="ti ti-history" style="color:var(--gold);margin-right:6px"></i>Historial
      </h2>
      <span class="badge badge-gold" style="font-size:12px;padding:6px 14px">{{ $notificaciones->count() }} mensajes</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Destinatario</th>
            <th>Canal</th>
            <th>Mensaje</th>
            <th style="text-align:center">Estado</th>
            <th style="text-align:center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($notificaciones as $n)
          @php
            $wa = $n->tipo === 'whatsapp'
                ? \App\Http\Controllers\NotificacionController::linkWhatsApp(
                    optional($n->usuario)->whatsapp,
                    ($n->asunto ? '*'.$n->asunto.'*'."\n" : '').$n->mensaje
                  )
                : null;
            $iconCanal = ['whatsapp'=>'ti-brand-whatsapp','email'=>'ti-mail','sistema'=>'ti-bell'];
            $colorCanal= ['whatsapp'=>'#25D366','email'=>'#85B7EB','sistema'=>'var(--gold)'];
          @endphp
          <tr>
            <td>
              <div style="font-weight:500;font-size:13px">{{ optional($n->usuario)->nombre }} {{ optional($n->usuario)->apellido }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ optional($n->usuario)->whatsapp ?? optional($n->usuario)->email }}</div>
            </td>
            <td>
              <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:{{ $colorCanal[$n->tipo] ?? 'var(--muted)' }}">
                <i class="ti {{ $iconCanal[$n->tipo] ?? 'ti-bell' }}"></i> {{ ucfirst($n->tipo) }}
              </span>
            </td>
            <td style="max-width:300px">
              @if($n->asunto)<div style="font-weight:500;font-size:12px">{{ $n->asunto }}</div>@endif
              <div style="font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px">{{ $n->mensaje }}</div>
            </td>
            <td style="text-align:center">
              @if($n->enviada)
                <span class="badge badge-green" style="font-size:10px">Enviada</span>
              @else
                <span class="badge badge-gold" style="font-size:10px">Pendiente</span>
              @endif
            </td>
            <td style="text-align:center">
              <div style="display:flex;gap:6px;justify-content:center;align-items:center">
                @if($wa)
                  <a href="{{ $wa }}" target="_blank" rel="noopener"
                     onclick="marcarEnviada({{ $n->id }})"
                     class="btn btn-sm" style="background:#25D366;color:#0A0E1A" title="Abrir WhatsApp">
                    <i class="ti ti-brand-whatsapp" style="font-size:14px"></i> Enviar
                  </a>
                @elseif($n->tipo === 'whatsapp')
                  <span style="font-size:11px;color:#ff9999" title="El participante no tiene WhatsApp registrado">Sin número</span>
                @endif
                <form action="{{ route('admin.notificaciones.eliminar', $n->id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar esta notificación?')">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash" style="font-size:13px"></i></button>
                </form>
              </div>
              <form id="enviada-{{ $n->id }}" action="{{ route('admin.notificaciones.enviada', $n->id) }}" method="POST" style="display:none">
                @csrf
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted)">No hay notificaciones registradas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function toggleUsuario() {
    var destino = document.getElementById('destino').value;
    document.getElementById('box-usuario').style.display = destino === 'todos' ? 'none' : 'block';
}
// Marca como enviada (en segundo plano) cuando se abre el enlace de WhatsApp
function marcarEnviada(id) {
    var f = document.getElementById('enviada-' + id);
    if (f) { setTimeout(function(){ f.submit(); }, 600); }
}
</script>
@endsection
