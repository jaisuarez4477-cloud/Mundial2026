<?php
namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Pronostico;
use App\Models\Puntaje;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipanteController extends Controller
{
    /* ── Menú principal ─────────────────────────── */
    public function menu()
    {
        $uid       = session('usuario_id');
        $puntaje   = Puntaje::where('usuario_id', $uid)->first();
        $ranking   = DB::select('SELECT * FROM ranking_general LIMIT 10');
        $proximos  = Partido::with(['equipoLocal','equipoVisitante','fase'])
                        ->where('finalizado', 0)
                        ->orderBy('fecha_hora')
                        ->limit(4)
                        ->get();
        $miPosicion = DB::selectOne(
            'SELECT posicion FROM ranking_general WHERE usuario_id = ?', [$uid]
        );

        return view('participante.menu', compact('puntaje','ranking','proximos','miPosicion'));
    }

    /* ── Lista de partidos para pronosticar ─────── */
    public function pronosticos()
    {
        $uid      = session('usuario_id');
        $partidos = Partido::with(['equipoLocal','equipoVisitante','fase','grupo'])
                       ->orderBy('fecha_hora')
                       ->get();

        // Adjuntar pronóstico existente del usuario
        $misProns = Pronostico::where('usuario_id', $uid)
                       ->pluck('goles_visitante', 'partido_id')
                       ->toArray();
        $misPronsLocal = Pronostico::where('usuario_id', $uid)
                       ->pluck('goles_local', 'partido_id')
                       ->toArray();

        return view('participante.pronosticos', compact(
            'partidos','misProns','misPronsLocal'
        ));
    }

    /* ── Guardar / actualizar pronóstico ────────── */
    public function guardarPronostico(Request $request)
    {
        $request->validate([
            'partido_id'      => 'required|exists:partidos,id',
            'goles_local'     => 'required|integer|min:0|max:20',
            'goles_visitante' => 'required|integer|min:0|max:20',
        ]);

        $partido = Partido::findOrFail($request->partido_id);

        if ($partido->finalizado) {
            return back()->withErrors(['msg' => 'Este partido ya finalizó, no puedes modificar tu pronóstico.']);
        }

        if ($partido->fecha_hora <= now()) {
            return back()->withErrors(['msg' => 'El partido ya comenzó, no puedes registrar pronósticos.']);
        }

        Pronostico::updateOrCreate(
            ['usuario_id' => session('usuario_id'), 'partido_id' => $request->partido_id],
            ['goles_local' => $request->goles_local, 'goles_visitante' => $request->goles_visitante, 'calculado' => 0]
        );

        return back()->with('success', '¡Pronóstico guardado correctamente!');
    }

    /* ── Ranking general ────────────────────────── */
    public function ranking()
    {
        $ranking    = DB::select('SELECT * FROM ranking_general');
        $uid        = session('usuario_id');
        $miPosicion = DB::selectOne('SELECT posicion FROM ranking_general WHERE usuario_id = ?', [$uid]);
        return view('participante.ranking', compact('ranking','miPosicion'));
    }

    /* ── Resultados de partidos ─────────────────── */
    public function resultados()
    {
        $resultados = DB::select('SELECT * FROM resultados_partidos ORDER BY fecha_hora DESC');
        return view('participante.resultados', compact('resultados'));
    }
}
