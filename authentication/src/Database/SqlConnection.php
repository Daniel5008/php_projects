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

    private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

    private function setParams($statement, $parameters = array()) 
    {
        
        foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}
    }

    public function query($query, $parameters) 
    {
        $stmt = $this->connection->prepare($query);
        $this->setParams($stmt, $parameters);

        $stmt->execute();
    }

    public function select($query, $parameters = array())
    {
        $stmt = $this->connection->prepare($query);
        $this->setParams($stmt, $parameters);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


}

