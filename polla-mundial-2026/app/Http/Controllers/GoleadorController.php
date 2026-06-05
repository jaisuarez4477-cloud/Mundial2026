<?php
namespace App\Http\Controllers;

use App\Models\Goleador;
use App\Models\Equipo;
use App\Models\PrediccionDesempate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoleadorController extends Controller
{
    /* Premios individuales válidos (excluye 'ninguno') */
    public const PREMIOS = [
        'bota_oro'     => 'Bota de Oro',
        'bota_plata'   => 'Bota de Plata',
        'bota_bronce'  => 'Bota de Bronce',
        'balon_oro'    => 'Balón de Oro',
        'balon_plata'  => 'Balón de Plata',
        'balon_bronce' => 'Balón de Bronce',
    ];

    public function index()
    {
        $goleadores = Goleador::with('equipo')
                        ->orderByDesc('goles')
                        ->orderByDesc('asistencias')
                        ->get();
        $equipos = Equipo::orderBy('nombre')->get();
        $premios = self::PREMIOS;
        return view('admin.goleadores', compact('goleadores', 'equipos', 'premios'));
    }

    public function crear(Request $request)
    {
        $request->validate([
            'equipo_id'   => 'required|exists:equipos,id',
            'nombre'      => 'required|string|max:100',
            'apellido'    => 'required|string|max:100',
            'goles'       => 'required|integer|min:0|max:50',
            'asistencias' => 'required|integer|min:0|max:50',
            'minutos'     => 'required|integer|min:0|max:1200',
            'tipo_premio' => 'required|in:ninguno,' . implode(',', array_keys(self::PREMIOS)),
        ], [
            'equipo_id.required' => 'Selecciona el equipo del jugador.',
        ]);

        $this->validarPremioUnico($request->tipo_premio);

        Goleador::create([
            'equipo_id'   => $request->equipo_id,
            'nombre'      => ucfirst(trim($request->nombre)),
            'apellido'    => ucfirst(trim($request->apellido)),
            'goles'       => $request->goles,
            'asistencias' => $request->asistencias,
            'minutos'     => $request->minutos,
            'tipo_premio' => $request->tipo_premio,
        ]);

        return back()->with('success', 'Goleador registrado correctamente.');
    }

    public function editar(Request $request, $id)
    {
        $request->validate([
            'equipo_id'   => 'required|exists:equipos,id',
            'nombre'      => 'required|string|max:100',
            'apellido'    => 'required|string|max:100',
            'goles'       => 'required|integer|min:0|max:50',
            'asistencias' => 'required|integer|min:0|max:50',
            'minutos'     => 'required|integer|min:0|max:1200',
            'tipo_premio' => 'required|in:ninguno,' . implode(',', array_keys(self::PREMIOS)),
        ]);

        $this->validarPremioUnico($request->tipo_premio, $id);

        Goleador::findOrFail($id)->update([
            'equipo_id'   => $request->equipo_id,
            'nombre'      => ucfirst(trim($request->nombre)),
            'apellido'    => ucfirst(trim($request->apellido)),
            'goles'       => $request->goles,
            'asistencias' => $request->asistencias,
            'minutos'     => $request->minutos,
            'tipo_premio' => $request->tipo_premio,
        ]);

        return back()->with('success', 'Goleador actualizado.');
    }

    public function eliminar($id)
    {
        Goleador::findOrFail($id)->delete();
        return back()->with('success', 'Goleador eliminado.');
    }

    /* ══════════════════════════════════════════
       EVALUAR PREDICCIONES DE DESEMPATE
       Compara el jugador premiado con lo que predijeron
       los participantes (por nombre) y marca correcto=1/0.
    ══════════════════════════════════════════ */
    public function evaluarDesempate($tipo)
    {
        if (!array_key_exists($tipo, self::PREMIOS)) {
            return back()->withErrors(['msg' => 'Tipo de premio no válido.']);
        }

        // Buscar al jugador que tiene asignado ese premio
        $ganador = Goleador::where('tipo_premio', $tipo)->first();
        if (!$ganador) {
            return back()->withErrors(['msg' => 'Aún no has asignado el premio "' . self::PREMIOS[$tipo] . '" a ningún jugador.']);
        }

        $nombreGanador = mb_strtolower(trim($ganador->nombre . ' ' . $ganador->apellido));

        $predicciones = PrediccionDesempate::where('tipo', $tipo)->get();
        $aciertos = 0;

        foreach ($predicciones as $pred) {
            $nombrePred = mb_strtolower(trim($pred->jugador_nombre));
            // Acierto si el nombre del premiado contiene lo que el usuario escribió, o viceversa
            $acerto = str_contains($nombreGanador, $nombrePred) || str_contains($nombrePred, $nombreGanador);
            $pred->update(['correcto' => $acerto ? 1 : 0]);
            if ($acerto) $aciertos++;
        }

        return back()->with('success',
            "Predicciones de \"" . self::PREMIOS[$tipo] . "\" evaluadas. {$aciertos} acierto(s) sobre " . $predicciones->count() . " predicción(es).");
    }

    /* Garantiza que un premio (oro/plata/bronce) lo tenga un solo jugador */
    private function validarPremioUnico(string $tipo, $exceptoId = null): void
    {
        if ($tipo === 'ninguno') return;

        $query = Goleador::where('tipo_premio', $tipo);
        if ($exceptoId) $query->where('id', '!=', $exceptoId);

        if ($query->exists()) {
            // Liberar el premio del jugador anterior para reasignarlo
            $query->update(['tipo_premio' => 'ninguno']);
        }
    }
}
