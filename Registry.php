<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 03.08.14
 * Time: 17:27
 */

class Registry extends \AbstractModel {
	private static $instance = NULL;

	public static function getinstance()
	{
		if (static::$instance === NULL)
		{
			static::$instance = new static;
		}

		return static::$instance;
	}

    public static function get($key)
    {
        return static::getinstance()->offsetGet($key);
    }

	public static function set($key, $value)
	{
		return static::getinstance()->offsetSet($key, $value);
	}
} 