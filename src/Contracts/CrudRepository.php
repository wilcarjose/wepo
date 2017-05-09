<?php

namespace Wilcar\Wepo\Contracts;

interface CrudRepository
{
    /**
     * Get all instances of associated model.
     *
     * @param array $with The relations to be eager-loaded
     *
     * @return Collection
     */
    public function all($with = []);

    /**
     * Get the global number of models.
     *
     * @return int
     */
    public function count();

    /**
     * Get the instances of model filtering by id.
     *
     * @param mixed $id model id
     *
     * @return Model
     */
    public function find($id);

    /**
     * Get the instances of model filtering by id | if not found return Exception.
     *
     * @param mixed $id model id
     *
     * @return Model
     */
    public function findOrFail($id);

    /**
     * Get all instances of associated model filtering by custom field using equality.
     *
     * @param string $field field name
     * @param mixed  $value field value
     *
     * @return Collection
     */
    public function findEquals($field, $value);

    /**
     * Get all instances of associated model filtering by custom field.
     *
     * @param string   $field   field name
     * @param mixed    $value   field value
     * @param null|int $perPage elements per page
     *
     * @return Collection
     */
    public function findBy($field, $value, $perPage = null);

    /**
     * Create a new instance of associated model.
     *
     * @param array $params model fields
     *
     * @return Model
     */
    public function create(array $params);

    /**
     * Create new instances of associated model. (Bulk operation).
     *
     * @param array $params array of array model fields
     *
     * @return bool
     */
    public function insert(array $params);

    /**
     * Update an existent instance of associated model.
     *
     * @param mixed $id     model id
     * @param array $params model fields
     *
     * @return Model
     */
    public function update($id, array $params);

    /**
     * Delete an existent instance of associated model.
     *
     * @param mixed $id model id
     *
     * @return bool
     */
    public function delete($id);

    /**
     * Get a collection of all fields given its name.
     *
     * @param array $fieldNames field names
     *
     * @return array
     */
    public function getFields(array $fieldNames);

    /**
     * Get an array of renderable fields into select control.
     *
     * @param string $fieldName field name
     *
     * @return array
     */
    public function getSelectableField($fieldName);
}
