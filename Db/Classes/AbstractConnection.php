<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 14:44
 */

namespace Db\Classes;


abstract class AbstractConnection {

	public function get_model_reflection($classname)
	{
		$reflection = new \Model\Classes\Reflection($classname, $this);

		return $reflection;
	}
}