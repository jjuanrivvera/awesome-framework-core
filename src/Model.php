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
    protected $table;

    /**
     * Database
     * @var Database
     */
    protected $db;

    /**
     * Model constructor.
     * @param Database $db
     * @throws \Exception
     */
    public function __construct(Database $db)
    {
        $this->db = $db;

        if (!$this->db->connection) {
            $this->db->connect();
        }
    }

    /**
     * Get all records from the database
     * @return array
     */
    public function all()
    {
        $query = "SELECT * FROM {$this->table}";
        $statement = $this->db->connection->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a record from the database by id
     * @param  int $id
     * @return array
     */
    public function find($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a record from the database by id
     * @param $field
     * @param $value
     * @return array
     */
    public function findWhere($field, $value)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$field} = :value";
        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':value', $value);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all records from the database by field
     * @param  string $field
     * @param  string $value
     * @return array
     */
    public function allBy($field, $value)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$field} = :value";
        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':value', $value);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update a record in the database
     * @param int $id
     * @param array $data
     * @return array
     * @throws \Throwable
     */
    public function update($id, $data)
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
     * @param array $data
     * @return array
     * @throws \Throwable
     */
    public function create($data)
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

            return $this->find($id);
        } catch (\Throwable $th) {
            $this->db->rollBack();
            throw $th;
        }
    }

    /**
     * Delete a record from the database
     * @param int $id
     * @return boolean
     * @throws \Throwable
     */
    public function delete($id)
    {
        try {
            $this->db->beginTransaction();
            $query = "DELETE FROM {$this->table} WHERE id = :id";
    
            $statement = $this->db->connection->prepare($query);
            $statement->bindParam(':id', $id);
            $statement->execute();
            $this->db->commit();
            return true;
        } catch (\Throwable $th) {
            $this->db->rollBack();
            throw $th;
        }

        return false;
    }

    /**
     * Delete a record from the database by field
     * @param string $field
     * @param string $value
     * @return void
     * @throws \Throwable
     */
    public function deleteWhere($field, $value)
    {
        try {
            $this->db->beginTransaction();
            $query = "DELETE FROM {$this->table} WHERE {$field} = :value";
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
     * @param array $data
     * @return
     */
    private function setStatementBindings($statement, $data)
    {
        foreach ($data as $key => $value) {
            $valueType = gettype($value);
            $dataType = PDO::PARAM_STR;

            switch ($valueType) {
                case 'integer':
                    $dataType = PDO::PARAM_INT;
                    break;
                case 'boolean':
                    $dataType = PDO::PARAM_BOOL;
                    break;
                case 'NULL':
                    $dataType = PDO::PARAM_NULL;
                    break;
            }

            $statement->bindValue(":{$key}", $value, $dataType);
        }

        return $statement;
    }
}
