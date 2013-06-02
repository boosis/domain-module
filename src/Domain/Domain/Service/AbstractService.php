<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bill
 * Date: 01/04/2013
 * Time: 12:16
 * 
 */
namespace Domain\Domain\Service; 
use Domain\Db\Dao\AbstractDao;
use Domain\Domain\Model\AbstractModel;

class AbstractService
{
    /**
     * @var array AbstractDao[]
     */
    protected $daos = array();

    /**
     * @param $key
     * @param AbstractDao $dao
     * @return $this
     */
    public function addDao($key, AbstractDao $dao)
    {
        $this->daos[strtolower($key)] = $dao;
        return $this;
    }

    /**
     * @param $key
     * @return AbstractDao
     */
    public function getDao($key = 'default')
    {
        return $this->daos[strtolower($key)];
    }
    public function getBy($condition = array(), $fields = array(), $daoKey = 'default', $sort = null, $skip = null, $limit = null)
    {
        return $this->getDao($daoKey)->fetchBy($condition, $fields, $sort, $skip, $limit);
    }
    public function save(AbstractModel $model, $options = array(), $daoKey = 'default')
    {
        $model->validate();
        if ($model->getId()) {
            $model = $this->getDao($daoKey)->update($model, $options);
        } else {
            $model = $this->getDao($daoKey)->save($model, $options);
        }
        return $model;
    }
    public function delete($id, $daoKey = 'default')
    {
        if (!$id) {
            return false;
        }
        return $this->getDao($daoKey)->delete($id);
    }
    public function getById($id, $fields = array(), $daoKey = 'default')
    {
        if (!$id) {
            return false;
        }
        $result = $this->getDao($daoKey)->fetchBy(array($this->getDao($daoKey)->getIdField() => $id), $fields);
        return $result;
    }
}
