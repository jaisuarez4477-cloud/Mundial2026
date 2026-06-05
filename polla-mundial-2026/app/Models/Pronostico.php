<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pronostico extends Model
{
    protected $table    = 'pronosticos';
    protected $fillable = ['usuario_id','partido_id','goles_local','goles_visitante','puntos_obtenidos','calculado'];

    public function usuario(){ return $this->belongsTo(Usuario::class, 'usuario_id'); }
    public function partido(){ return $this->belongsTo(Partido::class, 'partido_id'); }
}
