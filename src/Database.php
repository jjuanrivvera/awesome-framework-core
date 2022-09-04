<?php

namespace Awesome;

use PDO;

class Database
{
    /**
     * Connection
     */
    public $connection;

    /**
     * Database constructor
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Connect to the database
     */
    public function connect()
    {
        try {
            $this->connection = new PDO(
                $this->config->getConnectionString(),
                $this->config->getDbUser(),
                $this->config->getDbPassword()
            );
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        }
    }
}
