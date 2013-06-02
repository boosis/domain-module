<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bill
 * Date: 31/03/2013
 * Time: 09:30
 * 
 */

namespace Domain\Db\Dao;

use Domain\Domain\Collection;
use Domain\Domain\Model\AbstractModel;

class Mongo extends AbstractDao
{
    protected $idField = '_id';

    public function save(AbstractModel $model, $options = array())
    {
        if (!isset($options['w'])) {
            $options['w'] = 1;
        }
        $id = $model->getId();
        if ($id) {
            return $this->update($model, $options);
        } else {
            $data = $model->getUnsavedData();
            $this->getDbAdapter()
                ->selectCollection($this->getTableName())
                ->insert($data, $options);
            $id = $data[$this->getIdField()];
            $model->{$this->getIdField()} = $id;
            $model->mergeDirtyAndCommit();
            return $model;
        }
    }

    public function update(AbstractModel $model, $options = array())
    {
        if (!isset($options['w'])) {
            $options['w'] = 1;
        }
        $data = $model->getDirtyArray();
        unset($data['set']['_id']);
        unset($data['unset']['_id']);
        $condition = array($this->getIdField() => $model->{$this->getIdField()});
        $operation = array();
        if (!empty($data['set'])) {
            $operation['$set'] = $data['set'];
        }
        if (!empty($data['unset'])) {
            $operation['$unset'] = $data['unset'];
        }
        $this->getDbAdapter()
            ->selectCollection($this->getTableName())
            ->update($condition, $operation, $options);
        $model->mergeDirtyAndCommit();
        return $model;
    }

    public function bulkUpdate($condition, $update, $options = array())
    {
        if (!isset($options['multiple'])) {
            $options['multiple'] = true;
        }
        return $this->getDbAdapter()
            ->selectCollection($this->getTableName())
            ->update($condition, $update, $options);
    }

    public function delete($id, $options = array())
    {
        if (!isset($options['w'])) {
            $options['w'] = 0;
        }
        $condition = array($this->getIdField() => $id);
        return $this->getDbAdapter()
            ->selectCollection($this->getTableName())
            ->remove($condition, $options);
    }

    public function deleteByCondition($condition, $options = array())
    {
        if (!isset($options['w'])) {
            $options['w'] = 0;
        }
        return $this->getDbAdapter()
            ->selectCollection($this->getTableName())
            ->remove($condition, $options);
    }


    public function fetchOneBy($condition, $fields = array())
    {
        $result = $this->getDbAdapter()
            ->selectCollection($this->getTableName())
            ->findOne($condition, $fields);
        if ($result) {
            $className = $this->getModelClassName();
            $model = new $className($result);
            $model->setRequestedFields($fields);
            return $model;
        }
        return false;
    }

    public function fetchBy($conditions, $fields = array(), $order = array(), $limit = null, $skip = null)
    {
        $collection = new Collection();
        if (!$conditions) {
            $conditions = array();
        }
        $result = $this->getDbAdapter()
            ->selectCollection($this->getTableName())
            ->find($conditions, $fields);
        if ($order) {
            $result->sort($order);
        }
        if ($limit) {
            $result->limit($limit);
        }
        if ($skip) {
            $result->skip($skip);
        }
        foreach ($result as $row) {
            $className = $this->getModelClassName();
            $model = new $className($row);
            $model->setRequestedFields($fields);
            $collection->add($model, (string) $row[$this->getIdField()]);
        }

        return $collection;
    }

    public function fetchCount($conditions = null, $limit = null)
    {
        if (!$conditions) {
            $conditions = array();
        }
        $result = $this->getDbAdapter()
            ->selectCollection($this->getTableName())
            ->count($conditions, $limit);
        return $result;
    }
    public function distinct($key, $query = array())
    {
        return $this->getDbAdapter()
            ->selectCollection($this->getTableName())
            ->distinct($key, $query);
    }
}
