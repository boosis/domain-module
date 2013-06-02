<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bill
 * Date: 31/03/2013
 * Time: 09:38
 * 
 */
namespace Domain\Domain;
class Collection implements \IteratorAggregate, \Countable
{
    /**
     * @var array|null
     */
    protected $_data = array();
    /**
     * @param null|array $items
     */
    public function __construct($items = null)
    {
        if ($items !== null && is_array($items)) {
            $this->_data = $items;
        }
    }
    /**
     * @param $item
     * @param $key
     */
    public function add($item, $key)
    {
        $this->_data[$key] = $item;
    }
    /**
     * @param $key
     */
    public function remove($key)
    {
        if ($this->exists($key)) {
            unset($this->_data[$key]);
        }
    }
    /**
     * @return bool|mixed
     */
    public function first()
    {
        if ($this->count() > 0) {
            return array_shift($this->_data);
        }
        return false;
    }
    /**
     * @return bool|mixed
     */
    public function last()
    {
        if ($this->count() > 0) {
            return array_pop($this->_data);
        }
        return false;
    }
    public function isLast($item)
    {
        $arrayKeys = array_keys($this->_data);
        if (array_search($item->Label, $arrayKeys) == $this->count()-1) {
            return true;
        }
        return false;
    }
    /**
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }
    /**
     * @param $key
     * @return bool
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }
        return false;
    }
    /**
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->_data[$key]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing Iterator or
     * Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_data);
    }
}