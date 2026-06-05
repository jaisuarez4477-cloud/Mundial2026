<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GrupoEquipo extends Model {
    protected $table = 'grupos_equipos';
    public $timestamps = false;
    protected $fillable = ['grupo_id','equipo_id','posicion_final','puntos','goles_favor','goles_contra','diferencia_goles','partidos_jugados','partidos_ganados','partidos_empatados','partidos_perdidos'];
    public function grupo()  { return $this->belongsTo(Grupo::class); }
    public function equipo() { return $this->belongsTo(Equipo::class); }
}
