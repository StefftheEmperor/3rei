<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 16.11.14
 * Time: 17:44
 */

namespace Db;

class Result extends \AbstractModel {

	public function map_to($classname)
	{
		$reflection_class = new \ReflectionClass($classname);

		$instance = $reflection_class->newInstance();

		foreach ($this as $key => $value)
		{
			$instance->{'set_'.strtolower($key)}($value);
		}

		foreach ($this->get_available_keys() as $key)
		{
			$instance->add_available_key($key);
		}
		return $instance;
	}
} 