CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    uuid CHAR(36) NOT NULL,

    name VARCHAR(150) NOT NULL,
    email VARCHAR(191) NOT NULL,
    phone VARCHAR(30) NULL,

    password_hash VARCHAR(255) NOT NULL,

    avatar VARCHAR(500) NULL,

    status VARCHAR(20) NOT NULL DEFAULT 'active',

    email_verified_at DATETIME NULL,

    last_login_at DATETIME NULL,
    password_changed_at DATETIME NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    deleted_at DATETIME NULL,

    PRIMARY KEY (id),

    UNIQUE KEY uq_users_uuid (uuid),
    UNIQUE KEY uq_users_email (email),

    KEY idx_users_phone (phone),
    KEY idx_users_status (status),
    KEY idx_users_deleted_at (deleted_at),
    KEY idx_users_status_deleted (status, deleted_at),

    CONSTRAINT chk_users_status
        CHECK (status IN ('active', 'inactive', 'blocked', 'pending'))

) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
