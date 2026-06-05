<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    use HasFactory;
    protected $table    = 'usuarios';
    protected $fillable = ['rol_id','nombre','apellido','email','password','whatsapp','activo'];
    protected $hidden   = ['password'];

    public function rol()        { return $this->belongsTo(Rol::class, 'rol_id'); }
    public function puntaje()    { return $this->hasOne(Puntaje::class, 'usuario_id'); }
    public function pronosticos(){ return $this->hasMany(Pronostico::class, 'usuario_id'); }
    public function notificaciones(){ return $this->hasMany(Notificacion::class, 'usuario_id'); }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}
