<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 14.12.14
 * Time: 18:15
 */

namespace Db\Mysql;


class Expression {

	private $value = NULL;
	public function __construct($value)
	{
		$this->value = $value;
	}

	public static function factory($value)
	{
		return new static($value);
	}

	public function __toString()
	{
		if ($this->value === NULL)
		{
			return 'NULL';
		} elseif (is_numeric($this->value))
		{
			return $this->value;
		}
		else {
			return '"'.$this->value.'"';
		}
	}
}