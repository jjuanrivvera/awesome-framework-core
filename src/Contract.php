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
     * @return array<mixed>|false
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
     * @param string $field
     * @param int|string $value
     * @return mixed
     */
    public function findWhere(string $field, $value);

    /**
     * Create a record
     * @param array<mixed> $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update a record
     * @param int $id
     * @param array<mixed> $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Delete a record
     * @param int $id
     * @return bool
     */
    public function delete(int $id);

    /**
     * Delete records with condition
     * @param string $field
     * @param int|string $value
     * @return mixed
     */
    public function deleteWhere(string $field, $value);
}
