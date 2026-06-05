<?php
namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::with('usuario')
                            ->orderByDesc('created_at')
                            ->get();

        // Participantes activos para destinatarios
        $usuarios = Usuario::where('rol_id', 2)->where('activo', 1)
                        ->orderBy('apellido')->get();

        return view('admin.notificaciones', compact('notificaciones', 'usuarios'));
    }

    /* ══════════════════════════════════════════
       CREAR notificación (individual o masiva)
    ══════════════════════════════════════════ */
    public function crear(Request $request)
    {
        $request->validate([
            'destino'    => 'required|in:individual,todos',
            'usuario_id' => 'required_if:destino,individual|nullable|exists:usuarios,id',
            'tipo'       => 'required|in:whatsapp,email,sistema',
            'asunto'     => 'nullable|string|max:200',
            'mensaje'    => 'required|string|max:2000',
        ], [
            'mensaje.required' => 'El mensaje es obligatorio.',
            'usuario_id.required_if' => 'Selecciona el participante destinatario.',
        ]);

        // Definir destinatarios
        if ($request->destino === 'todos') {
            $destinatarios = Usuario::where('rol_id', 2)->where('activo', 1)->pluck('id');
        } else {
            $destinatarios = collect([$request->usuario_id]);
        }

        foreach ($destinatarios as $uid) {
            Notificacion::create([
                'usuario_id' => $uid,
                'tipo'       => $request->tipo,
                'asunto'     => $request->asunto,
                'mensaje'    => $request->mensaje,
                'leida'      => 0,
                'enviada'    => 0,
                'enviada_at' => null,
                'created_at' => now(),
            ]);
        }

        $msg = $request->destino === 'todos'
            ? 'Notificación creada para ' . $destinatarios->count() . ' participante(s).'
            : 'Notificación creada correctamente.';

        return back()->with('success', $msg);
    }

    /* ══════════════════════════════════════════
       MARCAR como enviada (al abrir el enlace WhatsApp)
    ══════════════════════════════════════════ */
    public function marcarEnviada($id)
    {
        $notif = Notificacion::findOrFail($id);
        $notif->update(['enviada' => 1, 'enviada_at' => now()]);
        return back()->with('success', 'Notificación marcada como enviada.');
    }

    public function eliminar($id)
    {
        Notificacion::findOrFail($id)->delete();
        return back()->with('success', 'Notificación eliminada.');
    }

    /* ══════════════════════════════════════════
       Helper estático: arma el enlace wa.me
       Se usa desde la vista para cada notificación WhatsApp.
    ══════════════════════════════════════════ */
    public static function linkWhatsApp(?string $telefono, string $mensaje): ?string
    {
        if (!$telefono) return null;
        // Dejar solo dígitos (wa.me no acepta '+', espacios ni guiones)
        $numero = preg_replace('/\D+/', '', $telefono);
        if ($numero === '') return null;
        return 'https://wa.me/' . $numero . '?text=' . rawurlencode($mensaje);
    }
}
