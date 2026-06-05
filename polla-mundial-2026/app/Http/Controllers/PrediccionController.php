<?php
namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Equipo;
use App\Models\PrediccionGrupo;
use App\Models\PrediccionEspecial;
use App\Models\PrediccionDesempate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrediccionController extends Controller
{
    /* Tipos de predicción especial con su puntaje (sección 5.4 del PDF) */
    public const TIPOS_ESPECIALES = [
        'clasificado_32avos'   => ['label' => 'Clasificado a 32avos',   'pts' => 20],
        'clasificado_octavos'  => ['label' => 'Clasificado a octavos',  'pts' => 40],
        'clasificado_cuartos'  => ['label' => 'Clasificado a cuartos',  'pts' => 50],
        'clasificado_semis'    => ['label' => 'Clasificado a semis',    'pts' => 60],
        'clasificado_final'    => ['label' => 'Clasificado a la final', 'pts' => 100],
        'tercer_puesto'        => ['label' => 'Tercer puesto',          'pts' => 70],
        'subcampeon'           => ['label' => 'Subcampeón',             'pts' => 120],
        'campeon'              => ['label' => 'Campeón',                'pts' => 200],
    ];

    /* Tipos de predicción de desempate (premios individuales, sección 5.5) */
    public const TIPOS_DESEMPATE = [
        'bota_oro'     => 'Bota de Oro',
        'bota_plata'   => 'Bota de Plata',
        'bota_bronce'  => 'Bota de Bronce',
        'balon_oro'    => 'Balón de Oro',
        'balon_plata'  => 'Balón de Plata',
        'balon_bronce' => 'Balón de Bronce',
    ];

    /* ══════════════════════════════════════════
       PREDICCIONES DE GRUPOS (posición 1-4)
    ══════════════════════════════════════════ */
    public function grupos()
    {
        $uid    = session('usuario_id');
        $grupos = Grupo::with('equipos')->orderBy('nombre')->get();

        // Predicciones existentes del usuario: [grupo_id][equipo_id] = posicion_pred
        $misPred = PrediccionGrupo::where('usuario_id', $uid)
                    ->get()
                    ->groupBy('grupo_id')
                    ->map(fn($items) => $items->pluck('posicion_pred', 'equipo_id'));

        return view('participante.predicciones_grupos', compact('grupos', 'misPred'));
    }

    public function guardarGrupo(Request $request)
    {
        $request->validate([
            'grupo_id'    => 'required|exists:grupos,id',
            'posiciones'  => 'required|array',
            'posiciones.*'=> 'nullable|integer|min:1|max:4',
        ]);

        $uid    = session('usuario_id');
        $grupoId = $request->grupo_id;

        // Validar que no se repitan posiciones dentro del grupo
        $usadas = array_filter($request->posiciones, fn($p) => $p !== null && $p !== '');
        if (count($usadas) !== count(array_unique($usadas))) {
            return back()->withErrors(['msg' => 'No puedes repetir la misma posición dentro del grupo.']);
        }

        // Bloqueo: si ya se calcularon puntos no se permite modificar
        $calculado = PrediccionGrupo::where('usuario_id', $uid)
                       ->where('grupo_id', $grupoId)
                       ->where('calculado', 1)
                       ->exists();
        if ($calculado) {
            return back()->withErrors(['msg' => 'Este grupo ya fue calculado, no puedes modificar tus predicciones.']);
        }

        foreach ($request->posiciones as $equipoId => $posicion) {
            if ($posicion === null || $posicion === '') {
                // Si dejó vacío, eliminar predicción previa de ese equipo
                PrediccionGrupo::where('usuario_id', $uid)
                    ->where('grupo_id', $grupoId)
                    ->where('equipo_id', $equipoId)
                    ->delete();
                continue;
            }

            PrediccionGrupo::updateOrCreate(
                ['usuario_id' => $uid, 'grupo_id' => $grupoId, 'equipo_id' => $equipoId],
                ['posicion_pred' => $posicion, 'puntos_obtenidos' => 0, 'calculado' => 0]
            );
        }

        return back()->with('success', 'Predicción del grupo guardada correctamente.');
    }

    /* ══════════════════════════════════════════
       PREDICCIONES ESPECIALES (clasificados, campeón...)
    ══════════════════════════════════════════ */
    public function especiales()
    {
        $uid     = session('usuario_id');
        $equipos = Equipo::orderBy('nombre')->get();
        $tipos   = self::TIPOS_ESPECIALES;

        // Predicciones existentes: lista del usuario
        $misPred = PrediccionEspecial::with('equipo')
                    ->where('usuario_id', $uid)
                    ->get()
                    ->groupBy('tipo');

        return view('participante.predicciones_especiales', compact('equipos', 'tipos', 'misPred'));
    }

    public function guardarEspecial(Request $request)
    {
        $tipos = array_keys(self::TIPOS_ESPECIALES);

        $request->validate([
            'tipo'      => 'required|in:' . implode(',', $tipos),
            'equipo_id' => 'required|exists:equipos,id',
        ]);

        $uid = session('usuario_id');

        // Evitar duplicado exacto (mismo usuario, tipo y equipo)
        $existe = PrediccionEspecial::where('usuario_id', $uid)
                    ->where('tipo', $request->tipo)
                    ->where('equipo_id', $request->equipo_id)
                    ->exists();
        if ($existe) {
            return back()->withErrors(['msg' => 'Ya registraste ese equipo en esa categoría.']);
        }

        // Para tipos de selección única (campeón, subcampeón, tercer puesto) solo 1 equipo
        $unicos = ['campeon', 'subcampeon', 'tercer_puesto'];
        if (in_array($request->tipo, $unicos)) {
            PrediccionEspecial::where('usuario_id', $uid)
                ->where('tipo', $request->tipo)
                ->where('calculado', 0)
                ->delete();
        }

        PrediccionEspecial::create([
            'usuario_id'       => $uid,
            'equipo_id'        => $request->equipo_id,
            'tipo'             => $request->tipo,
            'puntos_obtenidos' => 0,
            'calculado'        => 0,
        ]);

        return back()->with('success', 'Predicción especial guardada correctamente.');
    }

    public function eliminarEspecial($id)
    {
        $pred = PrediccionEspecial::where('id', $id)
                  ->where('usuario_id', session('usuario_id'))
                  ->firstOrFail();

        if ($pred->calculado) {
            return back()->withErrors(['msg' => 'No puedes eliminar una predicción ya calculada.']);
        }

        $pred->delete();
        return back()->with('success', 'Predicción especial eliminada.');
    }

    /* ══════════════════════════════════════════
       PREDICCIONES DE DESEMPATE (bota/balón de oro...)
    ══════════════════════════════════════════ */
    public function desempate()
    {
        $uid   = session('usuario_id');
        $tipos = self::TIPOS_DESEMPATE;

        // Predicciones existentes: [tipo] = registro
        $misPred = PrediccionDesempate::where('usuario_id', $uid)
                    ->get()
                    ->keyBy('tipo');

        return view('participante.predicciones_desempate', compact('tipos', 'misPred'));
    }

    public function guardarDesempate(Request $request)
    {
        $tipos = array_keys(self::TIPOS_DESEMPATE);

        $request->validate([
            'tipo'           => 'required|in:' . implode(',', $tipos),
            'jugador_nombre' => 'required|string|max:200',
        ]);

        $uid = session('usuario_id');

        // Bloqueo si ya fue evaluada (correcto no es NULL)
        $previa = PrediccionDesempate::where('usuario_id', $uid)
                    ->where('tipo', $request->tipo)
                    ->first();
        if ($previa && !is_null($previa->correcto)) {
            return back()->withErrors(['msg' => 'Esta predicción ya fue evaluada, no puedes modificarla.']);
        }

        PrediccionDesempate::updateOrCreate(
            ['usuario_id' => $uid, 'tipo' => $request->tipo],
            ['jugador_nombre' => trim($request->jugador_nombre)]
        );

        return back()->with('success', 'Predicción de desempate guardada correctamente.');
    }

    public function eliminarDesempate($id)
    {
        $pred = PrediccionDesempate::where('id', $id)
                  ->where('usuario_id', session('usuario_id'))
                  ->firstOrFail();

        if (!is_null($pred->correcto)) {
            return back()->withErrors(['msg' => 'No puedes eliminar una predicción ya evaluada.']);
        }

        $pred->delete();
        return back()->with('success', 'Predicción de desempate eliminada.');
    }
}
