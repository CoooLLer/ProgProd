<?php

namespace App\Core\AbstractClasses;

use App\Core\Interfaces\DatabaseInterface;
use App\Core\Interfaces\ModelInterface;
use DateTime;
use stdClass;

abstract class ModelRepository
{

    protected string $baseClass;
    protected string $tableName;
    protected DatabaseInterface $databaseConnection;

    public function __construct(DatabaseInterface $database)
    {
        $this->databaseConnection = $database;
    }

    public function fill(ModelInterface $model, array $data): ModelInterface
    {
        $dataMappings = $model->getDataMapping();
        foreach ($dataMappings as $key => $property) {
            if (empty($data[$key])) continue;

            switch ($property['type']) {
                case 'date':
                    $value = DateTime::createFromFormat('Y-m-d H:i:s', $data[$key]);
                    break;
                case 'float':
                    $value = (float)$data[$key];
                    break;
                case 'string':
                default:
                    $value = $data[$key];
                    break;
            }

            $model->{'set' . ucfirst($property['propertyName'])}($value);
        }

        return $model;
    }

    public function add($model): ModelInterface
    {
        if (is_array($model)) {
            $model = $this->fill(new $this->baseClass(), $model);
        }
        if (!$model instanceof $this->baseClass) {
            throw  new \Exception(sprintf('Wrong model of type %s for repository of type %s', $this->baseClass, self::class));
        }

        if (!empty($model->getId())) {
            $this->update($model);
        } else {
            $this->insert($model);
        }

        return $model;
    }

    private function update(ModelInterface $model)
    {
        $updateQueryData = $this->getUpdateQueryData($model);
        $query = 'UPDATE ' . $this->tableName . ' SET ' . $updateQueryData->queryString . ' WHERE id = :id';
        $updateQueryData->queryParams['id'] = $model->getId();
        $this->databaseConnection->updateQuery($query, $updateQueryData->queryParams);
    }

    private function insert(ModelInterface $model)
    {
        $insertQueryData = $this->getInsertQueryData($model);
        $query = 'INSERT INTO ' . $this->tableName . ' ' . $insertQueryData->queryString;
        $id = $this->databaseConnection->updateQuery($query, $insertQueryData->queryParams);
        $model->setId($id);
    }

    private function getUpdateQueryData(ModelInterface $model): stdClass
    {
        $updateQueryData = new stdClass();
        $updateQueryData->queryString = '';
        $updateQueryData->queryParams = [];

        $dataMapping = $model->getDataMapping();

        foreach ($dataMapping as $columnName => $modelProperty) {
            if ($columnName === 'id') continue;

            if (!empty($updateQueryData->queryString)) {
                $updateQueryData->queryString .= ', ';
            }
            $updateQueryData->queryString .= $columnName . '= :' . $modelProperty['propertyName'];
            $updateQueryData->queryParams[$modelProperty['propertyName']] = $this->getValueForQuery($model, $modelProperty);
        }

        return $updateQueryData;
    }

    private function getInsertQueryData(ModelInterface $model): stdClass
    {
        $insertQueryData = new stdClass();
        $insertQueryData->queryString = '';
        $insertQueryData->queryParams = [];

        $dataMapping = $model->getDataMapping();

        $firstQueryStringPart = '(';
        $secondQueryStringPart = '(';
        $i = 0;
        foreach ($dataMapping as $columnName => $modelProperty) {
            $divider = ++$i === count($dataMapping) ? '' : ', ';
            if ($columnName === 'id') continue;
            $firstQueryStringPart .= $columnName . $divider;
            $secondQueryStringPart .= ':' . $modelProperty['propertyName'] . $divider;
            $insertQueryData->queryParams[$modelProperty['propertyName']] = $this->getValueForQuery($model, $modelProperty);
        }
        $firstQueryStringPart .= ')';
        $secondQueryStringPart .= ')';

        $insertQueryData->queryString = $firstQueryStringPart . ' VALUES ' . $secondQueryStringPart;

        return $insertQueryData;
    }

    private function getValueForQuery(ModelInterface $model, $modelProperty)
    {
        $value = '';
        switch ($modelProperty['type']) {
            case 'date':
                $value = $model->{'get' . ucfirst($modelProperty['propertyName'])}()->format('Y.m.d');
                break;
            case 'int':
                $value = (int)$model->{'get' . ucfirst($modelProperty['propertyName'])}();
                break;
            case 'float':
                $value = number_format($model->{'get' . ucfirst($modelProperty['propertyName'])}(), 1, '.', '');
                break;
            case 'string':
            default:
                $value = (string)$model->{'get' . ucfirst($modelProperty['propertyName'])}();
                break;
        }
        return $value;
    }

    public function get(int $id): ?ModelInterface
    {
        $result = $this->getBy(['id' => $id]);

        if (empty($result)) return null;

        return $result[0];
    }

    public function getBy(array $parameters, string $orderParameter = null, string $orderDirection = null, int $limit = null): ?array
    {
        $models = [];
        $queryParameters = [];
        $query = 'SELECT * FROM ' . $this->tableName . ' WHERE 1=1 ';
        foreach ($parameters as $parameter => $value) {
            $operation = '=';
            if (substr($parameter, 0, 2) === '>=') {
                $operation = '>=';
                $parameter = substr($parameter, 2);
            }
            if (substr($parameter, 0, 2) === '<=') {
                $operation = '<=';
                $parameter = substr($parameter, 2);
            }
            $queryParameters[$parameter] = $value;
            $query .= sprintf('AND %s ' . $operation . ' :%s ', $parameter, $parameter);
        }
        if (!empty($orderParameter)) {
            $query .= 'ORDER BY ' . $orderParameter . ' ' . ($orderDirection ?? 'ASC') . ' ';
        }
        if (!empty($limit)) {
            $query .= 'LIMIT ' . $limit;
        }
        $results = $this->databaseConnection->selectQuery($query, $queryParameters);

        if (empty($results)) return null;

        foreach ($results as $result) {
            $model = new $this->baseClass();
            $models[] = $this->fill($model, $result);
        }

        return $models;
    }

    public function getOneBy(array $parameters): ?ModelInterface
    {
        $models = $this->getBy($parameters);
        if (!empty($models)) {
            return $models[0];
        }
        return null;
    }


}