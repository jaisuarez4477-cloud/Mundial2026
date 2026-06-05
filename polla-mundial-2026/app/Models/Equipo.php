<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table    = 'equipos';
    protected $fillable = ['nombre','nombre_corto','codigo','bandera_url','confederacion'];

    public function grupos()     { return $this->belongsToMany(Grupo::class, 'grupos_equipos', 'equipo_id', 'grupo_id'); }
    public function goleadores() { return $this->hasMany(Goleador::class, 'equipo_id'); }

}

