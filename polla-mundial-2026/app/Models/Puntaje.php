<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Puntaje extends Model
{
    protected $table    = 'puntajes';
    protected $fillable = [
        'usuario_id','puntos_partidos','puntos_grupos',
        'puntos_eliminatorias','puntos_total','posicion_ranking'
    ];
    public $timestamps = false;
    const UPDATED_AT   = 'updated_at';

    public function usuario(){ return $this->belongsTo(Usuario::class, 'usuario_id'); }
}
