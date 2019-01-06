<?php declare(strict_types=1);

namespace PartDB\FilterSystem;

use PartDB\Base\DBElement;
use PartDB\Database;
use PartDB\Exceptions\DatabaseException;
use PartDB\Exceptions\ElementNotExistingException;
use PartDB\Exceptions\NotImplementedException;
use PartDB\Log;
use PartDB\User;

/**
 * This class
 * @package PartDB\FilterSystem
 * @since Part-DB 0.6.0
 */
class ElementsResult implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * @var Database
     */
    protected $database;
    /**
     * @var User
     */
    protected $current_user;
    /**
     * @var Log
     */
    protected $log;

    /**
     * @var array
     */
    protected $element_data = array();

    protected $element_id = array();

    /**
     * @var int This counter points to the current element (the index of $element_id and $element_data)
     */
    protected $element_pointer = 0;

    protected function __construct(Database $database, User $current_user, Log $log, array $query_result)
    {
        if (empty($query_result)) {
            throw new \InvalidArgumentException(_('$query_results darf nicht leer sein!'));
        }

        $this->database = $database;
        $this->current_user = $current_user;
        $this->log = $log;

        foreach ($query_result as $result) {
            if (!is_array($result)) {
                //In this case the given array, is only a list of the IDs, without any other info
                $this->element_id[] = (int) $result;
            } else {
                //In this case we should have an ID entry in the array
                if (!isset($result['id'])) {
                    throw new \InvalidArgumentException(_('$query_results muss einen Eintrag id haben!'));
                }
                $this->element_id[] = (int) $result['id'];
                //If there is any other data, besides id, then save it
                if (count($result) > 1) {
                    $this->element_data[] = $result;
                } else {
                    $this->element_data[] = null;
                }
            }
        }
    }


    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @throws ElementNotExistingException
     * @throws DatabaseException
     */
    public function &current() : DBElement
    {
        $class = static::getElementClass();
        return $class::getInstance(
            $this->database,
            $this->current_user,
            $this->log,
            $this->element_id[$this->element_pointer],
            $this->element_data[$this->element_pointer]
        );
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->element_pointer;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() : int
    {
        return $this->element_pointer;
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->element_id[$this->element_pointer]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->element_id = 0;
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->element_id[$offset]);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset) : DBElement
    {
        return self::getElementClass()::getInstance(
            $this->database,
            $this->current_user,
            $this->log,
            $this->element_id[$offset],
            $this->element_data[$offset]
        );
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        //Do nothing
        return;
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        //Do nothing
        return;
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->element_id);
    }

    /**
     * @return DBElement
     */
    public static function getElementClass()
    {
        throw new NotImplementedException(_('getElementClass() ist nicht implementiert.'));
    }
}