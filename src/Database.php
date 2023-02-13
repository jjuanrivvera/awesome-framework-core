<?php

namespace Awesome;

use PDO;
use Awesome\Exceptions\DatabaseConnectionException;

/**
 * Class Database
 * @package Awesome
 * @author  Juan Felipe Rivera G
 */
class Database
{
    /**
     * Connection
     * @var PDO|null
     */
    public ?PDO $connection = null;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * Database constructor
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Connect to the database
     * @throws \Exception
     * @return void
     */
    public function connect(): void
    {
        try {
            $this->connection = new PDO(
                $this->config->get('database.connectionString'),
                $this->config->get('database.username'),
                $this->config->get('database.password')
            );
        } catch (\PDOException $e) {
            throw new DatabaseConnectionException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * Begin transaction
     * @return void
     */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     * @return void
     */
    public function commit(): void
    {
        $this->connection->commit();
    }

    /**
     * Rollback transaction
     * @return void
     */
    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    /**
     * Get last insert id
     * @return string|false
     */
    public function lastInsertId(): false|string
    {
        return $this->connection->lastInsertId();
    }
}
