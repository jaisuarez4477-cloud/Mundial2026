<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\AdminController;

/*
|──────────────────────────────────────────────────────────────
| Rutas públicas (sin sesión)
|──────────────────────────────────────────────────────────────
*/
Route::get('/',               fn() => redirect()->route('login'));
Route::get('/login',          [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',         [AuthController::class, 'login'])->name('login.post');
Route::get('/register',       [AuthController::class, 'showLogin'])->name('register.show');
Route::post('/register',      [AuthController::class, 'register'])->name('register');
Route::post('/logout',        [AuthController::class, 'logout'])->name('logout');
Route::get('/password/reset', [AuthController::class, 'showForgot'])->name('password.request');

/*
|──────────────────────────────────────────────────────────────
| Rutas del participante (sesión requerida)
|──────────────────────────────────────────────────────────────
*/
Route::middleware('sesion')->group(function () {
    Route::get('/menu',                [ParticipanteController::class, 'menu'])->name('menu');
    Route::get('/pronosticos',         [ParticipanteController::class, 'pronosticos'])->name('pronosticos');
    Route::post('/pronosticos/guardar',[ParticipanteController::class, 'guardarPronostico'])->name('pronosticos.guardar');
    Route::get('/ranking',             [ParticipanteController::class, 'ranking'])->name('ranking');
    Route::get('/resultados',          [ParticipanteController::class, 'resultados'])->name('resultados');
});

/*
|──────────────────────────────────────────────────────────────
| Rutas del administrador (sesión + rol admin)
|──────────────────────────────────────────────────────────────
*/
Route::middleware('sesion:admin')->prefix('admin')->name('admin.')->group(function () {

    // ── Dashboard ────────────────────────────────────────
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // ── Usuarios ─────────────────────────────────────────
    Route::get('/usuarios',              [AdminController::class, 'usuarios'])->name('usuarios');
    Route::post('/usuarios/{id}/toggle', [AdminController::class, 'toggleUsuario'])->name('usuarios.toggle');

    // ── Equipos ──────────────────────────────────────────
    Route::get('/equipos',                [AdminController::class, 'equipos'])->name('equipos');
    Route::post('/equipos/crear',         [AdminController::class, 'crearEquipo'])->name('equipos.crear');
    Route::post('/equipos/{id}/editar',   [AdminController::class, 'editarEquipo'])->name('equipos.editar');
    Route::post('/equipos/{id}/eliminar', [AdminController::class, 'eliminarEquipo'])->name('equipos.eliminar');

    // ── Grupos ───────────────────────────────────────────
    Route::get('/grupos',                              [AdminController::class, 'grupos'])->name('grupos');
    Route::post('/grupos/crear',                       [AdminController::class, 'crearGrupo'])->name('grupos.crear');
    Route::post('/grupos/{id}/eliminar',               [AdminController::class, 'eliminarGrupo'])->name('grupos.eliminar');
    Route::post('/grupos/asignar-equipo',              [AdminController::class, 'asignarEquipoGrupo'])->name('grupos.asignar');
    Route::post('/grupos/{grupoId}/quitar/{equipoId}', [AdminController::class, 'quitarEquipoGrupo'])->name('grupos.quitar');

    // ── Partidos ─────────────────────────────────────────
    Route::get('/partidos',                   [AdminController::class, 'partidos'])->name('partidos');
    Route::post('/partidos/crear',            [AdminController::class, 'crearPartido'])->name('partidos.crear');
    Route::post('/partidos/{id}/resultado',   [AdminController::class, 'registrarResultado'])->name('partidos.resultado');
    Route::post('/partidos/{id}/eliminar',    [AdminController::class, 'eliminarPartido'])->name('partidos.eliminar');
});