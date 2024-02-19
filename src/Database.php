<?php

namespace Awesome;

use PDO;
use Exception;
use PDOException;
use InvalidArgumentException;
use Awesome\Exceptions\DatabaseConnectionException;

/**
 * Class Database
 * @package Awesome
 * @author  Juan Felipe Rivera G
 */
class Database
{
    /**
     * The singleton instance of the Database.
     * @var Database|null
     */
    private static ?Database $instance = null;

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
     * Private constructor to prevent direct instantiation.
     * @param Config $config
     */
    private function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Prevents cloning of the instance.
     */
    private function __clone()
    {
    }

    /**
     * Prevents unserialization of the instance.
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize singleton');
    }

    /**
     * Get the singleton instance of the Database.
     * @param Config $config
     * @return Database
     */
    public static function getInstance(Config $config): Database
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * Connect to the database
     * @throws DatabaseConnectionException
     * @return PDO
     */
    public function connect(): PDO
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        try {
            $connectionString = $this->config->get('database.connectionString');
            $username = $this->config->get('database.username');
            $password = $this->config->get('database.password');
            $options = $this->config->get('database.options') ?: [];

            if (!$connectionString || !$username || !$password) {
                throw new InvalidArgumentException("Database configuration parameters missing.");
            }

            $this->connection = new PDO($connectionString, $username, $password, $options);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->connection;
        } catch (PDOException $e) {
            throw new DatabaseConnectionException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Begin transaction
     * @throws DatabaseConnectionException
     * @return void
     */
    public function beginTransaction(): void
    {
        try {
            $this->connect()->beginTransaction();
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(
                "Failed to begin transaction: " . $e->getMessage(),
                (int)$e->getCode()
            );
        }
    }

    /**
     * Commit transaction
     * @throws DatabaseConnectionException
     * @return void
     */
    public function commit(): void
    {
        try {
            $this->connect()->commit();
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(
                "Failed to commit transaction: " . $e->getMessage(),
                (int)$e->getCode()
            );
        }
    }

    /**
     * Rollback transaction
     * @throws DatabaseConnectionException
     * @return void
     */
    public function rollback(): void
    {
        try {
            $this->connect()->rollBack();
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(
                "Failed to rollback transaction: " . $e->getMessage(),
                (int)$e->getCode()
            );
        }
    }

    /**
     * Get last insert id
     * @throws DatabaseConnectionException
     * @return string|false
     */
    public function lastInsertId(): string | false
    {
        try {
            return $this->connect()->lastInsertId();
        } catch (PDOException $e) {
            throw new DatabaseConnectionException(
                "Failed to retrieve last insert ID: " . $e->getMessage(),
                (int) $e->getCode()
            );
        }
    }
}
