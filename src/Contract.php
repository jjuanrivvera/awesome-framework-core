<?php

namespace Awesome;

interface Contract
{
    /**
     * @return mixed
     */
    public function all();

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id);

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function findWhere(string $column, string $value);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     */
    public function deleteWhere(string $field, string $value);
}
