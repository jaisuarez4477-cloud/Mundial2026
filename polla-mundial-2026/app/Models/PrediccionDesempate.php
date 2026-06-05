<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PrediccionDesempate extends Model
{
    protected $table    = 'predicciones_desempate';
    protected $fillable = ['usuario_id','tipo','jugador_nombre','correcto'];
    public $timestamps  = false;
    const CREATED_AT    = 'created_at';

    public function usuario(){ return $this->belongsTo(Usuario::class, 'usuario_id'); }
}
