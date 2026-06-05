<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    protected $table    = 'partidos';
    protected $fillable = [
        'fase_id','grupo_id','equipo_local_id','equipo_visitante_id',
        'fecha_hora','estadio','ciudad','pais_sede',
        'goles_local','goles_visitante','finalizado','llave_referencia'
    ];
    protected $casts = ['fecha_hora' => 'datetime', 'finalizado' => 'boolean'];

    public function fase()          { return $this->belongsTo(Fase::class, 'fase_id'); }
    public function grupo()         { return $this->belongsTo(Grupo::class, 'grupo_id'); }
    public function equipoLocal()   { return $this->belongsTo(Equipo::class, 'equipo_local_id'); }
    public function equipoVisitante(){ return $this->belongsTo(Equipo::class, 'equipo_visitante_id'); }
    public function pronosticos()   { return $this->hasMany(Pronostico::class, 'partido_id'); }

    public function getResultadoTextoAttribute(): string
    {
        if (is_null($this->goles_local)) return 'Pendiente';
        if ($this->goles_local > $this->goles_visitante) return $this->equipoLocal->nombre ?? 'Local';
        if ($this->goles_local < $this->goles_visitante) return $this->equipoVisitante->nombre ?? 'Visitante';
        return 'Empate';
    }
}
