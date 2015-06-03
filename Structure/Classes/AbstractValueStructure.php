<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 28.05.15
 * Time: 17:28
 */

namespace Structure\Classes;


abstract class AbstractValueStructure extends \Structure\Classes\AbstractStructure
{
	protected $value;

	public function get_value()
	{
		return $this->value;
	}

	public function init($name, $value = NULL)
	{
		$this->value = $value;
		$this->set_attribute('name', $name);
	}
}