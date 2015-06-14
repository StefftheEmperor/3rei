<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 13:21
 */

namespace Structure\Classes;


use Debug\Classes\CustomException;

class AbstractStructure  implements \Request\Interfaces\Renderable
{

	protected $renderer = NULL;
	protected $attributes = NULL;

	final public function __construct()
	{
		$this->attributes = new \Structure\Classes\Attributes;

		$reflection = new \ReflectionClass($this);

		if (method_exists($this, 'init')) {
			$init_reflection = $reflection->getMethod('init');
			if (func_num_args() < $init_reflection->getNumberOfRequiredParameters())
			{
				throw new CustomException('Cannot init '.get_called_class().' - needs '.$init_reflection->getNumberOfRequiredParameters().' params but only got '.func_num_args());
			}
			call_user_func_array(array($this, 'init'), func_get_args());
		}
	}

	public function get_renderer()
	{
		return $this->renderer;
	}

	public function set_renderer($renderer)
	{
		$this->renderer = $renderer;
		return $this;
	}

	/**
	 * @return static
	 */
	public static function factory()
	{
		$reflection_class = new \ReflectionClass(get_called_class());
		return $reflection_class->newInstanceArgs(func_get_args());
	}

	public function get_attributes()
	{
		return $this->attributes;
	}

	public function get_attributes_html()
	{
		$attributes = '';

		foreach ($this->get_attributes() as $attribute_name => $attribute_value)
		{
			if ($attribute_value instanceof \Request\Classes\Url)
			{
				$attribute_value = $attribute_value->get_absolute_url();
			}
			$attribute_name_value_pair = $attribute_name.'="'.$attribute_value.'"';
			$attributes .= (' '.$attribute_name_value_pair);
		}

		return $attributes;
	}

	public function set_attribute($offset, $value)
	{
		$this->attributes[$offset] = $value;
	}

	public function __call($name, $args)
	{
		if (substr($name, 0, 4) == 'set_')
		{
			$this->attributes->__call($name, $args);
			return $this;
		} else {
			return $this->attributes->__call($name, $args);
		}
	}

	public function __get($offset)
	{
		return $this->attributes->__get($offset);
	}

	public function __set($offset, $value)
	{
		$this->attributes->__set($offset, $value);

		return $this;
	}
}