<?php

namespace Wilcar\Wepo\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Wilcar\Wepo\Contracts\CrudRepository as InterfaceRepository;

abstract class CrudRepository implements InterfaceRepository
{
    /**
     * Name of the Model with absolute namespace.
     *
     * @var string
     */
    protected $modelName;

    /**
     * Instance that extends Illuminate\Database\Eloquent\Model.
     *
     * @var Model
     */
    protected $model;

    /**
     * Checks if use uuid or autoincrement
     * 
     * @var bool
     */
    protected $useUuid;

    /**
     * EloquentCrudRepository constructor.
     *
     * @param Model $model
     */
    public function __construct($modelName = null)
    {
        $this->setModel($modelName);
        $this->useUuid = config('wepo.uuid');
    }

    //protected abstract function model();

    /**
     * Instantiate Model.
     *
     * @throws RepositoryException
     */
    public function setModel($modelName = null)
    {
        if (!is_null($modelName)) {
            $this->modelName = $modelName;
        }

        //check if the class exists
        if (class_exists($this->modelName)) {
            $this->model = new $this->modelName();

            //check object is a instanceof Illuminate\Database\Eloquent\Model
            if (!$this->model instanceof Model) {
                throw new \Exception("{$this->modelName} must be an instance of Illuminate\Database\Eloquent\Model");
            }
        } else {
            throw new \Exception("No {$this->modelName} name defined");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function all($with = [], $params = [])
    {
        $results = $this->model->with($with);

        return $this->getResults($results, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail($id)
    {
        $object = $this->model->find($id);
        
        return $object ? $object : $this->throwNotFound();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy($field, $value, $perPage = null)
    {
        if (is_null($perPage)) {
            return $this->model->where($field, 'LIKE', "%$value%")->get();
        }

        return $this->model->where($field, 'LIKE', "%$value%")->paginate();
    }

    /**
     * {@inheritdoc}
     */
    public function findEquals($field, $value, $params = [])
    {
        $results = $this->model->where($field, $value);

        return $this->getResults($results, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $params)
    {
        if ($this->useUuid) {
            $params['id'] = \Ramsey\Uuid\Uuid::uuid4();
        }

        return $this->model->create($params);
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $params)
    {
        return $this->model->insert($params);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $params)
    {
        $model = $this->model->findOrFail($id);
        $model->update($params);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(array $fieldNames)
    {
        return $this->model->select($fieldNames)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectableField($fieldName)
    {
        $result = [];
        foreach ($this->getFields([$fieldName]) as $field) {
            $result[$field->{$fieldName}] = $field->{$fieldName};
        }

        return $result;
    }

    /**
     * Throw Exception: object not found
     *
     * @return ModelNotFoundException
     */
    protected function throwNotFound()
    {
        throw new ModelNotFoundException();
    }

    /**
     * Apply filter to results using where
     *
     * @param  mixed $result
     * @param  array $filte
     *
     * @return midex
     */
    protected function applyWhere($result, $filter)
    {
        if (is_null($filter)) {
            return $result;
        }

        foreach ($filter as $section) {
            if (!$this->model->isFillable($section['field'])) {
                continue;
            }

            if (str_is($section['operator'], 'in')) {
                $field = $section['field'];
                $value = $section['value'];
                $result = $result->whereIn($field, $value);
                continue;
            }

            $field = $section['field'];
            $value = $section['value'];
            $operator = $section['operator'];
            $result = $result->where($field, $operator, $value);
        }

        return $result;
    }

    /**
     * Order the results by give field
     *
     * @param  mixed $result
     * @param  array $order
     *
     * @return mixed
     */
    protected function applyOrderBy($result, $order)
    {
        if (!is_null($order) && $this->model->isFillable($order['field'])) {
            return $result->orderBy($order['field'], $order['direction']);
        }

        return $result;
    }

    /**
     * Get results apply where and order
     *
     * @param  mixed $results
     * @param  array $params
     *
     * @return Collection
     */
    protected function getResults($results, $params)
    {
        if (!empty($params)) {
            $results = $this->applyWhere($results, $params['filter']);
            return $this->applyOrderBy($results, $params['sort'])->paginate($params['limit']);
        }

        return $results->paginate();
    }
}
