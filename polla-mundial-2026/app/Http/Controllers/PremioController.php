<?php
namespace App\Http\Controllers;

use App\Models\Premio;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PremioController extends Controller
{
    /* Distribución oficial del pozo por posición */
    public const PORCENTAJES = [1 => 50.00, 2 => 30.00, 3 => 20.00];

    public function index()
    {
        $premios = Premio::with('usuario')->orderBy('posicion')->get();

        // Participantes activos para el selector de ganadores
        $usuarios = Usuario::where('rol_id', 2)->where('activo', 1)
                        ->orderBy('apellido')->get();

        // Top 3 del ranking actual (sugerencia automática)
        $top3 = DB::select('SELECT * FROM ranking_general LIMIT 3');

        // Pozo total actual (si ya hay premios registrados)
        $montoTotal = optional($premios->first())->monto_total ?? 0;

        return view('admin.premios', compact('premios', 'usuarios', 'top3', 'montoTotal'));
    }

    /* Crea o actualiza el premio de una posición concreta */
    public function guardar(Request $request)
    {
        $request->validate([
            'posicion'    => 'required|integer|in:1,2,3',
            'usuario_id'  => 'required|exists:usuarios,id',
            'monto_total' => 'required|numeric|min:0|max:9999999999',
            'estado'      => 'required|in:pendiente,confirmado,pagado',
        ], [
            'posicion.in'        => 'La posición debe ser 1, 2 o 3.',
            'usuario_id.required'=> 'Selecciona el participante ganador.',
            'monto_total.required'=> 'Indica el pozo total del concurso.',
        ]);

        $porcentaje  = self::PORCENTAJES[$request->posicion];
        $montoGanado = round($request->monto_total * $porcentaje / 100, 2);

        Premio::updateOrCreate(
            ['posicion' => $request->posicion],
            [
                'usuario_id'   => $request->usuario_id,
                'porcentaje'   => $porcentaje,
                'monto_total'  => $request->monto_total,
                'monto_ganado' => $montoGanado,
                'estado'       => $request->estado,
            ]
        );

        // Mantener el mismo pozo total en las demás posiciones ya registradas
        Premio::where('posicion', '!=', $request->posicion)
            ->update(['monto_total' => $request->monto_total]);
        // Recalcular sus montos ganados con el nuevo pozo
        foreach (Premio::where('posicion', '!=', $request->posicion)->get() as $p) {
            $p->update(['monto_ganado' => round($request->monto_total * $p->porcentaje / 100, 2)]);
        }

        return back()->with('success', 'Premio de la posición ' . $request->posicion . ' guardado correctamente.');
    }

    /* Genera automáticamente los 3 premios con el Top 3 del ranking */
    public function generarAutomatico(Request $request)
    {
        $request->validate([
            'monto_total' => 'required|numeric|min:0|max:9999999999',
        ]);

        $top3 = DB::select('SELECT * FROM ranking_general LIMIT 3');
        if (count($top3) < 3) {
            return back()->withErrors(['msg' => 'Aún no hay suficientes participantes con puntaje en el ranking (se necesitan 3).']);
        }

        foreach ($top3 as $i => $row) {
            $posicion    = $i + 1;
            $porcentaje  = self::PORCENTAJES[$posicion];
            $montoGanado = round($request->monto_total * $porcentaje / 100, 2);

            Premio::updateOrCreate(
                ['posicion' => $posicion],
                [
                    'usuario_id'   => $row->usuario_id,
                    'porcentaje'   => $porcentaje,
                    'monto_total'  => $request->monto_total,
                    'monto_ganado' => $montoGanado,
                    'estado'       => 'pendiente',
                ]
            );
        }

        return back()->with('success', 'Premios generados automáticamente con el Top 3 del ranking.');
    }

    /* Cambia el estado (pendiente -> confirmado -> pagado) */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate(['estado' => 'required|in:pendiente,confirmado,pagado']);
        Premio::findOrFail($id)->update(['estado' => $request->estado]);
        return back()->with('success', 'Estado del premio actualizado.');
    }

    public function eliminar($id)
    {
        Premio::findOrFail($id)->delete();
        return back()->with('success', 'Premio eliminado.');
    }
}
