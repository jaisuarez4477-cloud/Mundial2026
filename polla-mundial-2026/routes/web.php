<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PrediccionController;
use App\Http\Controllers\GoleadorController;
use App\Http\Controllers\PremioController;
use App\Http\Controllers\NotificacionController;

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

    // ── Predicciones de grupos (posición final 1-4) ──────
    Route::get('/predicciones/grupos',          [PrediccionController::class, 'grupos'])->name('predicciones.grupos');
    Route::post('/predicciones/grupos/guardar', [PrediccionController::class, 'guardarGrupo'])->name('predicciones.grupos.guardar');

    // ── Predicciones especiales (clasificados, campeón...) ─
    Route::get('/predicciones/especiales',              [PrediccionController::class, 'especiales'])->name('predicciones.especiales');
    Route::post('/predicciones/especiales/guardar',     [PrediccionController::class, 'guardarEspecial'])->name('predicciones.especiales.guardar');
    Route::post('/predicciones/especiales/{id}/eliminar',[PrediccionController::class, 'eliminarEspecial'])->name('predicciones.especiales.eliminar');

    // ── Predicciones de desempate (bota/balón de oro...) ──
    Route::get('/predicciones/desempate',               [PrediccionController::class, 'desempate'])->name('predicciones.desempate');
    Route::post('/predicciones/desempate/guardar',      [PrediccionController::class, 'guardarDesempate'])->name('predicciones.desempate.guardar');
    Route::post('/predicciones/desempate/{id}/eliminar',[PrediccionController::class, 'eliminarDesempate'])->name('predicciones.desempate.eliminar');
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

    // ── Goleadores y premios individuales ────────────────
    Route::get('/goleadores',                  [GoleadorController::class, 'index'])->name('goleadores');
    Route::post('/goleadores/crear',           [GoleadorController::class, 'crear'])->name('goleadores.crear');
    Route::post('/goleadores/{id}/editar',     [GoleadorController::class, 'editar'])->name('goleadores.editar');
    Route::post('/goleadores/{id}/eliminar',   [GoleadorController::class, 'eliminar'])->name('goleadores.eliminar');
    Route::post('/goleadores/evaluar/{tipo}',  [GoleadorController::class, 'evaluarDesempate'])->name('goleadores.evaluar');

    // ── Premios (distribución del pozo) ──────────────────
    Route::get('/premios',                [PremioController::class, 'index'])->name('premios');
    Route::post('/premios/guardar',       [PremioController::class, 'guardar'])->name('premios.guardar');
    Route::post('/premios/generar',       [PremioController::class, 'generarAutomatico'])->name('premios.generar');
    Route::post('/premios/{id}/estado',   [PremioController::class, 'cambiarEstado'])->name('premios.estado');
    Route::post('/premios/{id}/eliminar', [PremioController::class, 'eliminar'])->name('premios.eliminar');

    // ── Notificaciones (WhatsApp / email / sistema) ──────
    Route::get('/notificaciones',                 [NotificacionController::class, 'index'])->name('notificaciones');
    Route::post('/notificaciones/crear',          [NotificacionController::class, 'crear'])->name('notificaciones.crear');
    Route::post('/notificaciones/{id}/enviada',   [NotificacionController::class, 'marcarEnviada'])->name('notificaciones.enviada');
    Route::post('/notificaciones/{id}/eliminar',  [NotificacionController::class, 'eliminar'])->name('notificaciones.eliminar');
});