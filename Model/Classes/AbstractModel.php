<?php
/**
 * File for Class \Model\Classes\AbstractModel
 *
 * Class handling array-like information
 *
 * @author Stefan Bischoff
 * @date 03.08.14 17:31
 */
namespace Model\Classes;

/**
 * Class AbstractModel
 * @package Model\Classes
 */
abstract class AbstractModel implements \ArrayAccess, \Iterator {

	/**
	 * @var int position in the array
	 */
	private $key;

	/**
	 * @var array The keys stored in the array
	 */
	private $keys = array();

	/**
	 * @var array the values stored in the array
	 */
	private $values = array();

	/**
	 * @var array The keys that have been changed since this Object was created
	 */
	private $changed_keys = array();

	/**
	 * @var array the available keys in the object
	 */
	private $available_keys = array();

	/**
	 * @var bool
	 */
	private $is_new = TRUE;

	/**
	 * @var int The next numric key in the Object
	 */
	private $next_numeric_key = 0;

	/**
	 * @return mixed
	 */
	public function key()
	{
		return $this->keys[$this->key];
	}

	/**
	 * Pushes the pointer one forward
	 * @return $this
	 */
	public function next()
	{
		$this->key++;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return array_key_exists($this->key, $this->keys);
	}

	/**
	 * @param $offset
	 * @return mixed
	 */
	public function getKeyByOffset($offset)
	{
		$key = array_search($offset, $this->keys, TRUE);

		return $key;
	}

	/**
	 * @param mixed $offset
	 * @return null
	 * @throws \Debug\Classes\CustomException
	 */
	public function offsetGet($offset)
	{

		$key = $this->getKeyByOffset($offset);

		if (($key !== FALSE) AND isset($this->values[$key])) {
			return $this->values[$key];
		} elseif (in_array($offset, $this->available_keys))
		{
				return NULL;
		} else {
				throw new \Debug\Classes\CustomException('Undefined offset: ' . get_called_class() . '[' . $offset . ']');
		}
	}

	/**
	 * @return mixed
	 */
	public function current()
	{
		return $this->values[$this->key];
	}

	/**
	 * @return $this
	 */
	public function rewind()
	{
		$this->key = 0;

		return $this;
	}

	/**
	 * @param mixed $offset
	 * @return $this
	 */
	public function offsetUnset($offset)
	{

		$key = $this->getKeyByOffset($offset);
		if ($key !== FALSE) {
			$this->changed_keys[] = $key;
			unset($this->keys[$key]);
			unset($this->values[$key]);
			$this->remove_available_key($offset);
		}

		return $this;
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		$key = $this->getKeyByOffset($offset);
		return $key !== FALSE;
	}

	/**
	 * @param $offset
	 * @return bool
	 */
	public function key_exists($offset)
	{
		return $this->offsetExists($offset);
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @return $this
	 */
	public function offsetSet($offset, $value)
	{
		if (is_string($offset) && intval($offset) == $offset AND ((intval($offset) > 0) OR ($offset === '0')))
		{
			$offset = intval($offset);
		}

		$key = $this->getKeyByOffset($offset);

		if ( ! isset($value) AND $key !== FALSE) {
			$this->offsetUnset($offset);

		} elseif (isset($value)) {

			if ($key !== FALSE) {
				$this->values[$key] = $value;
			} else {
				$this->keys[] = $offset;
				$this->values[] = $value;
				$this->add_available_key($offset);
				end($this->keys);
				$key = key($this->keys);
				if (is_int($offset))
				{
					$this->check_numeric_key($offset);
				}
			}
			$this->changed_keys[] = $key;
		}

		return $this;
	}

	/**
	 * @param $offset
	 * @return null
	 * @throws \Debug\Classes\CustomException
	 */
	public function __get($offset)
	{
		return $this->offsetGet($offset);
	}

	/**
	 * @param $offset
	 * @param $value
	 * @return AbstractModel
	 */
	public function __set($offset, $value)
	{
		return $this->offsetSet($offset, $value);
	}

	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 * @throws \Debug\Classes\CustomException
	 */
	public function __call($method, $arguments)
	{
		if (substr($method, 0, 4) === 'get_')
		{
			$argument = substr($method, 4);
			array_unshift($arguments, $argument);

			return call_user_func_array(array($this, '__get'), $arguments);

		} elseif (substr($method, 0, 4) === 'set_')
		{
			$argument = substr($method, 4);
			array_unshift($arguments, $argument);

			return call_user_func_array(array($this, '__set'), $arguments);

		} else {
			throw new \Debug\Classes\CustomException('Call to undefined method: '.get_called_class().'::'.$method);
		}
	}

	/**
	 * @param $offset
	 * @return AbstractModel
	 */
	public function __unset($offset)
	{
		return $this->offsetUnset($offset);
	}

	/**
	 * @param $offset
	 * @return bool
	 */
	public function __isset($offset)
	{
		$offset_exists = $this->offsetExists($offset);

		return $offset_exists;
	}

	/**
	 * @param $value
	 * @return $this
	 */
	public function add($value)
	{
		$this->values[] = $value;
		$next_numeric_key = $this->get_next_numeric_key();
		$this->keys[] = $next_numeric_key;
		$this->add_available_key($next_numeric_key);
		$this->check_numeric_key($next_numeric_key);

		return $this;
	}

	/**
	 * @param $next_numeric_key
	 * @return $this
	 */
	protected function check_numeric_key($next_numeric_key)
	{
		if ($next_numeric_key >= $this->next_numeric_key)
		{
			$this->next_numeric_key = $next_numeric_key + 1;
		}

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_next_numeric_key()
	{
		return $this->next_numeric_key;
	}

	/**
	 * @param $key
	 * @return $this
	 */
	public function add_available_key($key)
	{
		if (array_search($key, $this->available_keys) === FALSE)
		{
			$this->available_keys[] = $key;
		}

		return $this;
	}

	/**
	 * @param $key
	 * @return $this
	 */
	public function remove_available_key($key)
	{
		$array_keys = array_keys($this->available_keys, $key, TRUE);
		foreach ($array_keys as $array_key)
		{
			unset($this->available_keys[$array_key]);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_available_keys()
	{
		return $this->available_keys;
	}

	/**
	 * Clones the Object and all containing objects
	 * @return $this
	 */
	public function __clone()
	{
		foreach ($this as $key => $value)
		{
			if (is_object($value))
			{
				$this->offsetSet($key, clone $value);
			}
		}
		return $this;
	}

} 