<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bill
 * Date: 31/03/2013
 * Time: 09:30
 * 
 */

namespace Domain\Db\Dao;

use Domain\Domain\Model\AbstractModel;

class MySql extends AbstractDao
{
    protected $idField = 'id';

    public function save(AbstractModel $model, $options = array())
    {

    }

    public function update(AbstractModel $model, $options = array())
    {

    }

    public function bulkUpdate($condition, $update, $options = array())
    {

    }

    public function delete($id, $options = array())
    {

    }

    public function deleteByCondition($condition, $options = array())
    {

    }

    public function fetchOneBy($condition, $fields = null)
    {

    }

    public function fetchBy($conditions, $fields = array(), $order = array(), $limit = null, $skip = null)
    {

    }

    public function fetchCount($conditions = null, $limit = null)
    {

    }
    public function distinct($key, $query = array())
    {

    }
}
