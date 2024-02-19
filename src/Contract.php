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
    public function all(): array | false;

    /**
     * Get a single record
     * @param int $id
     * @return mixed
     */
    public function find(int $id): mixed;

    /**
     * Get records with condition
     * @param string $field
     * @param int|string $value
     * @return mixed
     */
    public function findWhere(string $field, int | string $value): mixed;

    /**
     * Create a record
     * @param array<mixed> $data
     * @return mixed
     */
    public function create(array $data): mixed;

    /**
     * Update a record
     * @param int $id
     * @param array<mixed> $data
     * @return mixed
     */
    public function update(int $id, array $data): mixed;

    /**
     * Delete a record
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Delete records with condition
     * @param string $field
     * @param int|string $value
     * @return mixed
     */
    public function deleteWhere(string $field, int | string $value): mixed;
}
