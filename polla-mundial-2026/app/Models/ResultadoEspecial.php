<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ResultadoEspecial extends Model
{
    protected $table    = 'resultados_especiales';
    protected $fillable = ['tipo','equipo_id'];
    public $timestamps  = false;
    const CREATED_AT    = 'created_at';

    public function equipo(){ return $this->belongsTo(Equipo::class, 'equipo_id'); }
}
