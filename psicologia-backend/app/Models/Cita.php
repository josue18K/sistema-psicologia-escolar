<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni_estudiante',
        'fecha_cita',
        'motivo',
        'estado',
        'correo_enviado',
    ];

    protected $casts = [
        'fecha_cita' => 'datetime',
        'correo_enviado' => 'boolean',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'dni_estudiante', 'dni');
    }
}
