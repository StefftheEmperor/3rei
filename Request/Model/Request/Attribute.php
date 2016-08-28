<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 31.07.16
 * Time: 15:56
 */

namespace Request\Model\Request;


use Model\Classes\AbstractModel;

class Attribute extends AbstractModel
{

	public static function factory($key, $value)
	{
		$instance = new static;
		$instance->offsetSet('key', $key);
		$instance->offsetSet('value', $value);

		return $instance;
	}
}