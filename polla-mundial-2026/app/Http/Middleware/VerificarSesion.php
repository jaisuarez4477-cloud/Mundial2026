<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class VerificarSesion
{
    public function handle(Request $request, Closure $next, string $rol = 'participante')
    {
        if (!session('usuario_id')) {
            return redirect()->route('login')->withErrors(['msg' => 'Debes iniciar sesión.']);
        }
        if ($rol === 'admin' && session('usuario_rol') !== 'admin') {
            return redirect()->route('menu')->withErrors(['msg' => 'No tienes permisos de administrador.']);
        }
        return $next($request);
    }
}
