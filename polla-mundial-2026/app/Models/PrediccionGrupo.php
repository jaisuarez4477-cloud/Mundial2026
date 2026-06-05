<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PrediccionGrupo extends Model {
    public $timestamps = false;
    protected $table = 'predicciones_grupos';
    protected $fillable = ['usuario_id','grupo_id','equipo_id','posicion_pred','puntos_obtenidos','calculado'];
    public function usuario() { return $this->belongsTo(Usuario::class); }
    public function grupo()   { return $this->belongsTo(Grupo::class); }
    public function equipo()  { return $this->belongsTo(Equipo::class); }
}
