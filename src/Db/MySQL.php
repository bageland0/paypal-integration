<?php
namespace App\Db;

use PDOException;

class MySQL {
    private static $instance;

    public $connection;

    private function __clone() {}

    private function __wakeup() {}

    private function __construct() {
        try {
            $this->connection = new \PDO(
                "mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'],
                $_ENV['DB_USER'], $_ENV['DB_PASSWORD']
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
