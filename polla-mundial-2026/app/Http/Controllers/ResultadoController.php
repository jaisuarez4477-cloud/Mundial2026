<?php
namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Equipo;
use App\Models\GrupoEquipo;
use App\Models\Puntaje;
use App\Models\PrediccionGrupo;
use App\Models\PrediccionEspecial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultadoController extends Controller
{
    /* Puntaje oficial por tipo de predicción especial (sección 5.4 del PDF) */
    public const PUNTOS_ESPECIALES = [
        'clasificado_32avos'  => 20,
        'clasificado_octavos' => 40,
        'clasificado_cuartos' => 50,
        'clasificado_semis'   => 60,
        'clasificado_final'   => 100,
        'tercer_puesto'       => 70,
        'subcampeon'          => 120,
        'campeon'             => 200,
    ];

    public const LABEL_ESPECIALES = [
        'clasificado_32avos'  => 'Clasificado a 32avos',
        'clasificado_octavos' => 'Clasificado a octavos',
        'clasificado_cuartos' => 'Clasificado a cuartos',
        'clasificado_semis'   => 'Clasificado a semis',
        'clasificado_final'   => 'Clasificado a la final',
        'tercer_puesto'       => 'Tercer puesto',
        'subcampeon'          => 'Subcampeón',
        'campeon'             => 'Campeón',
    ];

    /* Puntos por acertar una posición final dentro de un grupo */
    public const PUNTOS_POSICION_GRUPO = 10;

    /* ══════════════════════════════════════════════════════════
       PANTALLA PRINCIPAL — resultados oficiales del torneo
    ══════════════════════════════════════════════════════════ */
    public function index()
    {
        $grupos = Grupo::with(['equipos' => function ($q) {
            $q->orderByRaw('COALESCE(grupos_equipos.posicion_final, 99)');
        }])->orderBy('nombre')->get();

        $equipos = Equipo::orderBy('nombre')->get();
        $tipos   = self::LABEL_ESPECIALES;
        $puntos  = self::PUNTOS_ESPECIALES;

        // Equipos que ya fueron marcados como clasificados por tipo (resultado oficial)
        $oficiales = DB::table('resultados_especiales')
                        ->get()
                        ->groupBy('tipo')
                        ->map(fn ($rows) => $rows->pluck('equipo_id')->all());

        return view('admin.resultados', compact('grupos', 'equipos', 'tipos', 'puntos', 'oficiales'));
    }

    /* ══════════════════════════════════════════════════════════
       1) POSICIONES FINALES DE UN GRUPO
          Guarda posicion_final en grupos_equipos y calcula
          10 pts por cada participante que acertó la posición.
    ══════════════════════════════════════════════════════════ */
    public function guardarPosicionesGrupo(Request $request, $grupoId)
    {
        $grupo = Grupo::with('equipos')->findOrFail($grupoId);

        $request->validate([
            'posiciones'   => 'required|array',
            'posiciones.*' => 'nullable|integer|min:1|max:4',
        ]);

        // Validar que no se repitan posiciones
        $usadas = array_filter($request->posiciones, fn ($p) => $p !== null && $p !== '');
        if (count($usadas) !== count(array_unique($usadas))) {
            return back()->withErrors(['msg' => 'No puedes repetir la misma posición en el grupo.']);
        }

        DB::transaction(function () use ($request, $grupoId) {
            // 1. Guardar la posición final real de cada equipo
            foreach ($request->posiciones as $equipoId => $posicion) {
                GrupoEquipo::where('grupo_id', $grupoId)
                    ->where('equipo_id', $equipoId)
                    ->update(['posicion_final' => ($posicion === '' ? null : $posicion)]);
            }

            // 2. Calcular puntos de las predicciones de grupo de los participantes
            $this->calcularPuntosGrupo($grupoId);
        });

        return back()->with('success', 'Posiciones del grupo guardadas y puntos calculados.');
    }

    /* Compara la posición real con la pronosticada por cada usuario */
    private function calcularPuntosGrupo($grupoId): void
    {
        // Posiciones reales: [equipo_id => posicion_final]
        $reales = GrupoEquipo::where('grupo_id', $grupoId)
                    ->whereNotNull('posicion_final')
                    ->pluck('posicion_final', 'equipo_id');

        if ($reales->isEmpty()) return;

        // Predicciones aún no calculadas de este grupo
        $predicciones = PrediccionGrupo::where('grupo_id', $grupoId)
                            ->where('calculado', 0)
                            ->get();

        foreach ($predicciones as $pred) {
            $posReal = $reales[$pred->equipo_id] ?? null;

            // Solo calcular si ya existe posición real para ese equipo
            if ($posReal === null) continue;

            $puntos = ((int) $pred->posicion_pred === (int) $posReal)
                ? self::PUNTOS_POSICION_GRUPO
                : 0;

            $pred->update(['puntos_obtenidos' => $puntos, 'calculado' => 1]);

            if ($puntos > 0) {
                $this->sumarPuntaje($pred->usuario_id, 'puntos_grupos', $puntos);
            }
        }

        $this->recalcularPosiciones();
    }

    /* ══════════════════════════════════════════════════════════
       2) RESULTADOS DE PREDICCIONES ESPECIALES
          El admin marca qué equipos clasificaron oficialmente
          a una fase (o quién es campeón, etc.) y se calculan
          los puntos de los participantes que lo acertaron.
    ══════════════════════════════════════════════════════════ */
    public function guardarEspecial(Request $request)
    {
        $tipos = array_keys(self::PUNTOS_ESPECIALES);

        $request->validate([
            'tipo'        => 'required|in:' . implode(',', $tipos),
            'equipos'     => 'nullable|array',
            'equipos.*'   => 'integer|exists:equipos,id',
        ]);

        $tipo    = $request->tipo;
        $equipos = $request->equipos ?? [];

        DB::transaction(function () use ($tipo, $equipos) {
            // 1. Refrescar la lista oficial de equipos clasificados para ese tipo
            DB::table('resultados_especiales')->where('tipo', $tipo)->delete();
            foreach ($equipos as $equipoId) {
                DB::table('resultados_especiales')->insert([
                    'tipo'       => $tipo,
                    'equipo_id'  => $equipoId,
                    'created_at' => now(),
                ]);
            }

            // 2. Recalcular puntos de las predicciones especiales de ese tipo
            $this->calcularPuntosEspeciales($tipo, $equipos);
        });

        return back()->with('success',
            'Resultado oficial de "' . self::LABEL_ESPECIALES[$tipo] . '" guardado y puntos calculados.');
    }

    /* Calcula puntos para todas las predicciones de un tipo dado */
    private function calcularPuntosEspeciales(string $tipo, array $equiposCorrectos): void
    {
        $puntosTipo = self::PUNTOS_ESPECIALES[$tipo];

        $predicciones = PrediccionEspecial::where('tipo', $tipo)
                            ->where('calculado', 0)
                            ->get();

        foreach ($predicciones as $pred) {
            $acerto = in_array($pred->equipo_id, $equiposCorrectos);
            $puntos = $acerto ? $puntosTipo : 0;

            $pred->update(['puntos_obtenidos' => $puntos, 'calculado' => 1]);

            if ($puntos > 0) {
                $this->sumarPuntaje($pred->usuario_id, 'puntos_eliminatorias', $puntos);
            }
        }

        $this->recalcularPosiciones();
    }

    /* ══════════════════════════════════════════════════════════
       HELPERS — puntaje y ranking (mismo patrón que AdminController)
    ══════════════════════════════════════════════════════════ */
    private function sumarPuntaje(int $usuarioId, string $columna, int $puntos): void
    {
        $puntaje = Puntaje::firstOrCreate(
            ['usuario_id' => $usuarioId],
            ['puntos_partidos' => 0, 'puntos_grupos' => 0,
             'puntos_eliminatorias' => 0, 'puntos_total' => 0]
        );
        $puntaje->increment($columna, $puntos);
        $puntaje->increment('puntos_total', $puntos);
    }

    private function recalcularPosiciones(): void
    {
        $puntajes = Puntaje::orderByDesc('puntos_total')->get();
        foreach ($puntajes as $i => $p) {
            $p->update(['posicion_ranking' => $i + 1]);
        }
    }
}
