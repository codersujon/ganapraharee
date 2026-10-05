CREATE TABLE migrations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    migration VARCHAR(255) NOT NULL,
    batch INT UNSIGNED NOT NULL,

    executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE KEY uq_migrations_migration (migration),
    KEY idx_migrations_batch (batch)

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
