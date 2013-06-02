<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bill
 * Date: 31/03/2013
 * Time: 09:22
 * 
 */

namespace Domain\Db\Dao;

use Domain\Domain\Model\AbstractModel;

abstract class AbstractDao
{
    protected $dbAdapter;
    protected $tableName;
    protected $modelClassName;
    protected $idField;

    abstract public function save(AbstractModel $model, $options = array());
    abstract public function update(AbstractModel $model, $options = array());
    abstract public function delete($condition, $options = array());
    abstract public function fetchOneBy($condition, $fields = null);
    abstract public function fetchBy($condition, $fields = null, $sort = null, $skip = null, $limit = null);
    abstract public function fetchCount($condition, $limit = null);

    public function setIdField($idField)
    {
        $this->idField = $idField;
        return $this;
    }

    public function getIdField()
    {
        return $this->idField;
    }

    public function setModelClassName($modelClassName)
    {
        $this->modelClassName = $modelClassName;
        return $this;
    }

    public function getModelClassName()
    {
        return $this->modelClassName;
    }

    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getTableName()
    {
        return $this->tableName;
    }
}
