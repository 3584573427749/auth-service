CREATE TABLE `users`
(
    `id`         varchar(36) COLLATE utf8mb4_unicode_ci  NOT NULL,
    `email`      varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `last_name`  varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` datetime                                NOT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_users_email_unique` (`email`)
) ENGINE = MEMORY
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;