<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/config.php';

use App\Db\MySQL;
use PDOException;

$instance = MySQL::getInstance();
$connection = $instance->connection;

$connection->beginTransaction();

try {
    $connection->exec("DROP TABLE IF EXISTS `orders`;");
    $connection->exec("      
        CREATE TABLE `orders` (
          `id` varchar(36) NOT NULL,
          `name` varchar(128) NOT NULL,
          `email` varchar(128) NOT NULL,
          `qty` int(11) DEFAULT NULL,
          `sum` decimal(9,2) DEFAULT NULL,
          `currency` char(3) DEFAULT NULL,
          `status` tinyint(4) DEFAULT NULL,
          `token` varchar(128) DEFAULT NULL,
          `created_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    $connection->exec("DROP TABLE IF EXISTS order_items;");
    $connection->exec("        
        CREATE TABLE `order_items` (
          `id` varchar(36) NOT NULL,
          `id_order` varchar(36) DEFAULT NULL,
          `name_product` varchar(128) NOT NULL,
          `price` decimal(9,2) DEFAULT NULL,
          `qty` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `fk_order_items_order` (`id_order`),
          CONSTRAINT `fk_order_items_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
} catch (PDOException $e) {
    $connection->rollBack();
    die("Возникла ошибка: " . $e->getMessage());
}

$connection->commit();

echo 'Миграция успешна';
?>
