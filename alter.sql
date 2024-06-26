DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_payment_type` bigint unsigned DEFAULT NULL,
  `total` bigint NOT NULL,
  `transaction_date` datetime DEFAULT NULL,
  `notes` text NOT NULL,
  `timestamp` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_code` varchar(255) DEFAULT NULL,
  `source` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `status` int DEFAULT NULL,
  `id_store` bigint unsigned DEFAULT NULL,
  `nama_kasir` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_id_user_foreign` (`id_user`),
  KEY `transactions_id_payment_type_foreign` (`id_payment_type`),
  KEY `id_store` (`id_store`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`id_store`) REFERENCES `users` (`id`),
  CONSTRAINT `transactions_id_payment_type_foreign` FOREIGN KEY (`id_payment_type`) REFERENCES `m_payment_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transactions_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;