<?php

namespace Awesome;

/**
 * Class Repository
 */
abstract class Repository implements Contract
{
    /**
     * @var mixed
     */
    protected mixed $model;

    /**
     * Get all records from the database
     * @return array<mixed>|false
     */
    public function all(): array | false
    {
        return $this->model->all();
    }

    /**
     * Get a record from the database by id
     * @param int $id
     * @return mixed
     */
    public function find(int $id): mixed
    {
        return $this->model->find($id);
    }

    /**
     * Get records with condition
     * @param string $field
     * @param int|string $value
     * @return mixed
     */
    public function findWhere(string $field, int | string $value): mixed
    {
        return $this->model->findWhere($field, $value);
    }

    /**
     * Get all records from the database by field
     * @param string $field
     * @param int|string $value
     * @return array<mixed>|false
     */
    public function allBy(string $field, int | string $value): array | false
    {
        return $this->model->allBy($field, $value);
    }

    /**
     * Update a record in the database
     * @param int $id
     * @param array<mixed> $data
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        return $this->model->update($id, $data);
    }

    /**
     * Create a record in the database
     * @param array<mixed> $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->model->create($data);
    }

    /**
     * Delete a record from the database
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    /**
     * Delete a record from the database
     * @param string $field
     * @param int|string $value
     * @return bool
     */
    public function deleteWhere(string $field, int | string $value): bool
    {
        return $this->model->deleteWhere($field, $value);
    }
}
