<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 17:28
 */

namespace Structure\Classes;


abstract class AbstractValueStructure
	extends \Structure\Classes\AbstractStructure
	implements \Structure\Interfaces\Value

{
	protected $value;

	protected $key;

	public function get_value()
	{
		return $this->value;
	}

	public function set_value($value)
	{
		$this->value = $value;

		return $this;
	}

	public function get_key()
	{
		return $this->key;
	}

	public function set_key($key)
	{
		$this->key = $key;

		return $this;
	}

	public function init($name, $value = NULL)
	{
		$this->value = $value;
		$this->set_key($name);
	}

	public function get_name()
	{
		return $this->get_key();
	}

	public function __get($key)
	{
		if ($key == 'name')
		{
			$attribute = $this->get_key();
		} else {
			$attribute = parent::__get($key);
		}

		return $attribute;
	}

	public function validate(\Request\Classes\Request\Post $post)
	{
		if (($post->offsetExists($this->get_key())))
		{
			$this->set_value($post[$this->get_key()]);
		}
	}

	public function get_value_of($field)
	{
		$value = NULL;
		if ($this->get_key() === $field)
		{
			$value = $this->get_value();
		}

		return $value;
	}
}