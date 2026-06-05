<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de resultados oficiales de las predicciones especiales.
 * Guarda qué equipos clasificaron realmente a cada fase
 * (32avos, octavos, ..., campeón) para calcular los puntos
 * de las predicciones_especiales de los participantes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultados_especiales', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', [
                'clasificado_32avos',
                'clasificado_octavos',
                'clasificado_cuartos',
                'clasificado_semis',
                'clasificado_final',
                'tercer_puesto',
                'subcampeon',
                'campeon',
            ]);
            $table->unsignedInteger('equipo_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['tipo', 'equipo_id'], 'uq_resultado_tipo_equipo');
            $table->index('tipo', 'idx_resultado_tipo');
            $table->foreign('equipo_id')->references('id')->on('equipos')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados_especiales');
    }
};
