<?php

namespace Awesome;

use PDO;

/**
 * Base Model
 * @package Awesome
 */
abstract class Model
{
    protected $table;

    /**
     * Model constructor.
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
     * @param  int $id
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
     * @param  int $id
     * @param  array $data
     * @return void
     */
    public function update($id, $data)
    {
        $query = "UPDATE {$this->table} SET ";
        $query .= implode(', ', array_map(function ($key) use ($data) {
            return "{$key} = :{$key}";
        }, array_keys($data)));
        $query .= " WHERE id = :id";

        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':id', $id);

        foreach ($data as $key => $value) {
            $statement->bindParam(":{$key}", $value);
        }

        $statement->execute();
    }
    
    /**
     * Create a record in the database
     * @param  array $data
     * @return void
     */
    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (";
        $query .= implode(', ', array_keys($data));
        $query .= ") VALUES (";
        $query .= implode(', ', array_map(function ($key) {
            return ":{$key}";
        }, array_keys($data)));
        $query .= ")";

        $statement = $this->db->connection->prepare($query);

        foreach ($data as $key => $value) {
            $statement->bindParam(":{$key}", $value);
        }

        $statement->execute();
    }

    /**
     * Delete a record from the database
     * @param  int $id
     * @return boolean
     */
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";

        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':id', $id);
        return $statement->execute();
    }

    /**
     * Delete a record from the database by field
     * @param  string $field
     * @param  string $value
     * @return void
     */
    public function deleteWhere($field, $value)
    {
        $query = "DELETE FROM {$this->table} WHERE {$field} = :value";
        $statement = $this->db->connection->prepare($query);
        $statement->bindParam(':value', $value);
        $statement->execute();
    }
}
