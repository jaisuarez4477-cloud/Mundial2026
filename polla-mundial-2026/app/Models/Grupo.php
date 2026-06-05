<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table    = 'grupos';
    protected $fillable = ['nombre','descripcion'];
    public $timestamps  = false;

    public function equipos()  { return $this->belongsToMany(Equipo::class, 'grupos_equipos', 'grupo_id', 'equipo_id')->withPivot('posicion_final'); }
    public function partidos() { return $this->hasMany(Partido::class, 'grupo_id'); }
}
