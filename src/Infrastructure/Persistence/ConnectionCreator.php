<?php


namespace PDOProject\Pdo\Infrastructure\Persistence;

use PDO;
use PDOException;

class ConnectionCreator
{
    private $host;
    private $username;
    private $password;
    private $dbname;

    public function __construct(
        string $_username = 'alyssonDBA',
        string $_password = 'dev12@12',
        string $_dbname = 'pdo_PDOProject',
        string $_host = 'localhost'
    )
    {
        $this->host = $_host;
        $this->username = $_username;
        $this->password = $_password;
        $this->dbname = $_dbname;
    }

    public function create()
    {
        try{
            $pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
        }catch(PDOException $e){
            return false;
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

}