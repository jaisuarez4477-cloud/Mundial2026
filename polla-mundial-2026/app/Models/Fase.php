<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model
{
    protected $table    = 'fases';
    protected $fillable = ['nombre','slug','orden'];
    public $timestamps  = false;
    public function partidos(){ return $this->hasMany(Partido::class, 'fase_id'); }
}
