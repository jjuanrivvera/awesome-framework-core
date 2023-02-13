<?php

namespace Awesome;

use PDO;
use Awesome\Exceptions\NotFoundException;

/**
 * Base Model
 * @package Awesome
 */
abstract class Model
{
    /**
     * Table
     * @var string
     */
    protected string $table;

    /**
     * Database
     * @var Database
     */
    protected Database $db;

    /**
     * Model constructor.
     * @param Database $db
     * @throws \Exception
     * @return void
     */
    public function __construct(Database $db)
    {
        $this->db = $db;

        if (is_null($this->db->connection)) {
            $this->db->connect();
        }
    }

    /**
     * Get all records from the database
     * @return array<mixed>|false
     */
    public function all(): array|false
    {
        $query = "SELECT * FROM {$this->table}";
        $statement = $this->db->connection->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a record from the database by id
     * @param int $id
     * @return mixed
     */
    public function find(int $id): mixed
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a record from the database by id
     * @param string $field
     * @param int|string $value
     * @return mixed
     */
    public function findWhere(string $field, $value): mixed
    {
        $query = "SELECT * FROM {$this->table} WHERE '{$field}' = :value";
        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':value', $value);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all records from the database by field
     * @param string $field
     * @param int|string $value
     * @return array<mixed>|false
     */
    public function allBy(string $field, $value): array|false
    {
        $query = "SELECT * FROM {$this->table} WHERE '{$field}' = :value";
        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':value', $value);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a record in the database
     * @param int $id
     * @param array<mixed> $data
     * @return mixed
     * @throws \Throwable
     */
    public function update(int $id, array $data): mixed
    {
        if (!$this->find($id)) {
            throw new NotFoundException('Record not found');
        }

        try {
            $this->db->beginTransaction();
            $query = "UPDATE {$this->table} SET ";
            $query .= implode(', ', array_map(function ($key) use ($data) {
                return "{$key} = :{$key}";
            }, array_keys($data)));
            $query .= " WHERE id = :id";

            $statement = $this->db->connection->prepare($query);
            $statement->bindParam(':id', $id);
            $statement = $this->setStatementBindings($statement, $data);
            $statement->execute();
            $this->db->commit();

            return $this->find($id);
        } catch (\Throwable $th) {
            $this->db->rollback();
            throw $th;
        }
    }

    /**
     * Create and return record in the database
     * @param array<mixed> $data
     * @return mixed
     * @throws \Throwable
     */
    public function create(array $data): mixed
    {
        try {
            $this->db->beginTransaction();
            $query = "INSERT INTO {$this->table} (";
            $query .= implode(', ', array_keys($data));
            $query .= ") VALUES (";
            $query .= implode(', ', array_map(function ($key) {
                return ":{$key}";
            }, array_keys($data)));
            $query .= ")";

            $statement = $this->db->connection->prepare($query);

            $statement = $this->setStatementBindings($statement, $data);

            $statement->execute();
            $id = $this->db->lastInsertId();
            $this->db->commit();

            return $this->find((int) $id);
        } catch (\Throwable $th) {
            $this->db->rollBack();
            throw $th;
        }
    }

    /**
     * Delete a record from the database
     * @param int $id
     * @return bool
     * @throws \Throwable
     */
    public function delete(int $id): bool
    {
        try {
            $this->db->beginTransaction();
            $query = "DELETE FROM {$this->table} WHERE id = :id";

            $statement = $this->db->connection->prepare($query);
            $statement->bindParam(':id', $id);
            $result = $statement->execute();
            $this->db->commit();
        } catch (\Throwable $th) {
            $this->db->rollBack();
            throw $th;
        }

        return $result;
    }

    /**
     * Delete a record from the database by field
     * @param string $field
     * @param int|string $value
     * @return void
     * @throws \Throwable
     */
    public function deleteWhere(string $field, int|string $value): void
    {
        try {
            $this->db->beginTransaction();
            $query = "DELETE FROM {$this->table} WHERE '{$field}' = :value";
            $statement = $this->db->connection->prepare($query);
            $statement->bindParam(':value', $value);
            $statement->execute();
            $this->db->commit();
        } catch (\Throwable $th) {
            $this->db->rollBack();
            throw $th;
        }
    }

    /**
     * Set statement bindings
     * @param mixed $statement
     * @param array<mixed> $data
     * @return mixed
     */
    private function setStatementBindings(mixed $statement, array $data): mixed
    {
        foreach ($data as $key => $value) {
            $valueType = gettype($value);
            $dataType = PDO::PARAM_STR;

            $dataType = match ($valueType) {
                'integer' => PDO::PARAM_INT,
                'bool' => PDO::PARAM_BOOL,
                'NULL' => PDO::PARAM_NULL,
                'string' => PDO::PARAM_STR
            };

            $statement->bindValue(":{$key}", $value, $dataType);
        }

        return $statement;
    }
}
