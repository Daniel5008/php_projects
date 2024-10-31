<?php

namespace Src\Database;

use Dotenv\Dotenv;
use PDO;

class SqlConnection {

    private $connection;

    public function __construct() {

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $host    = $_ENV['HOST'];
        $dbName  = $_ENV['DB_NAME'];
        $user    = $_ENV['USER'];
        $pass    = $_ENV['PASS'];
        
        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbName", $user, $pass);
        } catch (\PDOException $e) {
            die('Erro de conexão: ' . $e->getMessage());
        }
    }


}

