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
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractService implements ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    /**
     * @var array AbstractDao[]
     */
    protected $daos = array();
    protected $serviceLocator;
    protected $events;

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

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Inject an EventManager instance
     *
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->events = $eventManager;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }
}
