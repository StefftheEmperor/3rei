<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 03.08.14
 * Time: 17:27
 */
namespace Model\Classes;
class Registry extends \Model\Classes\AbstractModel {
	private static $instance = NULL;

	public static function get_instance()
	{
		if (static::$instance === NULL)
		{
			static::$instance = new static;
		}

		return static::$instance;
	}

    public static function get($key)
    {
        return static::get_instance()->offsetGet($key);
    }

	public static function set($key, $value)
	{
		return static::get_instance()->offsetSet($key, $value);
	}
} 