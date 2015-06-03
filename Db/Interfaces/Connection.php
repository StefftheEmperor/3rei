<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 24.05.15
 * Time: 14:40
 */

namespace Db\Interfaces;


interface Connection {
	public function get_model_reflection($classname);
}