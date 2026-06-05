<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Premio extends Model
{
    protected $table    = 'premios';
    protected $fillable = [
        'usuario_id','posicion','porcentaje',
        'monto_total','monto_ganado','estado'
    ];

    public function usuario(){ return $this->belongsTo(Usuario::class, 'usuario_id'); }
}
