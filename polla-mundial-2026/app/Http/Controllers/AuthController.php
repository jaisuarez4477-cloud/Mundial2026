<?php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Puntaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('usuario_id')) return redirect()->route('menu');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $usuario = Usuario::with('rol')
                          ->where('email', $request->email)
                          ->where('activo', 1)
                          ->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return back()->withErrors(['email' => 'Correo o contraseña incorrectos.'])
                         ->withInput($request->only('email'));
        }

        session([
            'usuario_id'     => $usuario->id,
            'usuario_nombre' => $usuario->nombre . ' ' . $usuario->apellido,
            'usuario_email'  => $usuario->email,
            'usuario_rol'    => $usuario->rol->nombre,
        ]);

        return $usuario->rol->nombre === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('menu');
    }

    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:usuarios,email',
            'whatsapp' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
            'terms'    => 'accepted',
        ], [
            'email.unique'       => 'Este correo ya está registrado.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'terms.accepted'     => 'Debes aceptar los términos y condiciones.',
        ]);

        if ($v->fails()) return back()->withErrors($v)->withInput();

        $usuario = Usuario::create([
            'rol_id'   => 2,
            'nombre'   => ucfirst(trim($request->nombre)),
            'apellido' => ucfirst(trim($request->apellido)),
            'email'    => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'whatsapp' => $request->whatsapp,
            'activo'   => 1,
        ]);

        Puntaje::create(['usuario_id' => $usuario->id]);

        return back()->with('success', '¡Registro exitoso! Ya puedes iniciar sesión.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    public function showForgot()
    {
        return view('auth.forgot');
    }
}
