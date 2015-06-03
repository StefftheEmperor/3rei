<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 13:39
 */

namespace Model\Classes;


class Reflection
{
	protected $classname = NULL;
	protected $reflection = NULL;
	protected $connection = NULL;
	public function __construct($classname, $connection)
	{
		$this->classname = $classname;
		$this->connection = $connection;
	}

	protected function get_reflection()
	{
		if ( ! isset($this->reflection))
		{
			$this->reflection = new \ReflectionClass($this->classname);
		}

		return $this->reflection;
	}

	public function __call($method, $arguments)
	{
		$classname = $this->get_reflection()->getName();

		array_unshift($arguments, $this->connection);
		$model = call_user_func_array($classname.'::'.$method, $arguments);

		return $model;
	}
}