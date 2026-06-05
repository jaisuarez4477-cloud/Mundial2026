<?php
namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Equipo;
use App\Models\Usuario;
use App\Models\Puntaje;
use App\Models\Pronostico;
use App\Models\Fase;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /* ══════════════════════════════════════════
       DASHBOARD
    ══════════════════════════════════════════ */
    public function dashboard()
    {
        $stats = [
            'usuarios'   => Usuario::where('rol_id', 2)->where('activo', 1)->count(),
            'partidos'   => Partido::count(),
            'jugados'    => Partido::where('finalizado', 1)->count(),
            'pendientes' => Partido::where('finalizado', 0)->count(),
            'pronos'     => Pronostico::count(),
        ];
        $ranking  = DB::select('SELECT * FROM ranking_general LIMIT 5');
        $proximos = Partido::with(['equipoLocal','equipoVisitante'])
                        ->where('finalizado', 0)
                        ->orderBy('fecha_hora')
                        ->limit(5)
                        ->get();
        return view('admin.dashboard', compact('stats','ranking','proximos'));
    }

    /* ══════════════════════════════════════════
       USUARIOS
    ══════════════════════════════════════════ */
    public function usuarios()
    {
        $usuarios = Usuario::with(['rol','puntaje'])
                        ->where('rol_id', 2)
                        ->orderBy('apellido')
                        ->get();
        return view('admin.usuarios', compact('usuarios'));
    }

    public function toggleUsuario($id)
    {
        $u = Usuario::findOrFail($id);
        $u->update(['activo' => !$u->activo]);
        return back()->with('success', 'Estado del usuario actualizado.');
    }

    /* ══════════════════════════════════════════
       EQUIPOS
    ══════════════════════════════════════════ */
    public function equipos()
    {
        $equipos = Equipo::withCount('grupos')->orderBy('nombre')->get();
        $grupos  = Grupo::orderBy('nombre')->get();
        return view('admin.equipos', compact('equipos','grupos'));
    }

    public function crearEquipo(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'nombre_corto'  => 'required|string|max:50',
            'codigo'        => 'required|string|size:3|unique:equipos,codigo',
            'confederacion' => 'required|in:CONMEBOL,UEFA,CONCACAF,CAF,AFC,OFC',
            'bandera_url'   => 'nullable|url|max:255',
        ], [
            'codigo.unique' => 'Ese código FIFA ya existe.',
            'codigo.size'   => 'El código debe tener exactamente 3 letras (ej: COL).',
        ]);

        Equipo::create($request->only([
            'nombre','nombre_corto','codigo','confederacion','bandera_url'
        ]));

        return back()->with('success', 'Equipo creado correctamente.');
    }

    public function editarEquipo(Request $request, $id)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'nombre_corto'  => 'required|string|max:50',
            'codigo'        => 'required|string|size:3|unique:equipos,codigo,'.$id,
            'confederacion' => 'required|in:CONMEBOL,UEFA,CONCACAF,CAF,AFC,OFC',
            'bandera_url'   => 'nullable|url|max:255',
        ]);

        Equipo::findOrFail($id)->update($request->only([
            'nombre','nombre_corto','codigo','confederacion','bandera_url'
        ]));

        return back()->with('success', 'Equipo actualizado.');
    }

    public function eliminarEquipo($id)
    {
        $equipo = Equipo::findOrFail($id);

        // 1. Obtener IDs de partidos del equipo
        $partidosIds = Partido::where('equipo_local_id', $id)
                              ->orWhere('equipo_visitante_id', $id)
                              ->pluck('id')
                              ->toArray();

        // 2. Borrar pronósticos de esos partidos
        if (!empty($partidosIds)) {
            DB::table('pronosticos')->whereIn('partido_id', $partidosIds)->delete();
        }

        // 3. Borrar los partidos
        DB::table('partidos')
            ->where('equipo_local_id', $id)
            ->orWhere('equipo_visitante_id', $id)
            ->delete();

        // 4. Borrar asignaciones de grupo
        DB::table('grupos_equipos')->where('equipo_id', $id)->delete();

        // 5. Borrar el equipo
        $equipo->delete();

        return back()->with('success', 'Equipo eliminado correctamente.');
    }

    /* ══════════════════════════════════════════
       GRUPOS
    ══════════════════════════════════════════ */
    public function grupos()
    {
        $grupos  = Grupo::with('equipos')->orderBy('nombre')->get();
        $equipos = Equipo::orderBy('nombre')->get();
        return view('admin.grupos', compact('grupos','equipos'));
    }

    public function crearGrupo(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|size:1|unique:grupos,nombre',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.size'   => 'El nombre debe ser una sola letra (A, B, C...).',
            'nombre.unique' => 'Ese grupo ya existe.',
        ]);

        Grupo::create([
            'nombre'      => strtoupper($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return back()->with('success', 'Grupo creado correctamente.');
    }

    public function eliminarGrupo($id)
    {
        $grupo = Grupo::findOrFail($id);
        if ($grupo->partidos()->count() > 0) {
            return back()->withErrors(['msg' => 'No puedes eliminar un grupo con partidos registrados.']);
        }
        DB::table('grupos_equipos')->where('grupo_id', $id)->delete();
        $grupo->delete();
        return back()->with('success', 'Grupo eliminado.');
    }

    public function asignarEquipoGrupo(Request $request)
    {
        $request->validate([
            'grupo_id'  => 'required|exists:grupos,id',
            'equipo_id' => 'required|exists:equipos,id',
        ]);

        $existe = DB::table('grupos_equipos')
                    ->where('grupo_id',  $request->grupo_id)
                    ->where('equipo_id', $request->equipo_id)
                    ->exists();

        if ($existe) {
            return back()->withErrors(['msg' => 'Ese equipo ya está en ese grupo.']);
        }

        $count = DB::table('grupos_equipos')
                   ->where('grupo_id', $request->grupo_id)
                   ->count();

        if ($count >= 4) {
            return back()->withErrors(['msg' => 'El grupo ya tiene 4 equipos (máximo permitido).']);
        }

        DB::table('grupos_equipos')->insert([
            'grupo_id'  => $request->grupo_id,
            'equipo_id' => $request->equipo_id,
        ]);

        return back()->with('success', 'Equipo asignado al grupo correctamente.');
    }

    public function quitarEquipoGrupo($grupoId, $equipoId)
    {
        DB::table('grupos_equipos')
            ->where('grupo_id', $grupoId)
            ->where('equipo_id', $equipoId)
            ->delete();
        return back()->with('success', 'Equipo removido del grupo.');
    }

    /* ══════════════════════════════════════════
       PARTIDOS
    ══════════════════════════════════════════ */
    public function partidos()
    {
        $partidos = Partido::with(['equipoLocal','equipoVisitante','fase','grupo'])
                        ->orderBy('fecha_hora')->get();
        $equipos  = Equipo::orderBy('nombre')->get();
        $fases    = Fase::orderBy('orden')->get();
        $grupos   = Grupo::orderBy('nombre')->get();
        return view('admin.partidos', compact('partidos','equipos','fases','grupos'));
    }

    public function crearPartido(Request $request)
    {
        $request->validate([
            'fase_id'             => 'required|exists:fases,id',
            'grupo_id'            => 'nullable|exists:grupos,id',
            'equipo_local_id'     => 'nullable|exists:equipos,id',
            'equipo_visitante_id' => 'nullable|exists:equipos,id',
            'fecha_hora'          => 'required|date',
            'estadio'             => 'required|string|max:150',
            'ciudad'              => 'required|string|max:100',
            'pais_sede'           => 'required|string|max:80',
            'llave_referencia'    => 'nullable|string|max:50',
        ], [
            'fase_id.required'    => 'Selecciona una fase.',
            'fecha_hora.required' => 'La fecha y hora son obligatorias.',
            'estadio.required'    => 'El estadio es obligatorio.',
        ]);

        if ($request->equipo_local_id &&
            $request->equipo_local_id === $request->equipo_visitante_id) {
            return back()->withErrors(['msg' => 'El equipo local y visitante no pueden ser el mismo.']);
        }

        Partido::create([
            'fase_id'             => $request->fase_id,
            'grupo_id'            => $request->grupo_id,
            'equipo_local_id'     => $request->equipo_local_id,
            'equipo_visitante_id' => $request->equipo_visitante_id,
            'fecha_hora'          => $request->fecha_hora,
            'estadio'             => $request->estadio,
            'ciudad'              => $request->ciudad,
            'pais_sede'           => $request->pais_sede,
            'llave_referencia'    => $request->llave_referencia,
            'finalizado'          => 0,
        ]);

        return back()->with('success', 'Partido creado correctamente.');
    }

    public function eliminarPartido($id)
    {
        // 1. Borrar pronósticos del partido
        DB::table('pronosticos')->where('partido_id', $id)->delete();

        // 2. Borrar el partido
        Partido::findOrFail($id)->delete();

        return back()->with('success', 'Partido eliminado correctamente.');
    }

    public function registrarResultado(Request $request, $id)
    {
        $request->validate([
            'goles_local'     => 'required|integer|min:0|max:20',
            'goles_visitante' => 'required|integer|min:0|max:20',
        ]);

        $partido = Partido::findOrFail($id);
        $partido->update([
            'goles_local'     => $request->goles_local,
            'goles_visitante' => $request->goles_visitante,
            'finalizado'      => 1,
        ]);

        $this->calcularPuntos($partido);

        return back()->with('success', "Resultado registrado: {$request->goles_local} - {$request->goles_visitante}");
    }

    /* ══════════════════════════════════════════
       LÓGICA INTERNA — PUNTOS Y RANKING
    ══════════════════════════════════════════ */
    private function calcularPuntos(Partido $partido)
    {
        $pronosticos = Pronostico::where('partido_id', $partido->id)
                                  ->where('calculado', 0)
                                  ->get();

        foreach ($pronosticos as $p) {
            $puntos = 0;

            // Cast a int para evitar comparación estricta string vs integer
            $realLocal      = (int) $partido->goles_local;
            $realVisitante  = (int) $partido->goles_visitante;
            $pronoLocal     = (int) $p->goles_local;
            $pronoVisitante = (int) $p->goles_visitante;

            if ($pronoLocal === $realLocal && $pronoVisitante === $realVisitante) {
                // Marcador exacto → 10 puntos
                $puntos = 10;
            } else {
                // Verificar si acertó resultado (ganador o empate)
                $resMundo = $this->resultado($realLocal, $realVisitante);
                $resProno = $this->resultado($pronoLocal, $pronoVisitante);
                if ($resMundo === $resProno) {
                    // Resultado correcto → 5 puntos
                    $puntos = 5;
                }
            }

            $p->update(['puntos_obtenidos' => $puntos, 'calculado' => 1]);

            $puntaje = Puntaje::firstOrCreate(
                ['usuario_id' => $p->usuario_id],
                ['puntos_partidos' => 0, 'puntos_grupos' => 0,
                 'puntos_eliminatorias' => 0, 'puntos_total' => 0]
            );
            $puntaje->increment('puntos_partidos', $puntos);
            $puntaje->increment('puntos_total', $puntos);
        }

        $this->recalcularPosiciones();
    }

    private function resultado($local, $visitante): string
    {
        if ($local > $visitante) return 'local';
        if ($local < $visitante) return 'visitante';
        return 'empate';
    }

    private function recalcularPosiciones()
    {
        $puntajes = Puntaje::orderByDesc('puntos_total')->get();
        foreach ($puntajes as $i => $p) {
            $p->update(['posicion_ranking' => $i + 1]);
        }
    }
}

