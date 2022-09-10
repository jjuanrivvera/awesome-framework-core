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
                $this->config->get('database.connectionString'),
                $this->config->get('database.username'),
                $this->config->get('database.password')
            );
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback()
    {
        $this->connection->rollBack();
    }

    /**
     * Get last insert id
     */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}
