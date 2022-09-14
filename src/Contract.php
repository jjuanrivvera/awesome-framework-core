<?php

namespace Awesome;

/**
 * Contract interface
 * @package Awesome
 * @author Juan Felipe Rivera G
 */
interface Contract
{
    /**
     * Get all records
     * @return mixed
     */
    public function all();

    /**
     * Get a single record
     * @param int $id
     * @return mixed
     */
    public function find(int $id);

    /**
     * Get records with condition
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function findWhere(string $column, string $value);

    /**
     * Create a record
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update a record
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Delete a record
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * Delete records with condition
     * @param string $field
     * @param string $value
     * @return mixed
     */
    public function deleteWhere(string $field, string $value);
}
