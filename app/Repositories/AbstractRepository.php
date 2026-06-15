<?php

namespace App\Repositories;

use Closure;

abstract class AbstractRepository
{
    public $model;

    abstract public function getModelClass();

    public function __construct()
    {
        $this->makeModel();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function resetModel()
    {
        $this->makeModel();
    }

    public function makeModel()
    {
        $model = app($this->getModelClass());

        return $this->model = $model;
    }

    public function first($filters)
    {
        return $this->model->where($filters)->first();
    }

    public function create($fields)
    {
        return $this->model->create($fields);
    }

    public function simplePaging($filters, $columns = false)
    {
        $query = $this->model->where($filters);

        if (!empty($columns)) {
            $query->select($columns);
        }

        $limit = request('limit', default: 1);

        return $query->simplePaginate($limit);
    }

    public function findOrFail($id, $columns = ['*'])
    {
        $result = $this->model->findOrFail($id, $columns);

        return  $result;
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        $result = $this->model->updateOrCreate($attributes, $values);
        return  $result;
    }

    public function findByFilter($filters)
    {
        $result =  $this->model->where($filters)->first();
        $this->resetModel();

        return $result;
    }

    public function findByWhereOrWhere($wheres, $orWhere)
    {
        $result =  $this->model->where($wheres)->first();
        if (!$result) {
            $result = $this->model->where($orWhere)->first();
        }
        $this->resetModel();

        return $result;
    }

    public function find($id, $columns = ['*'], $limit = 3)
    {
        $result = $this->model->find($id, $columns);
        $this->resetModel();

        return  $result;
    }

    public function withCount($relations)
    {
        $this->model = $this->model->withCount($relations);
        return $this;
    }

    public function withWhereHas($relation, Closure $callback = null, $operator = '>=', $count = 1)
    {
        $this->model = $this->model->withWhereHas($relation, $callback, $operator, $count);
        return $this;
    }

    public function get($filters)
    {
        $result =  $this->model->where($filters)->get();
        $this->resetModel();

        return $result;
    }

    public function deleteByFilter($filters)
    {
        $result =  $this->model->where($filters)->delete();
        $this->resetModel();

        return $result;
    }
}
