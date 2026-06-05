-- ============================================================
--  TABLA: resultados_especiales
--  Registra qué equipos clasificaron oficialmente a cada fase
--  (o quién fue campeón, subcampeón, tercero) para calcular
--  los puntos de las predicciones_especiales de los participantes.
--
--  Ejecutar UNA sola vez sobre la base polla_mundial_2026.
--  (Si usas Laravel, equivale a:
--   php artisan migrate  -> 2026_06_05_000001_create_resultados_especiales_table)
-- ============================================================
USE polla_mundial_2026;

CREATE TABLE IF NOT EXISTS resultados_especiales (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo       ENUM(
        'clasificado_32avos',
        'clasificado_octavos',
        'clasificado_cuartos',
        'clasificado_semis',
        'clasificado_final',
        'tercer_puesto',
        'subcampeon',
        'campeon'
    ) NOT NULL,
    equipo_id  INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_resultado_tipo_equipo UNIQUE (tipo, equipo_id),
    CONSTRAINT fk_resultado_equipo FOREIGN KEY (equipo_id)
        REFERENCES equipos(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Resultados oficiales de fases para calcular predicciones especiales';

CREATE INDEX idx_resultado_tipo ON resultados_especiales(tipo);
