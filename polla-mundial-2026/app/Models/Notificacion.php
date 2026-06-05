<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table    = 'notificaciones';
    protected $fillable = ['usuario_id','tipo','asunto','mensaje','leida','enviada','enviada_at'];
    public $timestamps  = false;
    const CREATED_AT    = 'created_at';
    public function usuario(){ return $this->belongsTo(Usuario::class, 'usuario_id'); }
}
