<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 17.08.14
 * Time: 20:39
 */
namespace Config\Classes;
class AbstractConfig extends \Model\Classes\AbstractModel {

	public function __construct($data = NULL)
	{
		if ( ! isset($data)) {
			$this->load();
		} elseif (is_array($data)) {
			$this->load_array($data);
		}

	}

	public function load_array($array)
	{
		foreach ($array as $key => $value) {

			$this->offsetSet($key, $value);
		}
	}

	public function load() {

	}
} 