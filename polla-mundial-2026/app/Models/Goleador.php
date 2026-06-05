<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Goleador extends Model
{
    protected $table    = 'goleadores';
    protected $fillable = ['equipo_id','nombre','apellido','goles','asistencias','minutos','tipo_premio'];
    public function equipo(){ return $this->belongsTo(Equipo::class, 'equipo_id'); }
    public function getNombreCompletoAttribute(){ return $this->nombre.' '.$this->apellido; }
}
