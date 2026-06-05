<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PrediccionEspecial extends Model {
    protected $table = 'predicciones_especiales';
    protected $fillable = ['usuario_id','equipo_id','tipo','puntos_obtenidos','calculado'];
    public function usuario() { return $this->belongsTo(Usuario::class); }
    public function equipo()  { return $this->belongsTo(Equipo::class); }
}
