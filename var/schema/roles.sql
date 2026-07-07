CREATE TABLE `roles`
(
    `id`          varchar(36) COLLATE utf8mb4_unicode_ci  NOT NULL,
    `name`        varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `admin_level` int(11)  DEFAULT 0,
    `created_at`  datetime DEFAULT current_timestamp(),
    `updated_at`  datetime DEFAULT NULL ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE = MEMORY
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;