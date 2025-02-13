<?php
namespace Db;

class MySQL {
    private static $instance;

    public $connection;

    private function __clone() {}

    private function __wakeup() {}

    private function __construct() {
        $config = require_once('config.php');
        try {
            $this->connection = new \PDO(
                "mysql:host=".$config['DB_HOST'].";dbname=".$config['DB_NAME'],
                $config['DB_USER'], $config['DB_PASSWORD']
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
