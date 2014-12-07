<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 03.08.14
 * Time: 17:31
 */

abstract class AbstractModel implements ArrayAccess, Iterator {
	private $key;
	private $keys = array();
	private $values = array();
	private $changed_keys = array();
	private $available_keys = array();
	private $is_new = TRUE;
	public function key()
	{
		return $this->keys[$this->key];
	}

	public function next()
	{
		$this->key++;
	}

	public function valid()
	{
		return array_key_exists($this->key, $this->keys);
	}

	public function getKeyByOffset($offset)
	{
		$key = array_search($offset, $this->keys, TRUE);

		return $key;
	}

	public function offsetGet($offset)
	{

		$key = $this->getKeyByOffset($offset);
		if (isset($this->values[$key])) {
			return $this->values[$key];
		} else {

			if (in_array($offset, $this->available_keys))
			{
				return NULL;
			} else {
				throw new \CustomException('Undefined offset: ' . get_called_class() . '[' . $offset . ']');
			}
		}

	}

	public function current()
	{
		return $this->values[$this->key];
	}

	public function rewind()
	{
		$this->key = 0;
	}

	public function offsetUnset($offset)
	{

		$key = $this->getKeyByOffset($offset);
		if ($key !== FALSE) {
			$this->changed_keys[] = $key;
			unset($this->keys[$key]);
			unset($this->values[$key]);
		}
	}

	public function offsetExists($offset)
	{
		$key = $this->getKeyByOffset($offset);
		return $key !== FALSE;
	}

	public function offsetSet($offset, $value)
	{

		$key = $this->getKeyByOffset($offset);

		if ( ! isset($value) AND $key !== FALSE) {
			$this->offsetUnset($offset);
		} elseif (isset($value)) {
			if ($key !== FALSE) {
				$this->values[$key] = $value;
			} else {

				$this->keys[] = $offset;
				$this->values[] = $value;
			}
			$this->changed_keys[] = $key;
		}

		return $this;
	}

	public function __get($offset)
	{
		return $this->offsetGet($offset);
	}

	public function __set($offset, $value)
	{
		return $this->offsetSet($offset, $value);
	}

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
			throw new Exception('Call to undefined method: '.get_called_class().'::'.$method);
		}
	}
	public function __unset($offset)
	{
		return $this->offsetUnset($offset);
	}

	public function __isset($offset)
	{
		$offset_exists = $this->offsetExists($offset);

		return $offset_exists;
	}

	public function add($value)
	{
		$this->values[] = $value;
		$this->keys[] = $this->get_next_numeric_key();
	}

	public function add_available_key($key)
	{
		if (array_search($key, $this->available_keys) === FALSE)
		{
			$this->available_keys[] = $key;
		}
	}

	public function get_available_keys()
	{
		return $this->available_keys;
	}
} 