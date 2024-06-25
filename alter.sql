ALTER TABLE `transactions`
ADD `id_store` bigint unsigned NULL,
ADD FOREIGN KEY (`id_store`) REFERENCES `users` (`id`);

ALTER TABLE `transactions`
ADD `nama_kasir` varchar(255) NULL;