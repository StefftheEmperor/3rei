<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 25.01.15
 * Time: 15:14
 */

namespace Db\Classes\Expression;


class AbstractExpression {

	protected $unfiltered;

	public function __construct($expression)
	{
		return $this->unfiltered = $expression;
	}

	public static function factory($expression)
	{
		return new static($expression);
	}

	public function get_unfiltered()
	{
		return $this->unfiltered;
	}

	public function get_filtered()
	{
		return $this->get_unfiltered();
	}
	public function map_to($expression_class)
	{
		$expression_reflection_class = new \ReflectionClass($expression_class);
		return call_user_func(array($expression_class, 'factory'), $this->get_unfiltered());
	}

	public function map_to_namespace($namespace)
	{
		$expression_reflection_class = new \ReflectionClass($this);

		if (substr($namespace, -1) !== '\\')
		{
			$namespace .= '\\';
		}
		$class_name_segments = explode('\\', $expression_reflection_class->getName());
		$class_name = end($class_name_segments);

		$new_class = $namespace.$class_name;
		return call_user_func(array($new_class, 'factory'), $this->get_unfiltered());
	}
}