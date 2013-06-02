<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bill
 * Date: 31/03/2013
 * Time: 09:24
 *
 */

namespace Domain\Domain\Model;

abstract class AbstractModel
{
    /** @var array */
    protected $data = array();
    /** @var array */
    protected $dirtyData = array('set' => array(), 'unset' => array());
    /** @var array */
    protected $tempData = array();
    /** @var array */
    protected $requiredFields = array();
    /** @var array */
    protected $defaults = array();
    /** @var array */
    protected $requestedFields = array();

    /**
     * @param null $data
     * @param bool $clean
     */
    public function __construct($data = null, $clean = true)
    {
        if ($data) {
            if (is_object($data)) {
                $data = (array) $data;
            }

            foreach ($data as $key => $value) {
                if (substr($key, 0, 2) == '__') {
                    $this->tempData[$key] = $value;
                    unset($data[$key]);
                }
            }
            $this->populate($data, $clean);
        } else {
            $this->setDefaults();
        }
    }

    public function getId($asMongoId = true)
    {
        if ($this->_id) {
            if ($asMongoId) {
                return new \MongoId($this->_id);
            }
            return (string) $this->_id;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return get_called_class();
    }

    public function populateDirty($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }
        if (!is_array($data)) {
            throw new \Exception('Initial data must be an array or object');
        }

        foreach ($data as $key => $value) {
            $this->dirtyData['set'][$key] = $value;
        }
        return $this;
    }

    public function populate($data, $clean = false)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }
        if (!is_array($data)) {
            throw new \Exception('Initial data must be an array or object');
        }

        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
            if (!$clean) {
                $this->dirtyData['set'][$key] = $value;
            }
        }
        return $this;
    }

    public function __set($name, $value)
    {
        if (substr($name, 0, 2) == '__') {
            $this->tempData[$name] = $value;
        } else {
            $methodName = 'set' . $name;
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            } else {
                $this->dirtyData['set'][$name] = $value;
                unset($this->dirtyData['unset'][$name]);
            }
        }
    }

    public function __get($name)
    {
        if (substr($name, 0, 2) == '__') {
            if (array_key_exists($name, $this->tempData)) {
                return $this->tempData[$name];
            }
        } else {
            if ($this->isPartial() && !in_array($name, $this->requestedFields)) {
                throw new Exception('You are trying to get a field which was not requested from database');
            }
            if (array_key_exists($name, $this->dirtyData['set'])) {
                return $this->dirtyData['set'][$name];
            } elseif (array_key_exists($name, $this->dirtyData['unset'])) {
                return null;
            } else {
                if (array_key_exists($name, $this->data)) {
                    return $this->data[$name];
                }
            }
        }
        return null;
    }

    public function __isset($name)
    {
        if ($this->isPartial() && !in_array($name, $this->requestedFields)) {
            throw new \Exception('You are trying to get a field which was not requested from database');
        }
        if (isset($this->dirtyData['set'][$name])) {
            return true;
        } else {
            if (isset($this->data[$name])) {
                return true;
            }
        }
        return false;
    }

    public function __unset($name)
    {
        if ($this->isPartial() && !in_array($name, $this->requestedFields)) {
            throw new \Exception('You are trying to unset a field which was not requested from database');
        }
        $this->dirtyData['unset'][$name] = true;
        unset($this->dirtyData['set'][$name]);
    }

    public function _toArray()
    {
        return $this->getUnsavedData();
    }

    public function getDirtyArray()
    {
        return $this->dirtyData;
    }

    public function getDirtyValue($key)
    {
        if (array_key_exists($key, $this->dirtyData['set'])) {
            return $this->dirtyData['set'][$key];
        }
        return false;
    }

    public function resetDirty()
    {
        $this->dirtyData = array('set' => array(), 'unset' => array());
    }

    public function resetTemp()
    {
        $this->tempData = array();
    }

    public function getUnsavedData()
    {
        $data = array_merge($this->data, $this->dirtyData['set']);
        foreach ($this->dirtyData['unset'] as $key => $value) {
            unset($data[$key]);
        }
        return $data;
    }

    public function mergeDirtyAndCommit()
    {
        $this->data = $this->getUnsavedData();
        $this->resetDirty();
    }

    public function setDefaults()
    {
        foreach ($this->defaults as $field => $value) {
            $this->{$field} = $value;
        }
        return $this;
    }

    public function setRequestedFields($fields)
    {
        $this->requestedFields = $fields;
    }
    public function getRequestedFields()
    {
        return $this->requestedFields;
    }

    /**
     * If requested fields is anything other then empty array
     * then this model is partial.
     * @return bool
     */
    public function isPartial()
    {
        if (empty($this->requestedFields)) {
            return false;
        }
        return true;
    }

    /**
     * @internal param $data
     * @throws \Exception
     * @return bool
     */
    public function validate()
    {
        if ($this->isPartial()) {
            throw new \Exception('Validation is not available for partial models');
        }
        $data = $this->getUnsavedData();
        foreach ($this->requiredFields as $fieldName) {
            if (!isset($data[$fieldName])) {
                throw new \Exception($fieldName);
            }
        }
        return true;
    }
}
