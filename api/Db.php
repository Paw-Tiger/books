<?php


class Db
{

    public $connection;

    public function __construct()
    {
        $config = require(__DIR__ . '/config.php');

        $this->connection = new mysqli($config['host'],$config['username'],$config['password'],$config['dbname']);
        $this->connection->set_charset("utf8");
        if ($this->connection->connect_error) {
            throw new \Exception('No connect');
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
