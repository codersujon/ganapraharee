CREATE TABLE permissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,

    name VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    module VARCHAR(100) NOT NULL,
    description VARCHAR(500) NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE KEY uq_permissions_uuid (uuid),
    UNIQUE KEY uq_permissions_slug (slug),

    KEY idx_permissions_module (module)

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
