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
    protected $model;

    /**
     * Get all records from the database
     * @return array
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Get a record from the database by id
     * @param  int $id
     * @return array
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Get a record from the database by id
     * @param  int $id
     * @return array
     */
    public function findWhere($field, $value)
    {
        return $this->model->findWhere($field, $value);
    }

    /**
     * Get all records from the database by field
     * @param  string $field
     * @param  string $value
     * @return array
     */
    public function allBy($field, $value)
    {
        return $this->model->allBy($field, $value);
    }

     /**
     * Update a record in the database
     * @param  int $id
     * @param  array $data
     * @return void
     */
    public function update($id, $data)
    {
        return $this->model->update($id, $data);
    }

    /**
     * Create a record in the database
     * @param  array $data
     * @return void
     */
    public function create($data)
    {
        return $this->model->create($data);
    }

    /**
     * Delete a record from the database
     * @param  int $id
     * @return boolean
     */
    public function delete($id)
    {
        return $this->model->delete($id);
    }

    /**
     * Delete a record from the database
     * @param  string $field
     * @param  string $value
     * @return boolean
     */
    public function deleteWhere($field, $value)
    {
        return $this->model->deleteWhere($field, $value);
    }
}
